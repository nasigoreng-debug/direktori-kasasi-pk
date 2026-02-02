@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-tachometer-alt"></i> Admin Dashboard
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Stats Cards -->
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card text-white bg-primary">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Total Users</h6>
                                                <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                                            </div>
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="card text-white bg-success">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Total Upload</h6>
                                                <h2 class="mb-0">{{ $stats['total_uploads'] }}</h2>
                                            </div>
                                            <i class="fas fa-file-upload fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="card text-white bg-info">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Upload Hari Ini</h6>
                                                <h2 class="mb-0">{{ $stats['uploads_today'] }}</h2>
                                            </div>
                                            <i class="fas fa-calendar-day fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="card text-white bg-warning">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0">Total Pengadilan</h6>
                                                <h2 class="mb-0">{{ $stats['total_pengadilan'] }}</h2>
                                            </div>
                                            <i class="fas fa-landmark fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Second Row Stats -->
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Upload by Type</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="text-center p-3 border rounded bg-primary text-white">
                                                    <h4>{{ $stats['uploads_kasasi'] }}</h4>
                                                    <small>Putusan Kasasi</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-3 border rounded bg-warning text-white">
                                                    <h4>{{ $stats['uploads_pk'] }}</h4>
                                                    <small>Putusan PK</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Quick Actions</h6>
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('admin.uploads.index') }}" class="btn btn-primary">
                                                <i class="fas fa-file-upload"></i> Semua Upload
                                            </a>
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-info">
                                                <i class="fas fa-users"></i> Manage Users
                                            </a>
                                            <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-warning">
                                                <i class="fas fa-landmark"></i> Manage Pengadilan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Uploads -->
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-history"></i> Upload Terbaru
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Tanggal</th>
                                                        <th>User</th>
                                                        <th>Pengadilan</th>
                                                        <th>Jenis</th>
                                                        <th>Nomor Perkara</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($recentUploads as $upload)
                                                        <tr>
                                                            <td>{{ $upload->created_at->format('d/m H:i') }}</td>
                                                            <td>{{ $upload->user->name }}</td>
                                                            <td>{{ $upload->pengadilan->kode }}</td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $upload->jenis_putusan == 'kasasi' ? 'primary' : 'warning' }}">
                                                                    {{ strtoupper($upload->jenis_putusan) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <small>{{ $upload->nomor_perkara_pa }}</small>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Top Pengadilan -->
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="fas fa-chart-bar"></i> Top Pengadilan
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group">
                                            @foreach ($pengadilanStats as $pengadilan)
                                                <div
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>{{ $pengadilan->nama }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $pengadilan->kode }}</small>
                                                    </div>
                                                    <span
                                                        class="badge bg-primary rounded-pill">{{ $pengadilan->uploads_count }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
