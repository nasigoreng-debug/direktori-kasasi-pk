<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit profil pengguna
     * 
     * Method ini mengambil data user yang sedang login dan 
     * menampilkan form untuk mengubah informasi profil
     * 
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        // ============================================
        // AMBIL DATA USER YANG SEDANG LOGIN
        // ============================================
        $user = Auth::user();

        return view('user.profile.edit', compact('user'));
    }

    /**
     * Memperbarui informasi profil pengguna
     * 
     * Method ini menangani update data profil seperti nama dan email
     * dengan validasi yang sesuai
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // ============================================
        // AMBIL USER YANG SEDANG LOGIN
        // ============================================
        $user = Auth::user();

        // ============================================
        // VALIDASI INPUT
        // - Email harus unik, dengan pengecualian untuk user yang sedang diupdate
        // ============================================
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        // ============================================
        // UPDATE DATA USER
        // ============================================
        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        // ============================================
        // REDIRECT DENGAN PESAN SUKSES
        // ============================================
        return redirect()->route('user.profile.edit')
            ->with('success', 'Profile berhasil diperbarui!');
    }

    /**
     * Mengubah password pengguna
     * 
     * Method ini menangani perubahan password dengan validasi:
     * 1. Password lama harus sesuai
     * 2. Password baru harus konfirmasi
     * 3. Password baru minimal 8 karakter
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        // ============================================
        // VALIDASI INPUT PASSWORD
        // ============================================
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // ============================================
        // AMBIL USER YANG SEDANG LOGIN
        // ============================================
        $user = Auth::user();

        // ============================================
        // VERIFIKASI PASSWORD LAMA
        // Memastikan password yang dimasukkan sesuai dengan yang tersimpan
        // ============================================
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah!']);
        }

        // ============================================
        // UPDATE PASSWORD BARU
        // Password di-hash sebelum disimpan
        // ============================================
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // ============================================
        // REDIRECT DENGAN PESAN SUKSES
        // ============================================
        return redirect()->route('user.profile.edit')
            ->with('success', 'Password berhasil diubah!');
    }
}
