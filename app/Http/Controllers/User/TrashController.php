<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Upload;

class TrashController extends Controller
{
    /**
     * Menampilkan daftar dokumen yang dihapus (soft deleted)
     * 
     * Method ini menampilkan semua upload yang telah dihapus oleh user
     * dalam bentuk soft delete (masih ada di database dengan deleted_at terisi)
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ============================================
        // AMBIL DATA UPLOAD YANG DIHAPUS (SOFT DELETE)
        // Hanya menampilkan data milik user yang sedang login
        // ============================================
        $uploads = Upload::onlyTrashed() // Hanya data yang di-soft delete
            ->where('user_id', Auth::id()) // Filter berdasarkan user yang login
            ->orderBy('deleted_at', 'desc') // Urutkan berdasarkan waktu penghapusan terbaru
            ->paginate(10); // Pagination 10 item per halaman

        return view('user.trash.index', compact('uploads'));
    }

    /**
     * Memulihkan dokumen yang telah dihapus
     * 
     * Method ini mengembalikan dokumen dari trash ke daftar aktif
     * dengan mengosongkan kolom deleted_at
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        // ============================================
        // VALIDASI DAN AMBIL DATA
        // Pastikan dokumen milik user dan memang dihapus
        // ============================================
        $upload = Upload::onlyTrashed()
            ->where('user_id', Auth::id()) // Hanya user pemilik yang bisa restore
            ->where('id', $id)
            ->firstOrFail(); // Throw 404 jika tidak ditemukan

        // ============================================
        // RESTORE DOKUMEN
        // Mengosongkan deleted_at sehingga dokumen aktif kembali
        // ============================================
        $upload->restore();

        return redirect()->route('user.trash.index')
            ->with('success', 'Putusan berhasil dipulihkan!');
    }

    /**
     * Menghapus permanen dokumen dari sistem
     * 
     * Method ini menghapus dokumen secara permanen dengan:
     * 1. Menghapus file fisik dari storage
     * 2. Menghapus record dari database (force delete)
     * 
     * PERINGATAN: Tindakan ini tidak dapat dibatalkan!
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete($id)
    {
        // ============================================
        // VALIDASI DAN AMBIL DATA
        // Pastikan dokumen milik user dan memang dihapus
        // ============================================
        $upload = Upload::onlyTrashed()
            ->where('user_id', Auth::id()) // Hanya user pemilik yang bisa hapus permanen
            ->where('id', $id)
            ->firstOrFail(); // Throw 404 jika tidak ditemukan

        // ============================================
        // HAPUS FILE FISIK DARI STORAGE
        // Menghapus file dokumen dari sistem penyimpanan
        // ============================================
        if (Storage::disk('public')->exists($upload->file_path)) {
            Storage::disk('public')->delete($upload->file_path);
        }

        // ============================================
        // HAPUS PERMANEN DARI DATABASE
        // Menghapus record dari tabel uploads (force delete)
        // ============================================
        $upload->forceDelete();

        return redirect()->route('user.trash.index')
            ->with('success', 'Putusan berhasil dihapus permanen!');
    }
}
