@extends('layouts.app')

@section('title', 'Trash - Putusan Terhapus')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-trash"></i> Trash - Putusan Terhapus
                        <span class="badge bg-danger">{{ $uploads->total() }}</span>
                    </h5>
                    <div>
                        <a href="{{ route('user.upload.history') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali ke History
                        </a>
                        @if($uploads->total() > 0)
                        <button type="button" class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#emptyTrashModal">
                            <i class="fas fa-trash-alt"></i> Kosongkan Trash
                        </button>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($uploads->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Putusan di trash akan otomatis dihapus permanen setelah 30 hari.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Dihapus Pada</th>
                                    <th>Pengadilan</th>
                                    <th>Jenis</th>
                                    <th>Nomor Perkara</th>
                                    <th>Tanggal Putusan</th>
                                    <th>Alasan Penghapusan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($uploads as $upload)
                                <tr>
                                    <td>{{ $loop->iteration + ($uploads->currentPage() - 1) * $uploads->perPage() }}</td>
                                    <td>
                                        <small>
                                            {{ $upload->deleted_at->format('d/m/Y') }}<br>
                                            <span class="text-muted">{{ $upload->deleted_at->format('H:i') }}</span>
                                        </small>
                                    </td>
                                    <td>{{ $upload->pengadilan->kode }}</td>
                                    <td>
                                        <span class="badge bg-{{ $upload->jenis_putusan == 'kasasi' ? 'info' : 'warning' }}">
                                            {{ strtoupper($upload->jenis_putusan) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $upload->nomor_perkara_pa }}</small>
                                    </td>
                                    <td>{{ $upload->tanggal_putusan->format('d/m/Y') }}</td>
                                    <td>
                                        @if($upload->catatan)
                                            <span class="text-danger">
                                                <i class="fas fa-comment me-1"></i>
                                                {{ $upload->catatan }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-user me-1"></i>
                                                Dihapus oleh user
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- RESTORE BUTTON -->
                                            <button type="button" class="btn btn-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#restoreModal{{ $upload->id }}"
                                                    title="Pulihkan">
                                                <i class="fas fa-undo"></i>
                                            </button>

                                            <!-- PERMANENT DELETE BUTTON -->
                                            <button type="button" class="btn btn-danger" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#forceDeleteModal{{ $upload->id }}"
                                                    title="Hapus Permanen">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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
                        <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                        <h5>Trash kosong</h5>
                        <p class="text-muted">Tidak ada putusan yang terhapus</p>
                        <a href="{{ route('user.upload.history') }}" class="btn btn-primary">
                            <i class="fas fa-history"></i> Lihat History
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL RESTORE UNTUK SETIAP UPLOAD -->
@foreach($uploads as $upload)
<div class="modal fade" id="restoreModal{{ $upload->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-success">
                    <i class="fas fa-undo me-2"></i> Pulihkan Putusan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-undo fa-3x text-success"></i>
                </div>
                
                <h6 class="text-center mb-3">Pulihkan putusan ini?</h6>
                
                <div class="alert alert-info">
                    <strong>Detail Putusan:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Pengadilan:</strong> {{ $upload->pengadilan->nama }}</li>
                        <li><strong>Jenis:</strong> {{ strtoupper($upload->jenis_putusan) }}</li>
                        <li><strong>Nomor Perkara:</strong> {{ $upload->nomor_perkara_pa }}</li>
                        <li><strong>Tanggal Putusan:</strong> {{ $upload->tanggal_putusan->format('d/m/Y') }}</li>
                        <li><strong>Dihapus pada:</strong> {{ $upload->deleted_at->format('d/m/Y H:i') }}</li>
                    </ul>
                </div>
                
                <div class="alert alert-success">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Informasi:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Putusan akan dikembalikan ke <strong>History Upload</strong></li>
                        <li>Status akan kembali ke <strong>"submitted"</strong></li>
                        <li>Admin perlu memverifikasi ulang putusan ini</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form action="{{ route('user.upload.trash.restore', $upload->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-undo"></i> Ya, Pulihkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FORCE DELETE UNTUK SETIAP UPLOAD -->
<div class="modal fade" id="forceDeleteModal{{ $upload->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash-alt me-2"></i> Hapus Permanen
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-skull-crossbones fa-3x text-danger"></i>
                </div>
                
                <h6 class="text-center mb-3">HAPUS PERMANEN PUTUSAN INI?</h6>
                
                <div class="alert alert-danger">
                    <strong>PERINGATAN TINGGI:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Putusan akan <strong>DIHAPUS SELAMANYA</strong> dari database</li>
                        <li>File PDF juga akan <strong>DIHAPUS PERMANEN</strong> dari server</li>
                        <li><strong>TIDAK ADA</strong> cara untuk memulihkan data ini</li>
                        <li>Aksi ini <strong>TIDAK DAPAT DIBATALKAN</strong></li>
                    </ul>
                </div>
                
                <div class="alert alert-info">
                    <strong>Detail Putusan yang akan dihapus:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>Pengadilan:</strong> {{ $upload->pengadilan->nama }}</li>
                        <li><strong>Jenis:</strong> {{ strtoupper($upload->jenis_putusan) }}</li>
                        <li><strong>Nomor Perkara:</strong> {{ $upload->nomor_perkara_pa }}</li>
                        <li><strong>File:</strong> {{ basename($upload->file_path) }}</li>
                        <li><strong>Ukuran File:</strong> 
                            @php
                                $filePath = storage_path('app/public/' . $upload->file_path);
                                if(file_exists($filePath)) {
                                    echo number_format(filesize($filePath) / 1024, 2) . ' KB';
                                } else {
                                    echo 'File tidak ditemukan';
                                }
                            @endphp
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form action="{{ route('user.upload.trash.force-delete', $upload->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Ya, Hapus Permanen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

<!-- MODAL EMPTY TRASH -->
<div class="modal fade" id="emptyTrashModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-bomb me-2"></i> Kosongkan Trash
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-radiation fa-3x text-danger"></i>
                </div>
                
                <h4 class="text-center text-danger mb-4">PERINGATAN EKSTREM!</h4>
                
                <div class="alert alert-danger">
                    <strong>ANDA AKAN MENGHAPUS:</strong>
                    <ul class="mb-0 mt-2">
                        <li><strong>{{ $uploads->total() }} PUTUSAN</strong> secara permanen</li>
                        <li>Semua file PDF terkait</li>
                        <li>Semua data di database</li>
                        <li><strong>TIDAK ADA BACKUP</strong></li>
                        <li><strong>TIDAK BISA DIBATALKAN</strong></li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Saran:</strong>
                    <ul class="mb-0 mt-2">
                        <li>Pertimbangkan untuk memulihkan putusan penting terlebih dahulu</li>
                        <li>Pastikan tidak ada putusan yang masih diperlukan</li>
                        <li>Trash akan otomatis terhapus setelah 30 hari</li>
                    </ul>
                </div>
             </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <form action="{{ route('user.upload.trash.empty') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-bomb"></i> Ya, Kosongkan Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Animation for modal */
.modal.fade .modal-dialog {
    transform: translateY(-50px);
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: translateY(0);
}

/* Custom button styles */
.btn-danger {
    transition: all 0.3s ease;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.btn-success {
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
}
</style>
@endpush