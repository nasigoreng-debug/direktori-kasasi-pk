<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\Pengadilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Show the form for creating a new upload
     */
    public function create()
    {
        $pengadilans = Pengadilan::orderBy('kode')->get();
        return view('user.upload.create', compact('pengadilans'));
    }

    /**
     * Store a newly created upload
     */
    public function store(Request $request)
    {
        $request->validate([
            'pengadilan_id' => 'required|exists:pengadilan,id',
            'jenis_putusan' => 'required|in:kasasi,pk',
            'nomor_perkara_pa' => 'required|string|max:100',
            'nomor_perkara_banding' => 'nullable|string|max:100',
            'nomor_perkara_kasasi' => 'nullable|string|max:100',
            'nomor_perkara_pk' => 'nullable|string|max:100',
            'tanggal_putusan' => 'required|date',
            'file_putusan' => 'required|file|mimes:pdf|max:10240', // max 10MB
        ]);

        // Upload file
        if ($request->hasFile('file_putusan')) {
            $file = $request->file('file_putusan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');
            $fileSize = $file->getSize();
            $originalFilename = $file->getClientOriginalName();
        }

        // Simpan data
        $upload = Upload::create([
            'user_id' => auth()->id(),
            'pengadilan_id' => $request->pengadilan_id,
            'jenis_putusan' => $request->jenis_putusan,
            'nomor_perkara_pa' => $request->nomor_perkara_pa,
            'nomor_perkara_banding' => $request->nomor_perkara_banding,
            'nomor_perkara_kasasi' => $request->nomor_perkara_kasasi,
            'nomor_perkara_pk' => $request->nomor_perkara_pk,
            'tanggal_putusan' => $request->tanggal_putusan,
            'file_path' => $path ?? null,
            'original_filename' => $originalFilename ?? null,
            'file_size' => $fileSize ?? 0,
            'status' => 'submitted',
        ]);

        return redirect()->route('user.upload.history')
            ->with('success', 'Putusan berhasil diupload. Menunggu verifikasi admin.');
    }

    /**
     * Display upload history
     */
    public function history(Request $request)
    {
        $query = Upload::where('user_id', auth()->id())
            ->with('pengadilan')
            ->orderBy('created_at', 'desc');

        // Filter
        if ($request->filled('jenis_putusan')) {
            $query->where('jenis_putusan', $request->jenis_putusan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $uploads = $query->paginate(15);

        return view('user.upload.history', compact('uploads'));
    }

    /**
     * Download file
     */
    public function download($id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);
        $path = storage_path('app/public/' . $upload->file_path);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($path, $upload->original_filename);
    }

    /**
     * Preview file
     */
    public function preview($id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        if (pathinfo($upload->file_path, PATHINFO_EXTENSION) === 'pdf') {
            return response()->file(storage_path('app/public/' . $upload->file_path));
        }

        return view('user.upload.preview', compact('upload'));
    }

    /**
     * Show the form for editing an upload
     */
    public function edit($id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        // Cek jika sudah diverifikasi/tidak bisa edit
        if ($upload->status === 'verified') {
            return redirect()->route('user.upload.history')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat diedit.');
        }

        $pengadilans = Pengadilan::orderBy('kode')->get();
        return view('user.upload.edit', compact('upload', 'pengadilans'));
    }

    /**
     * Update the specified upload
     */
    public function update(Request $request, $id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        // Cek jika sudah diverifikasi/tidak bisa edit
        if ($upload->status === 'verified') {
            return redirect()->route('user.upload.history')
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

        $updateData = [
            'pengadilan_id' => $request->pengadilan_id,
            'jenis_putusan' => $request->jenis_putusan,
            'nomor_perkara_pa' => $request->nomor_perkara_pa,
            'nomor_perkara_banding' => $request->nomor_perkara_banding,
            'nomor_perkara_kasasi' => $request->nomor_perkara_kasasi,
            'nomor_perkara_pk' => $request->nomor_perkara_pk,
            'tanggal_putusan' => $request->tanggal_putusan,
            'status' => 'submitted', // Reset status ke submitted setelah edit
        ];

        // Update file jika ada file baru
        if ($request->hasFile('file_putusan')) {
            // Hapus file lama
            if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
                Storage::disk('public')->delete($upload->file_path);
            }

            $file = $request->file('file_putusan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');

            $updateData['file_path'] = $path;
            $updateData['original_filename'] = $file->getClientOriginalName();
            $updateData['file_size'] = $file->getSize();
        }

        $upload->update($updateData);

        return redirect()->route('user.upload.history')
            ->with('success', 'Putusan berhasil diperbarui.');
    }

    /**
     * Remove the specified upload (SOFT DELETE ke trash)
     */
    public function destroy($id)
    {
        $upload = Upload::where('user_id', auth()->id())->findOrFail($id);

        // Cek jika sudah diverifikasi/tidak bisa hapus
        if ($upload->status === 'verified') {
            return redirect()->route('user.upload.history')
                ->with('error', 'Putusan yang sudah diverifikasi tidak dapat dihapus.');
        }

        // Jangan hapus file fisik di sini (hanya soft delete)
        // File akan dihapus permanen saat forceDelete()

        $upload->delete(); // Ini soft delete (menambahkan deleted_at)

        return redirect()->route('user.upload.history')
            ->with('success', 'Putusan berhasil dipindahkan ke trash.');
    }

    /**
     * Display trash (soft deleted uploads)
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

        return view('user.trash.index', compact('uploads'));
    }

    /**
     * Restore soft deleted upload
     */
    public function restore($id)
    {
        $upload = Upload::onlyTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $upload->restore();

        return redirect()->route('user.upload.trash.index')
            ->with('success', 'Putusan berhasil dipulihkan.');
    }

    /**
     * Permanently delete upload
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

        $upload->forceDelete();

        return redirect()->route('user.upload.trash.index')
            ->with('success', 'Putusan berhasil dihapus permanen.');
    }

    /**
     * Empty trash (delete all)
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
            $upload->forceDelete();
            $deletedCount++;
        }

        return redirect()->route('user.upload.trash.index')
            ->with('success', $deletedCount . ' putusan di trash berhasil dihapus permanen.');
    }
}
