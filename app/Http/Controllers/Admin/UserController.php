<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pengadilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua pengguna dengan fitur filter dan pencarian
     * 
     * Method ini mendukung filtering berdasarkan:
     * - Pencarian nama/email
     * - Role pengguna
     * - Pengadilan terkait
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // ============================================
        // QUERY DASAR
        // Menyertakan relasi pengadilan dan hitung jumlah upload
        // ============================================
        $query = User::with('pengadilan')->withCount('uploads');

        // ============================================
        // FILTER PENCARIAN
        // Mencari berdasarkan nama atau email pengguna
        // ============================================
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // ============================================
        // FILTER ROLE
        // Filter berdasarkan role (admin/user)
        // ============================================
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // ============================================
        // FILTER PENGADILAN
        // Filter berdasarkan pengadilan terkait
        // ============================================
        if ($request->filled('pengadilan_id')) {
            $query->where('pengadilan_id', $request->pengadilan_id);
        }

        // ============================================
        // PAGINATION DAN ORDERING
        // Urutkan dari yang terbaru dan batasi 20 item per halaman
        // ============================================
        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        // Ambil semua pengadilan untuk dropdown filter
        $pengadilan = Pengadilan::orderBy('kode')->get();

        return view('admin.users.index', compact('users', 'pengadilan'));
    }

    /**
     * Menampilkan form untuk membuat pengguna baru
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Mengambil daftar pengadilan untuk dropdown
        $pengadilan = Pengadilan::orderBy('kode')->get();
        return view('admin.users.create', compact('pengadilan'));
    }

    /**
     * Menyimpan pengguna baru ke database
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // ============================================
        // VALIDASI INPUT
        // ============================================
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users', // Email harus unik
            'password' => 'required|min:8|confirmed', // Konfirmasi password
            'role' => 'required|in:admin,user', // Hanya role yang diizinkan
            'pengadilan_id' => 'nullable|exists:pengadilan,id' // Pastikan pengadilan valid
        ]);

        // ============================================
        // CREATE USER BARU
        // Password di-hash sebelum disimpan
        // ============================================
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'pengadilan_id' => $request->pengadilan_id
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail pengguna tertentu
     * 
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Eager loading untuk optimasi query
        $user = User::with(['pengadilan', 'uploads' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10); // 10 upload terbaru
        }])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Menampilkan form untuk mengedit pengguna
     * 
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $pengadilan = Pengadilan::orderBy('kode')->get();

        return view('admin.users.edit', compact('user', 'pengadilan'));
    }

    /**
     * Memperbarui data pengguna yang ada
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // ============================================
        // VALIDASI DENGAN IGNORE UNIQUE
        // Mengabaikan email yang sedang diupdate
        // ============================================
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id)
            ],
            'role' => 'required|in:admin,user',
            'pengadilan_id' => 'nullable|exists:pengadilan,id'
        ]);

        // ============================================
        // UPDATE DATA USER
        // Tidak termasuk password di sini
        // ============================================
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'pengadilan_id' => $request->pengadilan_id
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus pengguna dari sistem
     * 
     * Dilakukan pengecekan terlebih dahulu apakah pengguna memiliki data upload
     * untuk mencegah penghapusan data penting.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // ============================================
        // CEK RELASI UPLOAD
        // Mencegah penghapusan jika user memiliki data upload
        // ============================================
        if ($user->uploads()->count() > 0) {
            return redirect()->back()
                ->with('error', 'User tidak dapat dihapus karena memiliki data upload.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password pengguna ke default
     * 
     * Berguna untuk kasus lupa password atau akun baru.
     * Harus diikuti dengan pemberitahuan ke user untuk mengganti password.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $defaultPassword = 'password123'; // Password default

        $user->update([
            'password' => Hash::make($defaultPassword)
        ]);

        return redirect()->back()
            ->with('success', 'Password berhasil direset ke: ' . $defaultPassword)
            ->with('info', 'Beritahu user untuk segera mengganti password setelah login.');
    }

    /**
     * Mengubah password pengguna (oleh admin)
     * 
     * Admin dapat mengubah password user tanpa perlu tahu password lama.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()
            ->with('success', 'Password user berhasil diubah.');
    }

    /**
     * Mengaktifkan atau menonaktifkan status pengguna
     * 
     * Berguna untuk mengontrol akses tanpa menghapus akun.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Toggle status aktif/nonaktif
        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "User berhasil $status.");
    }
}
