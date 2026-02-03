@extends('layouts.app')

@section('title', 'History Upload')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i> History Upload Putusan
                    </h5>
                    <a href="{{ route('user.uploads.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Upload Baru
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <select name="jenis_putusan" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="kasasi" {{ request('jenis_putusan') == 'kasasi' ? 'selected' : '' }}>Kasasi</option>
                                    <option value="pk" {{ request('jenis_putusan') == 'pk' ? 'selected' : '' }}>PK</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('user.uploads.history') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </form>

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

                    @if($uploads->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tanggal Upload</th>
                                    <th>Pengadilan</th>
                                    <th>Jenis</th>
                                    <th>Nomor Perkara</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($uploads as $upload)
                                <tr>
                                    <td>{{ $loop->iteration + ($uploads->currentPage() - 1) * $uploads->perPage() }}</td>
                                    <td>
                                        <small>
                                            {{ $upload->created_at->format('d/m/Y') }}<br>
                                            <span class="text-muted">{{ $upload->created_at->format('H:i') }}</span>
                                        </small>
                                    </td>
                                    <td>{{ $upload->pengadilan->nama }}</td>
                                    <td>
                                        <span class="badge bg-{{ $upload->jenis_putusan == 'kasasi' ? 'info' : 'warning' }}">
                                            {{ strtoupper($upload->jenis_putusan) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $upload->nomor_perkara_pa }}</small>
                                    </td>
                                    <td>
                                        @php
                                        $statusColors = [
                                        'submitted' => 'primary',
                                        'verified' => 'success',
                                        'rejected' => 'danger'
                                        ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$upload->status] ?? 'secondary' }}">
                                            {{ ucfirst($upload->status) }}
                                        </span>
                                        @if($upload->catatan)
                                        <br>
                                        <small class="text-muted" title="{{ $upload->catatan }}">
                                            <i class="fas fa-sticky-note"></i> Catatan admin
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- TOMBOL SHOW -->
                                            <a href="{{ route('user.uploads.show', $upload->id) }}"
                                                class="btn btn-primary" title="Detail">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            <a href="{{ route('user.uploads.preview', $upload->id) }}"
                                                class="btn btn-info" target="_blank" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('user.uploads.download', $upload->id) }}"
                                                class="btn btn-success" title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @if($upload->status !== 'verified')
                                            <a href="{{ route('user.uploads.edit', $upload->id) }}"
                                                class="btn btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $upload->id }}"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $uploads->appends(request()->except('page'))->links() }}
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                        <h5>Belum ada history upload</h5>
                        <p class="text-muted">Silakan upload putusan pertama Anda</p>
                        <a href="{{ route('user.uploads.create') }}" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload Putusan
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DELETE UNTUK SETIAP UPLOAD -->
@foreach($uploads as $upload)
@if($upload->status !== 'verified')
<div class="modal fade" id="deleteModal{{ $upload->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash me-2"></i> Hapus Putusan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                </div>

                <h6 class="text-center mb-3">Anda yakin ingin menghapus putusan ini?</h6>

                <div class="alert alert-info">
                    <strong>Detail Putusan:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Pengadilan:</strong> {{ $upload->pengadilan->nama }}</li>
                        <li><strong>Jenis:</strong> {{ strtoupper($upload->jenis_putusan) }}</li>
                        <li><strong>Nomor Perkara:</strong> {{ $upload->nomor_perkara_pa }}</li>
                        <li><strong>Tanggal Putusan:</strong> {{ $upload->tanggal_putusan->format('d/m/Y') }}</li>
                        <li><strong>Status:</strong>
                            <span class="badge bg-{{ $statusColors[$upload->status] ?? 'secondary' }}">
                                {{ ucfirst($upload->status) }}
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informasi:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Putusan akan dipindahkan ke <strong>Trash</strong></li>
                        <li>Anda dapat memulihkannya nanti dari menu Trash</li>
                        <li>Putusan di Trash akan otomatis terhapus setelah 30 hari</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form action="{{ route('user.uploads.destroy', $upload->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Ya, Hapus ke Trash
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection