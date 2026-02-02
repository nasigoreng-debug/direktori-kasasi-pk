<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\Pengadilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    /**
     * Menampilkan form untuk membuat upload baru
     * 
     * Method ini menampilkan form upload dokumen putusan
     * dengan daftar pengadilan untuk dipilih
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Ambil semua data pengadilan untuk dropdown
        $pengadilans = Pengadilan::orderBy('kode')->get();
        return view('user.upload.create', compact('pengadilans'));
    }

    /**
     * Menyimpan upload baru ke database dan storage
     * 
     * Method ini menangani upload file putusan dengan:
     * 1. Validasi input dan file
     * 2. Upload file ke storage dengan multiple fallback methods
     * 3. Simpan metadata ke database
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ============================================
        // VALIDASI INPUT FORM
        // ============================================
        $validated = $request->validate([
            'pengadilan_id' => 'required|exists:pengadilan,id',
            'jenis_putusan' => 'required|in:kasasi,pk',
            'nomor_perkara_pa' => 'required|string|max:100',
            'nomor_perkara_banding' => 'nullable|string|max:100',
            'nomor_perkara_kasasi' => 'nullable|string|max:100',
            'nomor_perkara_pk' => 'nullable|string|max:100',
            'tanggal_putusan' => 'required|date',
            'file_putusan' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        // ============================================
        // PROSES UPLOAD FILE
        // ============================================
        if ($request->hasFile('file_putusan')) {
            $file = $request->file('file_putusan');

            // Validasi file
            if (!$file->isValid()) {
                return back()->withErrors(['file_putusan' => 'File tidak valid.'])->withInput();
            }

            // Data file
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Generate nama file yang aman
            $filename = 'putusan_' . time() . '_' . auth()->id() . '_' .
                preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $originalName);

            // ============================================
            // UPLOAD FILE DENGAN FALLBACK METHODS
            // ============================================
            $uploadDir = storage_path('app/public/uploads');

            // Pastikan folder upload ada
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                Log::info('Created upload directory: ' . $uploadDir);
            }

            // Cek permission folder
            if (!is_writable($uploadDir)) {
                Log::warning('Directory not writable: ' . $uploadDir);
                return back()->withErrors(['file_putusan' => 'Folder upload tidak memiliki izin tulis.'])->withInput();
            }

            // Path lengkap tujuan
            $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

            // Coba upload dengan berbagai metode
            try {
                // Method 1: move()
                $moved = $file->move($uploadDir, $filename);

                if (!$moved) {
                    // Method 2: rename()
                    Log::warning('move() failed, trying rename()...');
                    $tempPath = $file->getRealPath();
                    if (rename($tempPath, $destination)) {
                        $moved = true;
                    } else {
                        // Method 3: copy()
                        Log::warning('rename() failed, trying copy()...');
                        if (copy($tempPath, $destination)) {
                            $moved = true;
                            unlink($tempPath); // Hapus file temp
                        }
                    }
                }

                // Cek jika semua method gagal
                if (!$moved) {
                    throw new \Exception('Gagal memindahkan file ke server');
                }

                // Verifikasi file berhasil disimpan
                if (!file_exists($destination)) {
                    throw new \Exception('File tidak ditemukan setelah dipindahkan');
                }

                // Path untuk database
                $filePath = 'uploads/' . $filename;

                Log::info('File uploaded successfully:', [
                    'path' => $filePath,
                    'size' => filesize($destination)
                ]);
            } catch (\Exception $e) {
                Log::error('Upload error: ' . $e->getMessage());
                return back()->withErrors(['file_putusan' => 'Gagal menyimpan file: ' . $e->getMessage()])->withInput();
            }

            // ============================================
            // SIMPAN KE DATABASE
            // ============================================
            $upload = Upload::create([
                'user_id' => auth()->id(),
                'pengadilan_id' => $request->pengadilan_id,
                'jenis_putusan' => $request->jenis_putusan,
                'nomor_perkara_pa' => $request->nomor_perkara_pa,
                'nomor_perkara_banding' => $request->nomor_perkara_banding,
                'nomor_perkara_kasasi' => $request->nomor_perkara_kasasi,
                'nomor_perkara_pk' => $request->nomor_perkara_pk,
                'tanggal_putusan' => $request->tanggal_putusan,
                'file_path' => $filePath,
                'original_filename' => $originalName,
                'file_size' => $fileSize,
                'status' => 'submitted',
            ]);

            return redirect()->route('user.upload.history')
                ->with('success', 'Putusan berhasil diupload!');
        }

        return back()->withErrors(['file_putusan' => 'Tidak ada file yang diunggah.'])->withInput();
    }

    /**
     * Menampilkan riwayat upload user
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function history(Request $request)
    {
        $query = Upload::where('user_id', auth()->id())
            ->with('pengadilan')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan jenis putusan
        if ($request->filled('jenis_putusan')) {
            $query->where('jenis_putusan', $request->jenis_putusan);
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $uploads = $query->paginate(15);
        return view('user.upload.history', compact('uploads'));
    }

    /**
     * Mengunduh file putusan
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function download($id)
    {
        // Validasi kepemilikan
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        // Cek file di storage
        if (!Storage::disk('public')->exists($upload->file_path)) {
            Log::error('File not found: ' . $upload->file_path);

            // Coba cari di beberapa lokasi yang mungkin
            $possiblePaths = [
                storage_path('app/public/' . $upload->file_path),
                public_path('storage/' . $upload->file_path),
                storage_path('app/public/uploads/' . basename($upload->file_path)),
            ];

            // Cari file di lokasi alternatif
            $foundPath = null;
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $foundPath = $path;
                    break;
                }
            }

            if (!$foundPath) {
                return back()->with('error', 'File tidak ditemukan di server.');
            }

            return response()->download($foundPath, $upload->original_filename);
        }

        // Download file
        $filePath = storage_path('app/public/' . $upload->file_path);
        return response()->download($filePath, $upload->original_filename);
    }

    /**
     * Melihat preview file putusan
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function preview($id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        if (!Storage::disk('public')->exists($upload->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $upload->file_path);

        // Jika file PDF, tampilkan inline
        if (strtolower(pathinfo($upload->file_path, PATHINFO_EXTENSION)) === 'pdf') {
            return response()->file($filePath);
        }

        return view('user.upload.preview', compact('upload'));
    }

    /**
     * Menampilkan form untuk mengedit upload
     * 
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        // Cek status - jika sudah verified tidak bisa diedit
        if ($upload->status === 'verified') {
            return redirect()->route('user.upload.history')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat diedit.');
        }

        $pengadilans = Pengadilan::orderBy('kode')->get();
        return view('user.upload.edit', compact('upload', 'pengadilans'));
    }

    /**
     * Memperbarui data upload
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validasi kepemilikan
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        // Cek status
        if ($upload->status === 'verified') {
            return redirect()->route('user.upload.history')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat diedit.');
        }

        // Validasi input
        $request->validate([
            'pengadilan_id' => 'required|exists:pengadilan,id',
            'jenis_putusan' => 'required|in:kasasi,pk',
            'nomor_perkara_pa' => 'required|string|max:100',
            'nomor_perkara_banding' => 'nullable|string|max:100',
            'nomor_perkara_kasasi' => 'nullable|string|max:100',
            'nomor_perkara_pk' => 'nullable|string|max:100',
            'tanggal_putusan' => 'required|date',
            'file_putusan' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        Log::info('=== UPDATE PROCESS START ===');
        Log::info('Upload ID: ' . $id);

        // Data untuk update
        $updateData = [
            'pengadilan_id' => $request->pengadilan_id,
            'jenis_putusan' => $request->jenis_putusan,
            'nomor_perkara_pa' => $request->nomor_perkara_pa,
            'nomor_perkara_banding' => $request->nomor_perkara_banding,
            'nomor_perkara_kasasi' => $request->nomor_perkara_kasasi,
            'nomor_perkara_pk' => $request->nomor_perkara_pk,
            'tanggal_putusan' => $request->tanggal_putusan,
            'status' => 'submitted', // Reset status setelah edit
        ];

        // ============================================
        // PROSES FILE BARU JIKA ADA
        // ============================================
        if ($request->hasFile('file_putusan')) {
            $file = $request->file('file_putusan');

            if (!$file->isValid()) {
                Log::error('New file invalid');
                return back()->withErrors(['file_putusan' => 'File tidak valid.'])->withInput();
            }

            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $filename = 'putusan_edit_' . time() . '_' . auth()->id() . '_' .
                preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $originalName);

            Log::info('New file info:', [
                'original' => $originalName,
                'new_name' => $filename,
                'size' => $fileSize
            ]);

            // ============================================
            // HAPUS FILE LAMA
            // ============================================
            if ($upload->file_path) {
                try {
                    $oldFilePath = storage_path('app/public/' . $upload->file_path);
                    Log::info('Attempting to delete old file: ' . $oldFilePath);

                    if (file_exists($oldFilePath)) {
                        if (unlink($oldFilePath)) {
                            Log::info('Old file deleted successfully');
                        } else {
                            Log::warning('Failed to delete old file');
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting old file: ' . $e->getMessage());
                }
            }

            // ============================================
            // UPLOAD FILE BARU
            // ============================================
            $uploadDir = storage_path('app/public/uploads');

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                Log::info('Created upload directory');
            }

            if (!is_writable($uploadDir)) {
                Log::error('Directory not writable');
                return back()->withErrors(['file_putusan' => 'Folder upload tidak memiliki izin tulis.'])->withInput();
            }

            $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

            try {
                // Upload dengan fallback methods
                $moved = $file->move($uploadDir, $filename);

                if (!$moved) {
                    // Fallback methods
                    $tempPath = $file->getRealPath();
                    if (!rename($tempPath, $destination)) {
                        if (!copy($tempPath, $destination)) {
                            throw new \Exception('Gagal memindahkan file baru');
                        }
                        unlink($tempPath);
                    }
                }

                // Verifikasi
                if (!file_exists($destination)) {
                    throw new \Exception('File baru tidak ditemukan');
                }

                $newFilePath = 'uploads/' . $filename;

                Log::info('New file uploaded successfully:', [
                    'path' => $newFilePath
                ]);

                // Tambahkan data file ke updateData
                $updateData['file_path'] = $newFilePath;
                $updateData['original_filename'] = $originalName;
                $updateData['file_size'] = $fileSize;
            } catch (\Exception $e) {
                Log::error('New file upload error: ' . $e->getMessage());
                return back()->withErrors(['file_putusan' => 'Gagal menyimpan file baru.'])->withInput();
            }
        }

        // ============================================
        // UPDATE DATABASE
        // ============================================
        try {
            Log::info('Updating database');
            $upload->update($updateData);
            Log::info('Database update SUCCESS');
        } catch (\Exception $e) {
            Log::error('Database update error: ' . $e->getMessage());

            // Rollback: Hapus file baru jika gagal update database
            if (isset($newFilePath) && isset($destination) && file_exists($destination)) {
                unlink($destination);
                Log::info('Rollback: deleted new file after database error');
            }

            return back()
                ->withErrors(['error' => 'Gagal memperbarui data.'])
                ->withInput();
        }

        Log::info('=== UPDATE PROCESS END ===');

        return redirect()->route('user.upload.history')
            ->with('success', 'Putusan berhasil diperbarui.');
    }

    /**
     * Menghapus upload (soft delete)
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        if ($upload->status === 'verified') {
            return redirect()->route('user.upload.history')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat dihapus.');
        }

        $upload->delete();

        return redirect()->route('user.upload.history')
            ->with('success', 'Putusan berhasil dipindahkan ke trash.');
    }

    /**
     * Menampilkan daftar upload yang dihapus (trash)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function trashIndex(Request $request)
    {
        $query = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->with('pengadilan')
            ->orderBy('deleted_at', 'desc');

        if ($request->filled('jenis_putusan')) {
            $query->where('jenis_putusan', $request->jenis_putusan);
        }

        $uploads = $query->paginate(15);

        return view('user.upload.trash.index', compact('uploads'));
    }

    /**
     * Memulihkan upload dari trash
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $upload = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $upload->restore();

        return redirect()->route('user.upload.trash')
            ->with('success', 'Putusan berhasil dipulihkan.');
    }

    /**
     * Menghapus permanen upload dari trash
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        $upload = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Hapus file fisik
        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        // Hapus dari database
        $upload->forceDelete();

        return redirect()->route('user.upload.trash.index')
            ->with('success', 'Putusan berhasil dihapus permanen.');
    }

    /**
     * Mengosongkan semua isi trash
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emptyTrash()
    {
        $uploads = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->get();

        $deletedCount = 0;

        foreach ($uploads as $upload) {
            // Hapus file fisik
            if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
                Storage::disk('public')->delete($upload->file_path);
            }

            // Hapus dari database
            $upload->forceDelete();
            $deletedCount++;
        }

        return redirect()->route('user.upload.trash')
            ->with('success', $deletedCount . ' putusan di trash berhasil dihapus permanen.');
    }
}
