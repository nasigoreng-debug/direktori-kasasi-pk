@extends('layouts.app')

@section('title', 'Admin - Semua Upload Putusan')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-upload"></i> Semua Upload Putusan
                            <small class="text-muted">(Admin View - Semua Pengadilan)</small>
                        </h5>
                        <div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- FILTER FORM UNTUK ADMIN -->
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <!-- Search Nomor Perkara -->
                                <div class="col-md-2 mb-2">
                                    <input type="text" name="search" class="form-control"
                                        value="{{ request('search') }}" placeholder="Cari nomor...">
                                </div>

                                <!-- Filter Pengadilan -->
                                <div class="col-md-2 mb-2">
                                    <select name="pengadilan_id" class="form-select">
                                        <option value="">Semua Pengadilan</option>
                                        @foreach ($pengadilans as $pengadilan)
                                            <option value="{{ $pengadilan->id }}"
                                                {{ request('pengadilan_id') == $pengadilan->id ? 'selected' : '' }}>
                                                {{ $pengadilan->kode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter User -->
                                <div class="col-md-2 mb-2">
                                    <select name="user_id" class="form-select">
                                        <option value="">Semua User</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Jenis -->
                                <div class="col-md-1 mb-2">
                                    <select name="jenis_putusan" class="form-select">
                                        <option value="">Semua Jenis</option>
                                        <option value="kasasi" {{ request('jenis_putusan') == 'kasasi' ? 'selected' : '' }}>
                                            Kasasi</option>
                                        <option value="pk" {{ request('jenis_putusan') == 'pk' ? 'selected' : '' }}>PK
                                        </option>
                                    </select>
                                </div>

                                <!-- Filter Status -->
                                <div class="col-md-1 mb-2">
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>
                                            Submitted</option>
                                        <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>
                                            Verified</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                            Rejected</option>
                                    </select>
                                </div>

                                <!-- Range Tanggal Putus -->
                                <div class="col-md-2 mb-2">
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ request('start_date') }}" placeholder="Dari Tanggal">
                                </div>

                                <div class="col-md-2 mb-2">
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ request('end_date') }}" placeholder="Sampai Tanggal">
                                </div>

                                <!-- Tombol -->
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.uploads.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- INFO FILTER AKTIF -->
                        @if (request()->hasAny(['search', 'pengadilan_id', 'user_id', 'jenis_putusan', 'status', 'start_date', 'end_date']))
                            <div class="alert alert-info mb-3">
                                <h6><i class="fas fa-filter"></i> Filter Aktif:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @if (request('search'))
                                        <span class="badge bg-primary">
                                            Pencarian: "{{ request('search') }}"
                                        </span>
                                    @endif

                                    @if (request('pengadilan_id'))
                                        @php $pengadilan = $pengadilans->firstWhere('id', request('pengadilan_id')) @endphp
                                        @if ($pengadilan)
                                            <span class="badge bg-info">
                                                Pengadilan: {{ $pengadilan->kode }}
                                            </span>
                                        @endif
                                    @endif

                                    @if (request('user_id'))
                                        @php $user = $users->firstWhere('id', request('user_id')) @endphp
                                        @if ($user)
                                            <span class="badge bg-success">
                                                User: {{ $user->name }}
                                            </span>
                                        @endif
                                    @endif

                                    @if (request('jenis_putusan'))
                                        <span class="badge bg-warning">
                                            Jenis: {{ ucfirst(request('jenis_putusan')) }}
                                        </span>
                                    @endif

                                    @if (request('status'))
                                        <span class="badge bg-secondary">
                                            Status: {{ ucfirst(request('status')) }}
                                        </span>
                                    @endif

                                    @if (request('start_date') && request('end_date'))
                                        <span class="badge bg-danger">
                                            Tanggal: {{ request('start_date') }} s/d {{ request('end_date') }}
                                        </span>
                                    @elseif(request('start_date'))
                                        <span class="badge bg-danger">
                                            Dari: {{ request('start_date') }}
                                        </span>
                                    @elseif(request('end_date'))
                                        <span class="badge bg-danger">
                                            Sampai: {{ request('end_date') }}
                                        </span>
                                    @endif

                                    <a href="{{ route('admin.uploads.index') }}"
                                        class="badge bg-dark text-decoration-none">
                                        Hapus Semua Filter
                                    </a>
                                </div>
                            </div>
                        @endif

                        <!-- STATS CARD -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body py-2">
                                        <div class="row text-center">
                                            <div class="col-md-2">
                                                <h5 class="mb-0">{{ $uploads->total() }}</h5>
                                                <small>Total Data</small>
                                            </div>
                                            <div class="col-md-2">
                                                <h5 class="mb-0">{{ $pengadilans->count() }}</h5>
                                                <small>Total Pengadilan</small>
                                            </div>
                                            <div class="col-md-2">
                                                <h5 class="mb-0">{{ $users->count() }}</h5>
                                                <small>Total User</small>
                                            </div>
                                            <div class="col-md-2">
                                                <h5 class="mb-0">
                                                    {{ \App\Models\Upload::where('jenis_putusan', 'kasasi')->count() }}
                                                </h5>
                                                <small>Total Kasasi</small>
                                            </div>
                                            <div class="col-md-2">
                                                <h5 class="mb-0">
                                                    {{ \App\Models\Upload::where('jenis_putusan', 'pk')->count() }}</h5>
                                                <small>Total PK</small>
                                            </div>
                                            <div class="col-md-2">
                                                <h5 class="mb-0">
                                                    {{ \App\Models\Upload::whereDate('created_at', today())->count() }}
                                                </h5>
                                                <small>Upload Hari Ini</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TABLE -->
                        @if ($uploads->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tanggal Putusan</th>
                                            <th>Pengadilan</th>
                                            <th>User</th>
                                            <th>Jenis</th>
                                            <th>Nomor Perkara</th>
                                            <th>Status</th>
                                            <th>Upload Pada</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($uploads as $upload)
                                            <tr>
                                                <td>{{ $loop->iteration + ($uploads->currentPage() - 1) * $uploads->perPage() }}
                                                </td>
                                                <td>{{ $upload->tanggal_putusan->format('d/m/Y') }}</td>
                                                <td>
                                                    <small>
                                                        <strong>{{ $upload->pengadilan->kode }}</strong>
                                                        <br>
                                                        <span class="text-muted">{{ $upload->pengadilan->wilayah }}</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small>
                                                        {{ $upload->user->name }}
                                                        <br>
                                                        <span class="text-muted">{{ $upload->user->email }}</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-{{ $upload->jenis_putusan == 'kasasi' ? 'info' : 'warning' }}">
                                                        {{ strtoupper($upload->jenis_putusan) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small>
                                                        <div><strong>PA:</strong> {{ $upload->nomor_perkara_pa }}</div>
                                                        <div class="text-muted">{{ $upload->nomor_perkara_kasasi }}</div>
                                                    </small>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'submitted' => 'primary',
                                                            'verified' => 'success',
                                                            'rejected' => 'danger',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="badge bg-{{ $statusColors[$upload->status] ?? 'secondary' }}">
                                                        {{ ucfirst($upload->status) }}
                                                    </span>
                                                    @if ($upload->catatan)
                                                        <br>
                                                        <small class="text-muted" title="{{ $upload->catatan }}">
                                                            <i class="fas fa-sticky-note"></i>
                                                        </small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>
                                                        {{ $upload->created_at->format('d/m/Y') }}
                                                        <br>
                                                        <span
                                                            class="text-muted">{{ $upload->created_at->format('H:i') }}</span>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('admin.uploads.preview', $upload->id) }}"
                                                            class="btn btn-info" target="_blank" title="Preview">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.uploads.download', $upload->id) }}"
                                                            class="btn btn-success" title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#statusModal{{ $upload->id }}"
                                                            title="Update Status">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal{{ $upload->id }}"
                                                            title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Update Status -->
                                            <div class="modal fade" id="statusModal{{ $upload->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Update Status Putusan</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('admin.uploads.update-status', $upload->id) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Status</label>
                                                                    <select name="status" class="form-select" required>
                                                                        <option value="submitted"
                                                                            {{ $upload->status == 'submitted' ? 'selected' : '' }}>
                                                                            Submitted</option>
                                                                        <option value="verified"
                                                                            {{ $upload->status == 'verified' ? 'selected' : '' }}>
                                                                            Verified</option>
                                                                        <option value="rejected"
                                                                            {{ $upload->status == 'rejected' ? 'selected' : '' }}>
                                                                            Rejected</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Catatan (Opsional)</label>
                                                                    <textarea name="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika perlu...">{{ $upload->catatan }}</textarea>
                                                                    <small class="text-muted">Catatan akan terlihat oleh
                                                                        user</small>
                                                                </div>
                                                                <div class="alert alert-info">
                                                                    <small>
                                                                        <i class="fas fa-info-circle"></i>
                                                                        <strong>Info Putusan:</strong><br>
                                                                        Pengadilan: {{ $upload->pengadilan->nama }}<br>
                                                                        User: {{ $upload->user->name }}<br>
                                                                        Nomor: {{ $upload->nomor_perkara_pa }}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">Update
                                                                    Status</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Modal Delete -->
                                            <div class="modal fade" id="deleteModal{{ $upload->id }}" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Hapus Putusan</h5>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Apakah Anda yakin ingin menghapus putusan ini?</p>
                                                            <div class="alert alert-warning">
                                                                <strong>Detail:</strong><br>
                                                                Pengadilan: {{ $upload->pengadilan->nama }}<br>
                                                                User: {{ $upload->user->name }}<br>
                                                                Nomor Perkara: {{ $upload->nomor_perkara_pa }}<br>
                                                                Tanggal: {{ $upload->tanggal_putusan->format('d/m/Y') }}
                                                            </div>
                                                            <div class="alert alert-danger">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                <strong>PERHATIAN:</strong> Putusan yang sudah dihapus tidak
                                                                dapat dikembalikan!
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Batal</button>
                                                            <form
                                                                action="{{ route('admin.uploads.destroy', $upload->id) }}"
                                                                method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Ya,
                                                                    Hapus</button>
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
                            @if ($uploads->hasPages())
                                <div class="mt-3">
                                    {{ $uploads->appends(request()->except('page'))->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                <h5>Belum ada data upload</h5>
                                <p class="text-muted">Tidak ada putusan yang diupload</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
