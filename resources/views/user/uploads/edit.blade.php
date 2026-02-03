@extends('layouts.app')

@section('title', 'Edit Putusan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Putusan
                        <small class="text-muted">#{{ $upload->id }}</small>
                    </h5>
                    <a href="{{ route('user.uploads.history') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    {{-- Status Info --}}
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle"></i>
                        <strong>Status saat ini:</strong>
                        <span class="badge bg-{{ $upload->status == 'submitted' ? 'primary' : ($upload->status == 'verified' ? 'success' : 'danger') }}">
                            {{ ucfirst($upload->status) }}
                        </span>
                        @if($upload->status === 'verified')
                        <br><small class="text-danger">Putusan yang sudah diverifikasi tidak dapat diedit.</small>
                        @endif
                    </div>

                    {{-- Error Messages --}}
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Validasi Gagal!</h5>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Form --}}
                    <form action="{{ route('user.uploads.update', $upload->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Pengadilan --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Pengadilan <span class="text-danger">*</span></label>
                                <select name="pengadilan_id" id="pengadilan_id" class="form-select" required>
                                    <option value="">Pilih Pengadilan</option>
                                    @foreach($pengadilans as $pengadilan)
                                    <option value="{{ $pengadilan->id }}"
                                        {{ old('pengadilan_id', $upload->pengadilan_id) == $pengadilan->id ? 'selected' : '' }}>
                                        {{ $pengadilan->kode }} - {{ $pengadilan->nama }} ({{ $pengadilan->wilayah }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('pengadilan_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jenis Putusan <span class="text-danger">*</span></label>
                                <select name="jenis_putusan" id="jenis_putusan" class="form-select" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="kasasi" {{ old('jenis_putusan', $upload->jenis_putusan) == 'kasasi' ? 'selected' : '' }}>Kasasi</option>
                                    <option value="pk" {{ old('jenis_putusan', $upload->jenis_putusan) == 'pk' ? 'selected' : '' }}>Peninjauan Kembali (PK)</option>
                                </select>
                                @error('jenis_putusan')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Nomor Perkara --}}
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label class="form-label">Nomor Perkara PA <span class="text-danger">*</span></label>
                                <input type="text" name="nomor_perkara_pa" class="form-control"
                                    value="{{ old('nomor_perkara_pa', $upload->nomor_perkara_pa) }}"
                                    placeholder="Contoh: 123/Pdt.G/2023/PA.BDG" required>
                                @error('nomor_perkara_pa')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Nomor Perkara Banding</label>
                                <input type="text" name="nomor_perkara_banding" class="form-control"
                                    value="{{ old('nomor_perkara_banding', $upload->nomor_perkara_banding) }}"
                                    placeholder="(Opsional)">
                                @error('nomor_perkara_banding')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" id="label_kasasi">Nomor Perkara Kasasi</label>
                                <input type="text" name="nomor_perkara_kasasi" id="nomor_perkara_kasasi" class="form-control"
                                    value="{{ old('nomor_perkara_kasasi', $upload->nomor_perkara_kasasi) }}"
                                    placeholder="(Untuk kasasi)">
                                @error('nomor_perkara_kasasi')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="kasasi_note">Untuk putusan kasasi (opsional)</small>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label" id="label_pk">Nomor Perkara PK</label>
                                <input type="text" name="nomor_perkara_pk" id="nomor_perkara_pk" class="form-control"
                                    value="{{ old('nomor_perkara_pk', $upload->nomor_perkara_pk) }}"
                                    placeholder="(Untuk PK)">
                                @error('nomor_perkara_pk')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted" id="pk_note">Untuk putusan PK (opsional)</small>
                            </div>
                        </div>

                        {{-- Tanggal & File --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Putusan <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_putusan" class="form-control"
                                    value="{{ old('tanggal_putusan', $upload->tanggal_putusan->format('Y-m-d')) }}" required
                                    max="{{ date('Y-m-d') }}">
                                @error('tanggal_putusan')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">File Putusan (PDF)</label>
                                <div class="mb-2">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        File saat ini:
                                        <a href="{{ route('user.uploads.preview', $upload->id) }}" target="_blank" class="text-decoration-none">
                                            {{ $upload->original_filename }}
                                        </a>
                                        ({{ number_format($upload->file_size / 1024, 1) }} KB)
                                    </div>
                                </div>
                                <input type="file" name="file_putusan" class="form-control" accept=".pdf">
                                @error('file_putusan')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    Kosongkan jika tidak ingin mengganti file.
                                    Maksimal ukuran: 10MB, format PDF
                                </small>
                            </div>
                        </div>

                        {{-- Warning --}}
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Perhatian:</h6>
                            <ol class="mb-0">
                                <li>File saat ini: <strong>{{ $upload->original_filename }}</strong></li>
                                <li>Jika upload file baru, file lama akan diganti</li>
                                <li>Status akan direset ke "submitted" dan perlu verifikasi ulang oleh admin</li>
                                <li>Pastikan data yang diedit sudah benar</li>
                            </ol>
                        </div>

                        {{-- Buttons --}}
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary" {{ $upload->status === 'verified' ? 'disabled' : '' }}>
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="{{ route('user.uploads.history') }}" class="btn btn-secondary">
                                    Batal
                                </a>
                            </div>

                            @if($upload->status !== 'verified')
                            <button type="button" class="btn btn-danger"
                                data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
@if($upload->status !== 'verified')
<div class="modal fade" id="deleteModal" tabindex="-1">
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
                            <span class="badge bg-{{ $upload->status == 'submitted' ? 'primary' : 'secondary' }}">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('jenis_putusan');
        const kasasiInput = document.getElementById('nomor_perkara_kasasi');
        const kasasiLabel = document.getElementById('label_kasasi');
        const kasasiNote = document.getElementById('kasasi_note');
        const pkInput = document.getElementById('nomor_perkara_pk');
        const pkLabel = document.getElementById('label_pk');
        const pkNote = document.getElementById('pk_note');

        function updateFields() {
            if (jenisSelect.value === 'kasasi') {
                kasasiInput.required = true;
                kasasiLabel.innerHTML = 'Nomor Perkara Kasasi <span class="text-danger">*</span>';
                kasasiNote.innerHTML = 'Wajib diisi untuk putusan kasasi';
                kasasiNote.classList.remove('text-muted');
                kasasiNote.classList.add('text-danger');

                pkInput.required = false;
                pkLabel.innerHTML = 'Nomor Perkara PK';
                pkNote.innerHTML = 'Hanya untuk putusan PK (tidak perlu)';
                pkNote.classList.remove('text-danger');
                pkNote.classList.add('text-muted');

            } else if (jenisSelect.value === 'pk') {
                kasasiInput.required = false;
                kasasiLabel.innerHTML = 'Nomor Perkara Kasasi';
                kasasiNote.innerHTML = 'Hanya untuk putusan kasasi (tidak perlu)';
                kasasiNote.classList.remove('text-danger');
                kasasiNote.classList.add('text-muted');

                pkInput.required = true;
                pkLabel.innerHTML = 'Nomor Perkara PK <span class="text-danger">*</span>';
                pkNote.innerHTML = 'Wajib diisi untuk putusan PK';
                pkNote.classList.remove('text-muted');
                pkNote.classList.add('text-danger');

            } else {
                kasasiInput.required = false;
                kasasiLabel.innerHTML = 'Nomor Perkara Kasasi';
                kasasiNote.innerHTML = 'Untuk putusan kasasi (opsional)';
                kasasiNote.classList.remove('text-danger');
                kasasiNote.classList.add('text-muted');

                pkInput.required = false;
                pkLabel.innerHTML = 'Nomor Perkara PK';
                pkNote.innerHTML = 'Untuk putusan PK (opsional)';
                pkNote.classList.remove('text-danger');
                pkNote.classList.add('text-muted');
            }
        }

        // Initial update
        updateFields();

        // Update on change
        jenisSelect.addEventListener('change', updateFields);

        // File size validation
        const fileInput = document.querySelector('input[name="file_putusan"]');
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files[0]) {
                    // Check file type
                    if (this.files[0].type !== 'application/pdf') {
                        alert('File harus dalam format PDF!');
                        this.value = '';
                        return;
                    }

                    // Check file size
                    if (this.files[0].size > maxSize) {
                        alert('Ukuran file terlalu besar. Maksimal 10MB.');
                        this.value = '';
                    }
                }
            });
        }

        // Disable form jika status verified
        const status = "{{ $upload->status }}";
        if (status === 'verified') {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea, button[type="submit"]');
            inputs.forEach(input => {
                if (input.type !== 'hidden') {
                    input.disabled = true;
                }
            });

            // Show message
            const disabledMsg = document.createElement('div');
            disabledMsg.className = 'alert alert-danger mt-3';
            disabledMsg.innerHTML = '<i class="fas fa-ban"></i> Putusan yang sudah diverifikasi tidak dapat diedit.';
            form.appendChild(disabledMsg);
        }
    });
</script>
@endpush
@endsection