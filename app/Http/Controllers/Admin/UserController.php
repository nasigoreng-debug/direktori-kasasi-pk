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
     * Display a listing of users
     */
    public function index(Request $request)
    {
        // Query untuk filter
        $query = User::with('pengadilan')->withCount('uploads');

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter pengadilan
        if ($request->filled('pengadilan_id')) {
            $query->where('pengadilan_id', $request->pengadilan_id);
        }

        // Pagination
        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $pengadilan = Pengadilan::orderBy('kode')->get();

        return view('admin.users.index', compact('users', 'pengadilan'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $pengadilan = Pengadilan::orderBy('kode')->get();
        return view('admin.users.create', compact('pengadilan'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,user',
            'pengadilan_id' => 'nullable|exists:pengadilan,id'
        ]);

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
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with(['pengadilan', 'uploads' => function ($query) {
            $query->orderBy('created_at', 'desc')->limit(10);
        }])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing a user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $pengadilan = Pengadilan::orderBy('kode')->get();

        return view('admin.users.edit', compact('user', 'pengadilan'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

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
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cek jika user memiliki upload
        if ($user->uploads()->count() > 0) {
            return redirect()->back()
                ->with('error', 'User tidak dapat dihapus karena memiliki data upload.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password user
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
     * Update user password (by admin)
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
     * Aktifkan/nonaktifkan user
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()
            ->with('success', "User berhasil $status.");
    }
}
