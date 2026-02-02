<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengadilan;

class PengadilanController extends Controller
{
    /**
     * Menampilkan daftar semua pengadilan
     * 
     * Method ini menampilkan data pengadilan dengan pagination dan 
     * mengumpulkan daftar wilayah unik untuk keperluan filter
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil data pengadilan dengan urutan wilayah lalu nama
        $pengadilans = Pengadilan::orderBy('wilayah')
            ->orderBy('nama')
            ->paginate(20); // Pagination untuk 20 item per halaman

        // Mengambil daftar wilayah unik untuk filter
        $wilayahs = Pengadilan::select('wilayah')->distinct()->pluck('wilayah');

        return view('admin.pengadilan.index', compact('pengadilans', 'wilayahs'));
    }

    /**
     * Menampilkan form untuk membuat pengadilan baru
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Daftar wilayah yang tersedia (hardcoded sesuai kebutuhan sistem)
        $wilayahs = [
            'I',
            'II',
            'III',
            'IV',
            'V'
        ];

        // Opsi kelas pengadilan
        $kelasOptions = ['IA', 'IB', 'II'];

        return view('admin.pengadilan.create', compact('wilayahs', 'kelasOptions'));
    }

    /**
     * Menyimpan pengadilan baru ke database
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'kode' => 'required|string|max:10|unique:pengadilan', // Harus unik
            'nama' => 'required|string|max:255',
            'wilayah' => 'required|string|max:100',
            'kelas' => 'nullable|string|max:10',
            'alamat' => 'nullable|string'
        ]);

        // Membuat record pengadilan baru
        Pengadilan::create($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('admin.pengadilan.index')
            ->with('success', 'Pengadilan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form untuk mengedit pengadilan yang ada
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Mencari pengadilan berdasarkan ID, throw 404 jika tidak ditemukan
        $pengadilan = Pengadilan::findOrFail($id);

        // Daftar wilayah yang tersedia
        $wilayahs = [
            'I',
            'II',
            'III',
            'IV',
            'V'
        ];

        // Opsi kelas pengadilan
        $kelasOptions = ['IA', 'IB', 'II'];

        return view('admin.pengadilan.edit', compact('pengadilan', 'wilayahs', 'kelasOptions'));
    }

    /**
     * Memperbarui data pengadilan yang ada di database
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Mencari pengadilan berdasarkan ID
        $pengadilan = Pengadilan::findOrFail($id);

        // Validasi input, dengan pengecualian untuk kode yang sedang diupdate
        $request->validate([
            'kode' => 'required|string|max:10|unique:pengadilan,kode,' . $id,
            'nama' => 'required|string|max:255',
            'wilayah' => 'required|string|max:100',
            'kelas' => 'nullable|string|max:10',
            'alamat' => 'nullable|string'
        ]);

        // Memperbarui data pengadilan
        $pengadilan->update($request->all());

        // Redirect ke halaman index dengan pesan sukses
        return redirect()->route('admin.pengadilan.index')
            ->with('success', 'Pengadilan berhasil diperbarui!');
    }

    /**
     * Menghapus pengadilan dari database
     * 
     * Method ini melakukan pengecekan relasi sebelum menghapus.
     * Pengadilan tidak dapat dihapus jika masih memiliki user atau upload.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Mencari pengadilan berdasarkan ID
        $pengadilan = Pengadilan::findOrFail($id);

        // Pengecekan 1: Apakah pengadilan memiliki user terkait?
        if ($pengadilan->users()->count() > 0) {
            return redirect()->route('admin.pengadilan.index')
                ->with('error', 'Pengadilan memiliki user, tidak dapat dihapus!');
        }

        // Pengecekan 2: Apakah pengadilan memiliki data upload?
        if ($pengadilan->uploads()->count() > 0) {
            return redirect()->route('admin.pengadilan.index')
                ->with('error', 'Pengadilan memiliki data upload, tidak dapat dihapus!');
        }

        // Menghapus pengadilan jika lolos semua pengecekan
        $pengadilan->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('admin.pengadilan.index')
            ->with('success', 'Pengadilan berhasil dihapus!');
    }

    /**
     * Menampilkan detail pengadilan
     * 
     * Menampilkan informasi lengkap pengadilan beserta statistik terkait
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        // Mengambil data pengadilan dengan relasi users dan uploads (eager loading)
        $pengadilan = Pengadilan::with(['users', 'uploads'])->findOrFail($id);

        // Menghitung statistik untuk pengadilan ini
        $stats = [
            'total_users' => $pengadilan->users()->count(),
            'total_uploads' => $pengadilan->uploads()->count(),
            'uploads_kasasi' => $pengadilan->uploads()->where('jenis_putusan', 'kasasi')->count(),
            'uploads_pk' => $pengadilan->uploads()->where('jenis_putusan', 'pk')->count(),
        ];

        return view('admin.pengadilan.show', compact('pengadilan', 'stats'));
    }
}
