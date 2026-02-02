{{--
    DETAIL PENGADILAN
    View ini menampilkan detail lengkap sebuah pengadilan beserta statistik dan data terkait.
    Layout: layouts.app
--}}

@extends('layouts.app')

{{-- Set judul halaman --}}
@section('title', 'Detail Pengadilan: ' . $pengadilan->nama)

@section('content')
<div class="container-fluid">
    {{-- ============================================ --}}
    {{<!-- BARIS UTAMA -->}}
    {{-- ============================================ --}}
    <div class="row">
        <div class="col-md-12">
            {{-- ============================================ --}}
            {{<!-- KARTU UTAMA DETAIL -->}}
            {{-- ============================================ --}}
            <div class="card">
                {{-- Header kartu dengan judul dan tombol aksi --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-landmark"></i> Detail Pengadilan: {{ $pengadilan->nama }}
                    </h5>
                    <div>
                        {{-- Tombol Edit --}}
                        <a href="{{ route('admin.pengadilan.edit', $pengadilan->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        {{-- Tombol Kembali --}}
                        <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                {{-- End Header --}}

                {{-- Body kartu --}}
                <div class="card-body">
                    {{-- ============================================ --}}
                    {{<!-- STATISTIK PENGADILAN -->}}
                    {{-- ============================================ --}}
                    <div class="row mb-4">
                        {{-- Kartu: Total User --}}
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-users"></i></h1>
                                    <h5>Total User</h5>
                                    <h3>{{ $stats['total_users'] }}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- End Kartu Total User --}}

                        {{-- Kartu: Total Upload --}}
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-success">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-file-upload"></i></h1>
                                    <h5>Total Upload</h5>
                                    <h3>{{ $stats['total_uploads'] }}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- End Kartu Total Upload --}}

                        {{-- Kartu: Putusan Kasasi --}}
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-info">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-gavel"></i></h1>
                                    <h5>Putusan Kasasi</h5>
                                    <h3>{{ $stats['uploads_kasasi'] }}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- End Kartu Putusan Kasasi --}}

                        {{-- Kartu: Putusan PK --}}
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-balance-scale"></i></h1>
                                    <h5>Putusan PK</h5>
                                    <h3>{{ $stats['uploads_pk'] }}</h3>
                                </div>
                            </div>
                        </div>
                        {{-- End Kartu Putusan PK --}}
                    </div>
                    {{-- End Row Statistik --}}

                    {{-- ============================================ --}}
                    {{<!-- INFORMASI DETAIL PENGADILAN -->}}
                    {{-- ============================================ --}}
                    <div class="row">
                        {{-- Kolom Kiri: Informasi Pengadilan --}}
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Informasi Pengadilan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        {{-- Baris Kode --}}
                                        <tr>
                                            <th width="150">Kode</th>
                                            <td>
                                                <span class="badge bg-primary">{{ $pengadilan->kode }}</span>
                                            </td>
                                        </tr>
                                        {{-- End Baris Kode --}}

                                        {{-- Baris Nama --}}
                                        <tr>
                                            <th>Nama</th>
                                            <td>{{ $pengadilan->nama }}</td>
                                        </tr>
                                        {{-- End Baris Nama --}}

                                        {{-- Baris Wilayah --}}
                                        <tr>
                                            <th>Wilayah</th>
                                            <td>
                                                <span class="badge bg-info">{{ $pengadilan->wilayah }}</span>
                                            </td>
                                        </tr>
                                        {{-- End Baris Wilayah --}}

                                        {{-- Baris Kelas --}}
                                        <tr>
                                            <th>Kelas</th>
                                            <td>
                                                @if($pengadilan->kelas)
                                                <span class="badge bg-warning">{{ $pengadilan->kelas }}</span>
                                                @else
                                                <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        {{-- End Baris Kelas --}}

                                        {{-- Baris Alamat --}}
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $pengadilan->alamat ?? '-' }}</td>
                                        </tr>
                                        {{-- End Baris Alamat --}}

                                        {{-- Baris Dibuat --}}
                                        <tr>
                                            <th>Dibuat</th>
                                            <td>{{ $pengadilan->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        {{-- End Baris Dibuat --}}

                                        {{-- Baris Diupdate --}}
                                        <tr>
                                            <th>Diupdate</th>
                                            <td>{{ $pengadilan->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        {{-- End Baris Diupdate --}}
                                    </table>
                                </div>
                            </div>
                        </div>
                        {{-- End Kolom Informasi Pengadilan --}}

                        {{-- Kolom Kanan: Daftar User --}}
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Users di Pengadilan Ini</h6>
                                </div>
                                <div class="card-body">
                                    {{-- Cek apakah ada user --}}
                                    @if($pengadilan->users->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Nama</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- Loop melalui user --}}
                                                @foreach($pengadilan->users as $user)
                                                <tr>
                                                    {{-- Nama User --}}
                                                    <td>{{ $user->name }}</td>
                                                    {{-- Email User --}}
                                                    <td>{{ $user->email }}</td>
                                                    {{-- Role User dengan badge --}}
                                                    <td>
                                                        <span class="badge bg-{{ $user->role == 'admin' ? 'warning' : 'info' }}">
                                                            {{ ucfirst($user->role) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                {{-- End Loop --}}
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    {{-- Pesan jika tidak ada user --}}
                                    <p class="text-muted text-center">Belum ada user di pengadilan ini</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- End Kolom Daftar User --}}
                    </div>
                    {{-- End Row Informasi --}}

                    {{-- ============================================ --}}
                    {{<!-- UPLOAD TERBARU DARI PENGADILAN -->}}
                    {{-- ============================================ --}}
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Upload Terbaru dari Pengadilan Ini</h6>
                        </div>
                        <div class="card-body">
                            {{-- Cek apakah ada upload --}}
                            @if($pengadilan->uploads->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>User</th>
                                            <th>Jenis</th>
                                            <th>Nomor Perkara</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Loop melalui 10 upload terbaru --}}
                                        @foreach($pengadilan->uploads()->latest()->limit(10)->get() as $upload)
                                        <tr>
                                            {{-- Tanggal Upload --}}
                                            <td>{{ $upload->created_at->format('d/m/Y') }}</td>
                                            {{-- Nama User --}}
                                            <td>{{ $upload->user->name }}</td>
                                            {{-- Jenis Putusan --}}
                                            <td>
                                                <span class="badge bg-{{ $upload->jenis_putusan == 'kasasi' ? 'primary' : 'warning' }}">
                                                    {{ strtoupper($upload->jenis_putusan) }}
                                                </span>
                                            </td>
                                            {{-- Nomor Perkara --}}
                                            <td>
                                                <small>{{ $upload->nomor_perkara_pa }}</small>
                                            </td>
                                            {{-- Status Upload --}}
                                            <td>
                                                <span class="badge bg-{{ $upload->status == 'submitted' ? 'info' : 'success' }}">
                                                    {{ ucfirst($upload->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                        {{-- End Loop --}}
                                    </tbody>
                                </table>
                            </div>
                            @else
                            {{-- Pesan jika tidak ada upload --}}
                            <p class="text-muted text-center">Belum ada upload dari pengadilan ini</p>
                            @endif
                        </div>
                    </div>
                    {{-- End Card Upload Terbaru --}}
                </div>
                {{-- End Card Body --}}
            </div>
            {{-- End Main Card --}}
        </div>
        {{-- End Column --}}
    </div>
    {{-- End Row --}}
</div>
{{-- End Container --}}
@endsection