<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengadilan;

class PengadilanController extends Controller
{
    public function index()
    {
        $pengadilans = Pengadilan::orderBy('wilayah')
            ->orderBy('nama')
            ->paginate(20);

        $wilayahs = Pengadilan::select('wilayah')->distinct()->pluck('wilayah');

        return view('admin.pengadilan.index', compact('pengadilans', 'wilayahs'));
    }

    public function create()
    {
        $wilayahs = [
            'I',
            'II',
            'II',
            'IV',
            'V'
        ];

        $kelasOptions = ['IA', 'IB', 'II'];

        return view('admin.pengadilan.create', compact('wilayahs', 'kelasOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:10|unique:pengadilan',
            'nama' => 'required|string|max:255',
            'wilayah' => 'required|string|max:100',
            'kelas' => 'nullable|string|max:10',
            'alamat' => 'nullable|string'
        ]);

        Pengadilan::create($request->all());

        return redirect()->route('admin.pengadilan.index')
            ->with('success', 'Pengadilan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pengadilan = Pengadilan::findOrFail($id);

        $wilayahs = [
            'I',
            'II',
            'II',
            'IV',
            'V'
        ];

        $kelasOptions = ['IA', 'IB', 'II'];

        return view('admin.pengadilan.edit', compact('pengadilan', 'wilayahs', 'kelasOptions'));
    }

    public function update(Request $request, $id)
    {
        $pengadilan = Pengadilan::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|max:10|unique:pengadilan,kode,' . $id,
            'nama' => 'required|string|max:255',
            'wilayah' => 'required|string|max:100',
            'kelas' => 'nullable|string|max:10',
            'alamat' => 'nullable|string'
        ]);

        $pengadilan->update($request->all());

        return redirect()->route('admin.pengadilan.index')
            ->with('success', 'Pengadilan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengadilan = Pengadilan::findOrFail($id);

        // Cek jika pengadilan memiliki user
        if ($pengadilan->users()->count() > 0) {
            return redirect()->route('admin.pengadilan.index')
                ->with('error', 'Pengadilan memiliki user, tidak dapat dihapus!');
        }

        // Cek jika pengadilan memiliki upload
        if ($pengadilan->uploads()->count() > 0) {
            return redirect()->route('admin.pengadilan.index')
                ->with('error', 'Pengadilan memiliki data upload, tidak dapat dihapus!');
        }

        $pengadilan->delete();

        return redirect()->route('admin.pengadilan.index')
            ->with('success', 'Pengadilan berhasil dihapus!');
    }

    public function show($id)
    {
        $pengadilan = Pengadilan::with(['users', 'uploads'])->findOrFail($id);

        $stats = [
            'total_users' => $pengadilan->users()->count(),
            'total_uploads' => $pengadilan->uploads()->count(),
            'uploads_kasasi' => $pengadilan->uploads()->where('jenis_putusan', 'kasasi')->count(),
            'uploads_pk' => $pengadilan->uploads()->where('jenis_putusan', 'pk')->count(),
        ];

        return view('admin.pengadilan.show', compact('pengadilan', 'stats'));
    }
}
