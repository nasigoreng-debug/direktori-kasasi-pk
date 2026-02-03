@extends('layouts.app')

@section('title', 'Detail Putusan: ' . $upload->nomor_perkara_pa)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt"></i> Detail Putusan: {{ $upload->nomor_perkara_pa }}
                    </h5>
                    <div>
                        <a href="{{ route('admin.uploads.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informasi Putusan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Informasi Putusan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="200">Nomor Perkara PA</th>
                                            <td><strong>{{ $upload->nomor_perkara_pa }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Perkara Banding</th>
                                            <td>{{ $upload->nomor_perkara_banding ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Perkara Kasasi</th>
                                            <td>{{ $upload->nomor_perkara_kasasi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nomor Perkara PK</th>
                                            <td>{{ $upload->nomor_perkara_pk ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Putusan</th>
                                            <td>
                                                <span class="badge bg-{{ $upload->jenis_putusan == 'kasasi' ? 'primary' : 'warning' }}">
                                                    {{ strtoupper($upload->jenis_putusan) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Putusan</th>
                                            <td>{{ $upload->tanggal_putusan->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                @php
                                                $statusColors = [
                                                'submitted' => 'primary',
                                                'verified' => 'success',
                                                'rejected' => 'danger',
                                                ];
                                                @endphp
                                                <span class="badge bg-{{ $statusColors[$upload->status] ?? 'secondary' }}">
                                                    {{ ucfirst($upload->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Catatan</th>
                                            <td>{{ $upload->catatan ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Informasi Pengadilan & User</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="150">Pengadilan</th>
                                            <td>
                                                {{ $upload->pengadilan->nama }}<br>
                                                <small class="text-muted">{{ $upload->pengadilan->kode }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>User</th>
                                            <td>
                                                {{ $upload->user->name }}<br>
                                                <small class="text-muted">{{ $upload->user->email }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Upload Pada</th>
                                            <td>
                                                {{ $upload->created_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>File</th>
                                            <td>
                                                <a href="{{ route('admin.uploads.download', $upload->id) }}"
                                                    class="btn btn-success btn-sm">
                                                    <i class="fas fa-download"></i> Download File
                                                </a>
                                                <a href="{{ route('admin.uploads.preview', $upload->id) }}"
                                                    target="_blank" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i> Preview
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <!-- Update Status Button -->
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#statusModal">
                                    <i class="fas fa-edit"></i> Update Status
                                </button>

                                <!-- Delete Button -->
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Status -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Putusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.uploads.update-status', $upload->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="submitted" {{ $upload->status == 'submitted' ? 'selected' : '' }}>
                                Submitted
                            </option>
                            <option value="verified" {{ $upload->status == 'verified' ? 'selected' : '' }}>
                                Verified
                            </option>
                            <option value="rejected" {{ $upload->status == 'rejected' ? 'selected' : '' }}>
                                Rejected
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control" rows="3">{{ $upload->catatan }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Putusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus putusan ini?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Putusan yang sudah dihapus tidak dapat dikembalikan!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.uploads.destroy', $upload->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection