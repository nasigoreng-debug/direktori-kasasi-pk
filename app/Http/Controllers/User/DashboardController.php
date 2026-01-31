<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Pastikan user punya pengadilan_id
        if (empty($user->pengadilan_id)) {
            $pengadilan = \App\Models\Pengadilan::first();
            if ($pengadilan) {
                $user->pengadilan_id = $pengadilan->id;
                $user->save();
            }
        }

        // Load pengadilan
        $pengadilan = \App\Models\Pengadilan::find($user->pengadilan_id);

        // Hitung statistik upload
        $totalUploads = \App\Models\Upload::where('user_id', $user->id)->count();
        $kasasiCount = \App\Models\Upload::where('user_id', $user->id)
            ->where('jenis_putusan', 'kasasi')->count();
        $pkCount = \App\Models\Upload::where('user_id', $user->id)
            ->where('jenis_putusan', 'pk')->count();

        // Upload terbaru
        $recentUploads = \App\Models\Upload::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

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
