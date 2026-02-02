<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengadilan;
use App\Models\Upload;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard pengguna (user)
     * 
     * Method ini menampilkan statistik dan informasi penting untuk user:
     * 1. Data profil user dan pengadilan terkait
     * 2. Statistik upload dokumen
     * 3. Upload terbaru
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ============================================
        // AMBIL DATA USER YANG SEDANG LOGIN
        // ============================================
        $user = Auth::user();

        // ============================================
        // VALIDASI DAN SET DEFAULT PENGADILAN
        // Memastikan user selalu memiliki pengadilan_id yang valid
        // ============================================
        if (empty($user->pengadilan_id)) {
            $pengadilan = Pengadilan::first(); // Ambil pengadilan pertama sebagai default

            if ($pengadilan) {
                $user->pengadilan_id = $pengadilan->id;
                $user->save(); // Simpan perubahan ke database
            }
        }

        // ============================================
        // LOAD DATA PENGADILAN TERKAIT
        // Mengambil informasi pengadilan user untuk ditampilkan
        // ============================================
        $pengadilan = Pengadilan::find($user->pengadilan_id);

        // ============================================
        // HITUNG STATISTIK UPLOAD
        // Menghitung berbagai metrik untuk ditampilkan di dashboard
        // ============================================

        // Total semua upload user
        $totalUploads = Upload::where('user_id', $user->id)->count();

        // Jumlah upload dengan jenis putusan 'kasasi'
        $kasasiCount = Upload::where('user_id', $user->id)
            ->where('jenis_putusan', 'kasasi')->count();

        // Jumlah upload dengan jenis putusan 'pk' (Peninjauan Kembali)
        $pkCount = Upload::where('user_id', $user->id)
            ->where('jenis_putusan', 'pk')->count();

        // ============================================
        // AMBIL UPLOAD TERBARU
        // Menampilkan 5 upload terbaru untuk monitoring aktivitas
        // ============================================
        $recentUploads = Upload::where('user_id', $user->id)
            ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
            ->limit(5) // Batasi hanya 5 data
            ->get();

        // ============================================
        // RETURN VIEW DENGAN DATA
        // ============================================
        return view('user.dashboard', [
            'user' => $user,
            'pengadilan' => $pengadilan,
            'totalUploads' => $totalUploads,
            'kasasiCount' => $kasasiCount,
            'pkCount' => $pkCount,
            'recentUploads' => $recentUploads
        ]);
    }
}
