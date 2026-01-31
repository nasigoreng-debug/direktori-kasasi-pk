<?php  // Baris 1
namespace App\Http\Controllers\Admin;  // Baris 2 - HARUS langsung setelah <?php

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Upload;
use App\Models\Pengadilan;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_uploads' => Upload::count(),
            'total_pengadilan' => Pengadilan::count(),
            'uploads_today' => Upload::whereDate('created_at', today())->count(),
            'uploads_kasasi' => Upload::where('jenis_putusan', 'kasasi')->count(),
            'uploads_pk' => Upload::where('jenis_putusan', 'pk')->count(),
        ];

        // Recent uploads
        $recentUploads = Upload::with(['user', 'pengadilan'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Stats by pengadilan
        $pengadilanStats = Pengadilan::withCount(['uploads' => function ($query) {
            $query->whereNull('deleted_at');
        }])->orderBy('uploads_count', 'desc')->limit(10)->get();

        return view('admin.dashboard.index', compact('stats', 'recentUploads', 'pengadilanStats'));
    }
}
