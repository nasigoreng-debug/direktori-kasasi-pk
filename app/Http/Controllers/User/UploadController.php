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
     * Display a listing of user's uploads
     */
    public function index(Request $request)
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
        return view('user.uploads.index', compact('uploads'));
    }

    /**
     * Menampilkan form untuk membuat upload baru
     */
    public function create()
    {
        $pengadilans = Pengadilan::orderBy('kode')->get();
        return view('user.uploads.create', compact('pengadilans'));
    }

    /**
     * Menyimpan upload baru ke database dan storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pengadilan_id' => 'required|exists:pengadilan,id',
            'jenis_putusan' => 'required|in:kasasi,pk',
            'nomor_perkara_pa' => 'required|string|max:100',
            'nomor_perkara_banding' => 'nullable|string|max:100',
            'nomor_perkara_kasasi' => 'nullable|string|max:100',
            'nomor_perkara_pk' => 'nullable|string|max:100',
            'tanggal_putusan' => 'required|date',
            'file_putusan' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file_putusan')) {
            $file = $request->file('file_putusan');

            if (!$file->isValid()) {
                return back()->withErrors(['file_putusan' => 'File tidak valid.'])->withInput();
            }

            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $filename = 'putusan_' . time() . '_' . auth()->id() . '_' .
                preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $originalName);

            $uploadDir = storage_path('app/public/uploads');

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                Log::info('Created upload directory: ' . $uploadDir);
            }

            if (!is_writable($uploadDir)) {
                Log::warning('Directory not writable: ' . $uploadDir);
                return back()->withErrors(['file_putusan' => 'Folder upload tidak memiliki izin tulis.'])->withInput();
            }

            $destination = $uploadDir . DIRECTORY_SEPARATOR . $filename;

            try {
                $moved = $file->move($uploadDir, $filename);

                if (!$moved) {
                    Log::warning('move() failed, trying rename()...');
                    $tempPath = $file->getRealPath();
                    if (rename($tempPath, $destination)) {
                        $moved = true;
                    } else {
                        Log::warning('rename() failed, trying copy()...');
                        if (copy($tempPath, $destination)) {
                            $moved = true;
                            unlink($tempPath);
                        }
                    }
                }

                if (!$moved) {
                    throw new \Exception('Gagal memindahkan file ke server');
                }

                if (!file_exists($destination)) {
                    throw new \Exception('File tidak ditemukan setelah dipindahkan');
                }

                $filePath = 'uploads/' . $filename;

                Log::info('File uploaded successfully:', [
                    'path' => $filePath,
                    'size' => filesize($destination)
                ]);
            } catch (\Exception $e) {
                Log::error('Upload error: ' . $e->getMessage());
                return back()->withErrors(['file_putusan' => 'Gagal menyimpan file: ' . $e->getMessage()])->withInput();
            }

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

            return redirect()->route('user.uploads.index')
                ->with('success', 'Putusan berhasil diupload!');
        }

        return back()->withErrors(['file_putusan' => 'Tidak ada file yang diunggah.'])->withInput();
    }

    /**
     * Display the specified upload - Menggunakan Route Model Binding
     */
    public function show(Upload $upload)
    {
        // Pastikan hanya pemilik yang bisa melihat
        if ($upload->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $upload->load(['pengadilan', 'user']);
        return view('user.uploads.show', compact('upload'));
    }

    /**
     * Show the form for editing the specified upload - Menggunakan Route Model Binding
     */
    public function edit(Upload $upload)
    {
        // Pastikan hanya pemilik yang bisa edit
        if ($upload->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek status - jika sudah verified tidak bisa diedit
        if ($upload->status === 'verified') {
            return redirect()->route('user.uploads.index')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat diedit.');
        }

        $pengadilans = Pengadilan::orderBy('kode')->get();
        return view('user.uploads.edit', compact('upload', 'pengadilans'));
    }

    /**
     * Update the specified upload - Menggunakan Route Model Binding
     */
    public function update(Request $request, Upload $upload)
    {
        // Pastikan hanya pemilik yang bisa update
        if ($upload->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Cek status
        if ($upload->status === 'verified') {
            return redirect()->route('user.uploads.index')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat diedit.');
        }

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
        Log::info('Upload ID: ' . $upload->id);

        $updateData = [
            'pengadilan_id' => $request->pengadilan_id,
            'jenis_putusan' => $request->jenis_putusan,
            'nomor_perkara_pa' => $request->nomor_perkara_pa,
            'nomor_perkara_banding' => $request->nomor_perkara_banding,
            'nomor_perkara_kasasi' => $request->nomor_perkara_kasasi,
            'nomor_perkara_pk' => $request->nomor_perkara_pk,
            'tanggal_putusan' => $request->tanggal_putusan,
            'status' => 'submitted',
        ];

        // Proses file baru jika ada
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

            // Hapus file lama
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

            // Upload file baru
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
                $moved = $file->move($uploadDir, $filename);

                if (!$moved) {
                    $tempPath = $file->getRealPath();
                    if (!rename($tempPath, $destination)) {
                        if (!copy($tempPath, $destination)) {
                            throw new \Exception('Gagal memindahkan file baru');
                        }
                        unlink($tempPath);
                    }
                }

                if (!file_exists($destination)) {
                    throw new \Exception('File baru tidak ditemukan');
                }

                $newFilePath = 'uploads/' . $filename;

                Log::info('New file uploaded successfully:', [
                    'path' => $newFilePath
                ]);

                $updateData['file_path'] = $newFilePath;
                $updateData['original_filename'] = $originalName;
                $updateData['file_size'] = $fileSize;
            } catch (\Exception $e) {
                Log::error('New file upload error: ' . $e->getMessage());
                return back()->withErrors(['file_putusan' => 'Gagal menyimpan file baru.'])->withInput();
            }
        }

        // Update database
        try {
            Log::info('Updating database');
            $upload->update($updateData);
            Log::info('Database update SUCCESS');
        } catch (\Exception $e) {
            Log::error('Database update error: ' . $e->getMessage());

            if (isset($newFilePath) && isset($destination) && file_exists($destination)) {
                unlink($destination);
                Log::info('Rollback: deleted new file after database error');
            }

            return back()
                ->withErrors(['error' => 'Gagal memperbarui data.'])
                ->withInput();
        }

        Log::info('=== UPDATE PROCESS END ===');

        return redirect()->route('user.uploads.index')
            ->with('success', 'Putusan berhasil diperbarui.');
    }

    /**
     * Remove the specified upload (soft delete) - Menggunakan Route Model Binding
     */
    public function destroy(Upload $upload)
    {
        // Pastikan hanya pemilik yang bisa delete
        if ($upload->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($upload->status === 'verified') {
            return redirect()->route('user.uploads.index')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat dihapus.');
        }

        $upload->delete();

        return redirect()->route('user.uploads.index')
            ->with('success', 'Putusan berhasil dipindahkan ke trash.');
    }

    /**
     * Download file - Menggunakan Route Model Binding
     */
    public function download(Upload $upload)
    {
        // Pastikan hanya pemilik yang bisa download
        if ($upload->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($upload->file_path)) {
            Log::error('File not found: ' . $upload->file_path);

            $possiblePaths = [
                storage_path('app/public/' . $upload->file_path),
                public_path('storage/' . $upload->file_path),
                storage_path('app/public/uploads/' . basename($upload->file_path)),
            ];

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

        $filePath = storage_path('app/public/' . $upload->file_path);
        return response()->download($filePath, $upload->original_filename);
    }

    /**
     * Preview file - Menggunakan Route Model Binding
     */
    public function preview(Upload $upload)
    {
        // Pastikan hanya pemilik yang bisa preview
        if ($upload->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!Storage::disk('public')->exists($upload->file_path)) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $upload->file_path);

        if (strtolower(pathinfo($upload->file_path, PATHINFO_EXTENSION)) === 'pdf') {
            return response()->file($filePath);
        }

        return view('user.uploads.preview', compact('upload'));
    }

    /**
     * Menampilkan riwayat upload user (alias dari index)
     */
    public function history(Request $request)
    {
        return $this->index($request);
    }

    /**
     * Menampilkan daftar upload yang dihapus (trash)
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
        return view('user.uploads.trash', compact('uploads')); // <-- TAMBAHKAN .index
    }

    /**
     * Memulihkan upload dari trash
     */
    public function restore($id)
    {
        $upload = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $upload->restore();

        return redirect()->route('user.uploads.trash.index')
            ->with('success', 'Putusan berhasil dipulihkan.');
    }

    /**
     * Menghapus permanen upload dari trash
     */
    public function forceDelete($id)
    {
        $upload = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->forceDelete();

        return redirect()->route('user.uploads.trash.index')
            ->with('success', 'Putusan berhasil dihapus permanen.');
    }

    /**
     * Mengosongkan semua isi trash
     */
    public function emptyTrash()
    {
        $uploads = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->get();

        $deletedCount = 0;

        foreach ($uploads as $upload) {
            if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
                Storage::disk('public')->delete($upload->file_path);
            }

            $upload->forceDelete();
            $deletedCount++;
        }

        return redirect()->route('user.uploads.trash.index')
            ->with('success', $deletedCount . ' putusan di trash berhasil dihapus permanen.');
    }
}
