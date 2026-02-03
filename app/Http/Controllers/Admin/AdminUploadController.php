<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\Pengadilan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AdminUploadController extends Controller
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

        // Upload file - PASTIKAN KE STORAGE BUKAN PUBLIC
        if ($request->hasFile('file_putusan')) {
            $file = $request->file('file_putusan');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Generate unique filename
            $filename = time() . '_' . $request->user_id . '_' .
                preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $originalName);

            // Upload ke storage
            $uploadDir = storage_path('app/public/uploads');

            // Ensure directory exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
                Log::info('Created upload directory: ' . $uploadDir);
            }

            // Move file to storage
            $file->move($uploadDir, $filename);

            // Path untuk database
            $filePath = 'uploads/' . $filename;

            // Verify file was saved
            if (!file_exists($uploadDir . '/' . $filename)) {
                Log::error('File not saved to storage: ' . $filename);
                return back()->withErrors(['file_putusan' => 'Gagal menyimpan file.'])->withInput();
            }

            Log::info('Admin uploaded file: ' . $filePath);
        } else {
            return back()->withErrors(['file_putusan' => 'Tidak ada file yang diunggah.'])->withInput();
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
            'file_path' => $filePath,
            'original_filename' => $originalName,
            'file_size' => $fileSize,
            'status' => $request->status,
            'verified_at' => $request->status === 'verified' ? now() : null,
            'verified_by' => $request->status === 'verified' ? auth()->id() : null,
        ]);

        return redirect()->route('admin.uploads.index')
            ->with('success', 'Putusan berhasil ditambahkan.');
    }

    /**
     * Display the specified upload (admin view) - Menggunakan Route Model Binding
     */
    public function show(Upload $upload)
    {
        $upload->load(['user', 'pengadilan']);
        return view('admin.uploads.show', compact('upload'));
    }

    /**
     * Show the form for editing an upload (admin bisa edit semua) - Menggunakan Route Model Binding
     */
    public function edit(Upload $upload)
    {
        $upload->load(['user', 'pengadilan']);
        $pengadilans = Pengadilan::orderBy('kode')->get();
        $users = User::where('role', 'user')->orderBy('name')->get();

        return view('admin.uploads.edit', compact('upload', 'pengadilans', 'users'));
    }

    /**
     * Update the specified upload (admin) - Menggunakan Route Model Binding
     */
    public function update(Request $request, Upload $upload)
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
            $file = $request->file('file_putusan');
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Generate nama file baru
            $filename = time() . '_' . $request->user_id . '_edit_' .
                preg_replace('/[^A-Za-z0-9\.\_\-]/', '_', $originalName);

            // Hapus file lama dari semua lokasi yang mungkin
            if ($upload->file_path) {
                $oldFilename = basename($upload->file_path);

                // Hapus dari public/uploads/
                $publicPath = public_path('uploads/' . $oldFilename);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                    Log::info('Deleted old file from public/uploads/: ' . $publicPath);
                }

                // Hapus dari storage
                $storagePath = storage_path('app/public/' . $upload->file_path);
                if (file_exists($storagePath)) {
                    unlink($storagePath);
                    Log::info('Deleted old file from storage: ' . $storagePath);
                }
            }

            // Upload file baru ke storage
            $uploadDir = storage_path('app/public/uploads');
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $file->move($uploadDir, $filename);
            $filePath = 'uploads/' . $filename;

            $updateData['file_path'] = $filePath;
            $updateData['original_filename'] = $originalName;
            $updateData['file_size'] = $fileSize;
        }

        $upload->update($updateData);

        return redirect()->route('admin.uploads.index')
            ->with('success', 'Putusan berhasil diperbarui.');
    }

    /**
     * Remove the specified upload (admin - permanent delete) - Menggunakan Route Model Binding
     */
    public function destroy(Upload $upload)
    {
        // Hapus file fisik dari semua lokasi yang mungkin
        if ($upload->file_path) {
            $filename = basename($upload->file_path);

            // 1. Hapus dari public/uploads/
            $publicPath = public_path('uploads/' . $filename);
            if (file_exists($publicPath)) {
                unlink($publicPath);
                Log::info('Deleted from public/uploads/: ' . $publicPath);
            }

            // 2. Hapus dari storage/app/public/
            $storagePath = storage_path('app/public/' . $upload->file_path);
            if (file_exists($storagePath)) {
                unlink($storagePath);
                Log::info('Deleted from storage/app/public/: ' . $storagePath);
            }

            // 3. Hapus dari storage/app/public/uploads/
            $storagePath2 = storage_path('app/public/uploads/' . $filename);
            if (file_exists($storagePath2)) {
                unlink($storagePath2);
                Log::info('Deleted from storage/app/public/uploads/: ' . $storagePath2);
            }
        }

        $upload->forceDelete();

        return redirect()->route('admin.uploads.index')
            ->with('success', 'Putusan berhasil dihapus permanen.');
    }

    /**
     * Preview file (admin) - FIXED untuk cek multiple locations - Menggunakan Route Model Binding
     */
    public function preview(Upload $upload)
    {
        // Cek file di beberapa lokasi
        $filename = basename($upload->file_path);

        // Lokasi 1: public/uploads/ (where files actually are)
        $publicPath = public_path('uploads/' . $filename);

        // Lokasi 2: storage/app/public/uploads/
        $storagePath = storage_path('app/public/' . $upload->file_path);

        // Lokasi 3: storage/app/public/uploads/ dengan hanya filename
        $storagePath2 = storage_path('app/public/uploads/' . $filename);

        Log::info('Admin preview - Checking file locations:', [
            'upload_id' => $upload->id,
            'database_path' => $upload->file_path,
            'filename' => $filename,
            'public_exists' => file_exists($publicPath),
            'storage_exists' => file_exists($storagePath),
            'storage_exists2' => file_exists($storagePath2)
        ]);

        // Prioritaskan public/uploads/
        if (file_exists($publicPath)) {
            Log::info('Admin preview: File found in public/uploads/');

            if (strtolower(pathinfo($publicPath, PATHINFO_EXTENSION)) === 'pdf') {
                return response()->file($publicPath);
            }
        }

        // Coba di storage
        if (file_exists($storagePath)) {
            Log::info('Admin preview: File found in storage/');

            if (strtolower(pathinfo($storagePath, PATHINFO_EXTENSION)) === 'pdf') {
                return response()->file($storagePath);
            }
        }

        // Coba lokasi alternatif
        if (file_exists($storagePath2)) {
            Log::info('Admin preview: File found in storage/uploads/');

            if (strtolower(pathinfo($storagePath2, PATHINFO_EXTENSION)) === 'pdf') {
                return response()->file($storagePath2);
            }
        }

        Log::error('Admin preview: File not found in any location');
        return back()->with('error', 'File tidak ditemukan di server.');
    }

    /**
     * Download file (admin) - FIXED untuk cek multiple locations - Menggunakan Route Model Binding
     */
    public function download(Upload $upload)
    {
        // Cek file di beberapa lokasi
        $filename = basename($upload->file_path);

        // Lokasi 1: public/uploads/
        $publicPath = public_path('uploads/' . $filename);

        // Lokasi 2: storage/app/public/uploads/
        $storagePath = storage_path('app/public/' . $upload->file_path);

        // Lokasi 3: storage/app/public/uploads/ dengan hanya filename
        $storagePath2 = storage_path('app/public/uploads/' . $filename);

        Log::info('Admin download - Checking file locations:', [
            'upload_id' => $upload->id,
            'database_path' => $upload->file_path,
            'filename' => $filename,
            'public_exists' => file_exists($publicPath),
            'storage_exists' => file_exists($storagePath),
            'storage_exists2' => file_exists($storagePath2)
        ]);

        // Prioritaskan public/uploads/
        if (file_exists($publicPath)) {
            Log::info('Admin download: File found in public/uploads/');
            return response()->download($publicPath, $upload->original_filename);
        }

        // Coba di storage
        if (file_exists($storagePath)) {
            Log::info('Admin download: File found in storage/');
            return response()->download($storagePath, $upload->original_filename);
        }

        // Coba lokasi alternatif
        if (file_exists($storagePath2)) {
            Log::info('Admin download: File found in storage/uploads/');
            return response()->download($storagePath2, $upload->original_filename);
        }

        Log::error('Admin download: File not found in any location');
        return back()->with('error', 'File tidak ditemukan di server.');
    }

    /**
     * Update status upload (admin) - Menggunakan Route Model Binding
     */
    public function updateStatus(Request $request, Upload $upload)
    {
        $request->validate([
            'status' => 'required|in:draft,submitted,verified,rejected',
            'catatan' => 'nullable|string|max:500'
        ]);

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
     * Verify upload (admin) - Menggunakan Route Model Binding
     */
    public function verify(Upload $upload)
    {
        $upload->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => auth()->id()
        ]);

        return redirect()->back()
            ->with('success', 'Putusan berhasil diverifikasi.');
    }

    /**
     * Reject upload (admin) - Menggunakan Route Model Binding
     */
    public function reject(Upload $upload)
    {
        $upload->update(['status' => 'rejected']);

        return redirect()->back()
            ->with('success', 'Putusan berhasil ditolak.');
    }

    /**
     * Export to Excel (admin)
     */
    public function exportExcel(Request $request)
    {
        // Implementasi export Excel
        // ...
    }

    /**
     * Export to PDF (admin)
     */
    public function exportPdf(Request $request)
    {
        // Implementasi export PDF
        // ...
    }
}
