@extends('layouts.app')

@section('title', 'Manajemen Pengadilan')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-landmark"></i> Manajemen Pengadilan Agama
                        <small class="text-muted">(26 Pengadilan di Jawa Barat)</small>
                    </h5>
                    <div>
                        <a href="{{ route('admin.pengadilan.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Pengadilan
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Filter by Wilayah -->
                    <div class="mb-3">
                        <form method="GET" class="row g-2">
                            <div class="col-md-4">
                                <select name="wilayah" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Wilayah</option>
                                    @foreach($wilayahs as $wilayah)
                                    <option value="{{ $wilayah }}" {{ request('wilayah') == $wilayah ? 'selected' : '' }}>
                                        {{ $wilayah }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Cari nama/kode..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary">Cari</button>
                                <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Kode</th>
                                    <th>Nama Pengadilan</th>
                                    <th>Wilayah</th>
                                    <th>Kelas</th>
                                    <th>Total User</th>
                                    <th>Total Upload</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pengadilans as $pengadilan)
                                <tr>
                                    <td>{{ $loop->iteration + ($pengadilans->currentPage() - 1) * $pengadilans->perPage() }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $pengadilan->kode }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $pengadilan->nama }}</strong>
                                        @if($pengadilan->alamat)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($pengadilan->alamat, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $pengadilan->wilayah }}</span>
                                    </td>
                                    <td>
                                        @if($pengadilan->kelas)
                                            <span class="badge bg-warning">{{ $pengadilan->kelas }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $pengadilan->users_count ?? $pengadilan->users()->count() }}</td>
                                    <td>{{ $pengadilan->uploads_count ?? $pengadilan->uploads()->count() }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.pengadilan.show', $pengadilan->id) }}" 
                                               class="btn btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.pengadilan.edit', $pengadilan->id) }}" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteModal{{ $pengadilan->id }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Modal Delete -->
                                <div class="modal fade" id="deleteModal{{ $pengadilan->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Hapus Pengadilan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Apakah Anda yakin ingin menghapus pengadilan <strong>{{ $pengadilan->nama }}</strong>?</p>
                                                <div class="alert alert-warning">
                                                    <strong>Detail:</strong><br>
                                                    Kode: {{ $pengadilan->kode }}<br>
                                                    Wilayah: {{ $pengadilan->wilayah }}<br>
                                                    Total User: {{ $pengadilan->users()->count() }}<br>
                                                    Total Upload: {{ $pengadilan->uploads()->count() }}
                                                </div>
                                                <div class="alert alert-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    <strong>PERHATIAN:</strong> Pengadilan yang sudah dihapus tidak dapat dikembalikan!
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <form action="{{ route('admin.pengadilan.destroy', $pengadilan->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $pengadilans->links() }}
                    </div>
                    
                    <!-- Summary -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Summary</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4>{{ $pengadilans->total() }}</h4>
                                                <small>Total Pengadilan</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4>{{ $wilayahs->count() }}</h4>
                                                <small>Wilayah</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4>{{ \App\Models\User::count() }}</h4>
                                                <small>Total User</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4>{{ \App\Models\Upload::count() }}</h4>
                                                <small>Total Upload</small>
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
    </div>
</div>
@endsection