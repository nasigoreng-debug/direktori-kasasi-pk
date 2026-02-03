@extends('layouts.app')

@section('title', 'Dashboard User')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Dashboard Pengguna</h5>
            </div>
            <div class="card-body">
                {{-- Bagian Informasi Pengadilan dan Akun --}}
                <div class="row">
                    {{-- Informasi Pengadilan --}}
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-landmark"></i> Informasi Pengadilan
                                </h5>
                                <hr>
                                @isset($pengadilan)
                                <p class="mb-1"><strong>Nama:</strong> {{ $pengadilan->nama ?? 'Tidak tersedia' }}</p>
                                <p class="mb-1"><strong>Kode:</strong> {{ $pengadilan->kode ?? 'Tidak tersedia' }}</p>
                                <p class="mb-1"><strong>Wilayah:</strong> {{ $pengadilan->wilayah ?? 'Tidak tersedia' }}</p>
                                @if(!empty($pengadilan->kelas))
                                <p class="mb-0"><strong>Kelas:</strong> {{ $pengadilan->kelas }}</p>
                                @endif
                                @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Data pengadilan tidak ditemukan
                                </div>
                                @endisset
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Akun --}}
                    <div class="col-md-6 mb-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-user"></i> Informasi Akun
                                </h5>
                                <hr>
                                <p class="mb-1"><strong>Nama:</strong> {{ $user->name }}</p>
                                <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                                <p class="mb-1"><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                                <p class="mb-0"><strong>Bergabung:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Statistik --}}
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-info h-100">
                            <div class="card-body text-center">
                                <h1><i class="fas fa-file-upload"></i></h1>
                                <h5>Total Upload</h5>
                                <h3>{{ $totalUploads ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-primary h-100">
                            <div class="card-body text-center">
                                <h1><i class="fas fa-gavel"></i></h1>
                                <h5>Putusan Kasasi</h5>
                                <h3>{{ $kasasiCount ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3">
                        <div class="card text-white bg-warning h-100">
                            <div class="card-body text-center">
                                <h1><i class="fas fa-balance-scale"></i></h1>
                                <h5>Putusan PK</h5>
                                <h3>{{ $pkCount ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Uploads --}}
                @if(isset($recentUploads) && $recentUploads->count() > 0)
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-history"></i> Upload Terbaru</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Nomor Perkara</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentUploads as $upload)
                                            <tr>
                                                <td>{{ $upload->created_at->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $upload->jenis_putusan == 'kasasi' ? 'primary' : 'warning' }}">
                                                        {{ strtoupper($upload->jenis_putusan) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>{{ $upload->nomor_perkara_pa }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $upload->status == 'submitted' ? 'info' : 'success' }}">
                                                        {{ ucfirst($upload->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tombol Aksi --}}
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <a href="{{ route('user.uploads.create') }}" class="btn btn-primary me-2">
                                <i class="fas fa-upload"></i> Upload Putusan Baru
                            </a>
                            <a href="{{ route('user.uploads.history') }}" class="btn btn-secondary">
                                <i class="fas fa-history"></i> Lihat History Upload
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection