<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\Pengadilan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * Display a listing of all uploads (admin view)
     */
    public function index(Request $request)
    {
        // Ambil semua data untuk filter dropdown
        $pengadilans = Pengadilan::orderBy('kode')->get();
        $users = User::orderBy('name')->get();

        // Query dasar dengan eager loading (SEMUA upload)
        $query = Upload::with(['user', 'pengadilan']);

        // Filter: Search (nomor perkara)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_perkara_pa', 'like', "%{$search}%")
                    ->orWhere('nomor_perkara_kasasi', 'like', "%{$search}%")
                    ->orWhere('nomor_perkara_pk', 'like', "%{$search}%")
                    ->orWhere('original_filename', 'like', "%{$search}%");
            });
        }

        // Filter: Pengadilan
        if ($request->filled('pengadilan_id')) {
            $query->where('pengadilan_id', $request->pengadilan_id);
        }

        // Filter: User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter: Jenis Putusan
        if ($request->filled('jenis_putusan')) {
            $query->where('jenis_putusan', $request->jenis_putusan);
        }

        // Filter: Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Tanggal Putusan
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_putusan', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_putusan', '<=', $request->end_date);
        }

        // Pagination dengan 20 item per halaman
        $uploads = $query->latest()->paginate(20)->withQueryString();

        return view('admin.uploads.index', compact('uploads', 'pengadilans', 'users'));
    }

    /**
     * Show the form for creating a new upload (admin bisa upload untuk user lain)
     */
    public function create()
    {
        $pengadilans = Pengadilan::orderBy('kode')->get();
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.uploads.create', compact('pengadilans', 'users'));
    }

    /**
     * Store a newly created upload (admin)
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pengadilan_id' => 'required|exists:pengadilans,id',
            'jenis_putusan' => 'required|in:kasasi,pk',
            'nomor_perkara_pa' => 'required|string|max:100',
            'nomor_perkara_banding' => 'nullable|string|max:100',
            'nomor_perkara_kasasi' => 'nullable|string|max:100',
            'nomor_perkara_pk' => 'nullable|string|max:100',
            'tanggal_putusan' => 'required|date',
            'file_putusan' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:draft,submitted,verified,rejected',
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
            'user_id' => $request->user_id,
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
            'status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : null,
            'verified_by' => $request->status === 'verified' ? auth()->id() : null,
        ]);

        return redirect()->route('admin.uploads.index')
            ->with('success', 'Putusan berhasil ditambahkan.');
    }

    /**
     * Display the specified upload (admin view)
     */
    public function show($id)
    {
        $upload = Upload::with(['user', 'pengadilan'])->findOrFail($id);
        return view('admin.uploads.show', compact('upload'));
    }

    /**
     * Show the form for editing an upload (admin bisa edit semua)
     */
    public function edit($id)
    {
        $upload = Upload::findOrFail($id);
        $pengadilans = Pengadilan::orderBy('kode')->get();
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.uploads.edit', compact('upload', 'pengadilans', 'users'));
    }

    /**
     * Update the specified upload (admin)
     */
    public function update(Request $request, $id)
    {
        $upload = Upload::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'pengadilan_id' => 'required|exists:pengadilans,id',
            'jenis_putusan' => 'required|in:kasasi,pk',
            'nomor_perkara_pa' => 'required|string|max:100',
            'nomor_perkara_banding' => 'nullable|string|max:100',
            'nomor_perkara_kasasi' => 'nullable|string|max:100',
            'nomor_perkara_pk' => 'nullable|string|max:100',
            'tanggal_putusan' => 'required|date',
            'status' => 'required|in:draft,submitted,verified,rejected',
            'catatan' => 'nullable|string|max:500',
            'file_putusan' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $updateData = [
            'user_id' => $request->user_id,
            'pengadilan_id' => $request->pengadilan_id,
            'jenis_putusan' => $request->jenis_putusan,
            'nomor_perkara_pa' => $request->nomor_perkara_pa,
            'nomor_perkara_banding' => $request->nomor_perkara_banding,
            'nomor_perkara_kasasi' => $request->nomor_perkara_kasasi,
            'nomor_perkara_pk' => $request->nomor_perkara_pk,
            'tanggal_putusan' => $request->tanggal_putusan,
            'status' => $request->status,
            'catatan' => $request->catatan,
        ];

        // Update verified_at jika status verified
        if ($request->status === 'verified') {
            $updateData['verified_at'] = now();
            $updateData['verified_by'] = auth()->id();
        } else {
            $updateData['verified_at'] = null;
            $updateData['verified_by'] = null;
        }

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

        return redirect()->route('admin.uploads.index')
            ->with('success', 'Putusan berhasil diperbarui.');
    }

    /**
     * Remove the specified upload (admin - permanent delete)
     */
    public function destroy($id)
    {
        $upload = Upload::findOrFail($id);

        // Hapus file fisik
        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->forceDelete(); // Permanent delete (admin tidak pakai trash)

        return redirect()->route('admin.uploads.index')
            ->with('success', 'Putusan berhasil dihapus permanen.');
    }

    /**
     * Verify upload (admin)
     */
    public function verify($id)
    {
        $upload = Upload::findOrFail($id);
        $upload->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        return redirect()->back()
            ->with('success', 'Putusan berhasil diverifikasi.');
    }

    /**
     * Reject upload (admin)
     */
    public function reject($id)
    {
        $upload = Upload::findOrFail($id);
        $upload->update(['status' => 'rejected']);

        return redirect()->back()
            ->with('success', 'Putusan berhasil ditolak.');
    }

    /**
     * Update status upload (admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,submitted,verified,rejected',
            'catatan' => 'nullable|string|max:500'
        ]);

        $upload = Upload::findOrFail($id);

        $updateData = ['status' => $request->status];

        if ($request->filled('catatan')) {
            $updateData['catatan'] = $request->catatan;
        }

        if ($request->status === 'verified') {
            $updateData['verified_at'] = now();
            $updateData['verified_by'] = auth()->id();
        } else {
            $updateData['verified_at'] = null;
            $updateData['verified_by'] = null;
        }

        $upload->update($updateData);

        return redirect()->back()
            ->with('success', 'Status putusan berhasil diperbarui.');
    }

    /**
     * Preview file (admin)
     */
    public function preview($id)
    {
        $upload = Upload::findOrFail($id);

        if (pathinfo($upload->file_path, PATHINFO_EXTENSION) === 'pdf') {
            return response()->file(storage_path('app/public/' . $upload->file_path));
        }

        return view('admin.uploads.preview', compact('upload'));
    }

    /**
     * Download file (admin)
     */
    public function download($id)
    {
        $upload = Upload::findOrFail($id);
        $path = storage_path('app/public/' . $upload->file_path);

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        return response()->download($path, $upload->original_filename);
    }

    /**
     * Display trash (admin bisa lihat semua trash)
     */
    public function trashIndex(Request $request)
    {
        $query = Upload::onlyTrashed()
            ->with(['user', 'pengadilan'])
            ->orderBy('deleted_at', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $uploads = $query->paginate(20);
        $users = User::orderBy('name')->get();

        return view('admin.uploads.trash.index', compact('uploads', 'users'));
    }

    /**
     * Restore from trash (admin)
     */
    public function restore($id)
    {
        $upload = Upload::onlyTrashed()->findOrFail($id);
        $upload->restore();

        return redirect()->route('admin.uploads.trash')
            ->with('success', 'Putusan berhasil dipulihkan.');
    }

    /**
     * Force delete from trash (admin)
     */
    public function forceDelete($id)
    {
        $upload = Upload::onlyTrashed()->findOrFail($id);

        // Hapus file fisik
        if ($upload->file_path && Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        $upload->forceDelete();

        return redirect()->route('admin.uploads.trash')
            ->with('success', 'Putusan berhasil dihapus permanen.');
    }

    /**
     * Export to Excel (admin)
     */
    public function exportExcel(Request $request)
    {
        // Logika export Excel
        return response()->download('path/to/exports/uploads.xlsx');
    }

    /**
     * Export to PDF (admin)
     */
    public function exportPdf()
    {
        // Logika export PDF
        return response()->download('path/to/exports/uploads.pdf');
    }
}
