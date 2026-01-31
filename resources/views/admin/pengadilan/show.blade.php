@extends('layouts.app')

@section('title', 'Detail Pengadilan: ' . $pengadilan->nama)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-landmark"></i> Detail Pengadilan: {{ $pengadilan->nama }}
                    </h5>
                    <div>
                        <a href="{{ route('admin.pengadilan.edit', $pengadilan->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-primary">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-users"></i></h1>
                                    <h5>Total User</h5>
                                    <h3>{{ $stats['total_users'] }}</h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-success">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-file-upload"></i></h1>
                                    <h5>Total Upload</h5>
                                    <h3>{{ $stats['total_uploads'] }}</h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-info">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-gavel"></i></h1>
                                    <h5>Putusan Kasasi</h5>
                                    <h3>{{ $stats['uploads_kasasi'] }}</h3>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card text-white bg-warning">
                                <div class="card-body text-center">
                                    <h1><i class="fas fa-balance-scale"></i></h1>
                                    <h5>Putusan PK</h5>
                                    <h3>{{ $stats['uploads_pk'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pengadilan Info -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Informasi Pengadilan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="150">Kode</th>
                                            <td>
                                                <span class="badge bg-primary">{{ $pengadilan->kode }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Nama</th>
                                            <td>{{ $pengadilan->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Wilayah</th>
                                            <td>
                                                <span class="badge bg-info">{{ $pengadilan->wilayah }}</span>
                                            </td>
                                        </tr>
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
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $pengadilan->alamat ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat</th>
                                            <td>{{ $pengadilan->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Diupdate</th>
                                            <td>{{ $pengadilan->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Users di Pengadilan Ini</h6>
                                </div>
                                <div class="card-body">
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
                                                @foreach($pengadilan->users as $user)
                                                <tr>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $user->role == 'admin' ? 'warning' : 'info' }}">
                                                            {{ ucfirst($user->role) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <p class="text-muted text-center">Belum ada user di pengadilan ini</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Uploads -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Upload Terbaru dari Pengadilan Ini</h6>
                        </div>
                        <div class="card-body">
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
                                        @foreach($pengadilan->uploads()->latest()->limit(10)->get() as $upload)
                                        <tr>
                                            <td>{{ $upload->created_at->format('d/m/Y') }}</td>
                                            <td>{{ $upload->user->name }}</td>
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
                            @else
                            <p class="text-muted text-center">Belum ada upload dari pengadilan ini</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection