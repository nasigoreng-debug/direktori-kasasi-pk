<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Upload;
use App\Models\Pengadilan;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin
     * 
     * Method ini mengumpulkan berbagai statistik dan data untuk ditampilkan
     * pada dashboard admin, termasuk data pengguna, upload, dan pengadilan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ============================================
        // STATISTIK UTAMA
        // Mengumpulkan berbagai metrik statistik untuk ditampilkan di dashboard
        // ============================================
        $stats = [
            'total_users' => User::count(), // Total semua pengguna terdaftar
            'total_uploads' => Upload::count(), // Total semua dokumen yang diupload
            'total_pengadilan' => Pengadilan::count(), // Total lembaga pengadilan
            'uploads_today' => Upload::whereDate('created_at', today())->count(), // Upload hari ini
            'uploads_kasasi' => Upload::where('jenis_putusan', 'kasasi')->count(), // Putusan kasasi
            'uploads_pk' => Upload::where('jenis_putusan', 'pk')->count(), // Putusan peninjauan kembali
        ];

        // ============================================
        // UPLOAD TERBARU
        // Mendapatkan 10 upload terbaru dengan relasi user dan pengadilan
        // Digunakan untuk menampilkan aktivitas terkini
        // ============================================
        $recentUploads = Upload::with(['user', 'pengadilan']) // Eager loading untuk optimasi query
            ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
            ->limit(10) // Batasi hanya 10 data
            ->get();

        // ============================================
        // STATISTIK PER PENGADILAN
        // Menghitung jumlah upload untuk setiap pengadilan
        // Hanya menghitung upload yang belum dihapus (soft delete)
        // ============================================
        $pengadilanStats = Pengadilan::withCount(['uploads' => function ($query) {
            $query->whereNull('deleted_at'); // Filter hanya upload yang aktif (tidak terhapus)
        }])
            ->orderBy('uploads_count', 'desc') // Urutkan berdasarkan jumlah upload tertinggi
            ->limit(10) // Ambil 10 pengadilan dengan upload terbanyak
            ->get();

        // ============================================
        // RETURN VIEW
        // Mengirim semua data ke view admin.dashboard.index
        // ============================================
        return view('admin.dashboard.index', compact('stats', 'recentUploads', 'pengadilanStats'));
    }
}
