@extends('layouts.app')

@section('title', 'Upload Putusan Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-upload"></i> Upload Putusan Baru
                    </h5>
                </div>

                <div class="card-body">
                    {{-- ✅ TAMPILKAN ERROR VALIDASI --}}
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Validasi Gagal!</h5>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- ✅ FORM YANG BENAR --}}
                    <form id="uploadForm" action="{{ route('user.upload.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pengadilan_id" class="form-label">Pengadilan *</label>
                                <select name="pengadilan_id" id="pengadilan_id" class="form-select" required>
                                    <option value="">Pilih Pengadilan</option>
                                    @foreach($pengadilans as $pengadilan)
                                    <option value="{{ $pengadilan->id }}" {{ old('pengadilan_id') == $pengadilan->id ? 'selected' : '' }}>
                                        {{ $pengadilan->kode }} - {{ $pengadilan->nama }} ({{ $pengadilan->wilayah }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('pengadilan_id')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="jenis_putusan" class="form-label">Jenis Putusan *</label>
                                <select name="jenis_putusan" id="jenis_putusan" class="form-select" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="kasasi" {{ old('jenis_putusan') == 'kasasi' ? 'selected' : '' }}>Kasasi</option>
                                    <option value="pk" {{ old('jenis_putusan') == 'pk' ? 'selected' : '' }}>Peninjauan Kembali (PK)</option>
                                </select>
                                @error('jenis_putusan')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="nomor_perkara_pa" class="form-label">Nomor Perkara PA *</label>
                                <input type="text" name="nomor_perkara_pa" id="nomor_perkara_pa"
                                    class="form-control" value="{{ old('nomor_perkara_pa') }}"
                                    placeholder="Contoh: 123/Pdt.G/2023/PA.Bdg" required>
                                @error('nomor_perkara_pa')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="nomor_perkara_banding" class="form-label">Nomor Perkara Banding</label>
                                <input type="text" name="nomor_perkara_banding" id="nomor_perkara_banding"
                                    class="form-control" value="{{ old('nomor_perkara_banding') }}"
                                    placeholder="(Opsional)">
                                @error('nomor_perkara_banding')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="nomor_perkara_kasasi" class="form-label">Nomor Perkara Kasasi</label>
                                <input type="text" name="nomor_perkara_kasasi" id="nomor_perkara_kasasi"
                                    class="form-control" value="{{ old('nomor_perkara_kasasi') }}"
                                    placeholder="(Untuk putusan kasasi)">
                                @error('nomor_perkara_kasasi')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label for="nomor_perkara_pk" class="form-label">Nomor Perkara PK</label>
                                <input type="text" name="nomor_perkara_pk" id="nomor_perkara_pk"
                                    class="form-control" value="{{ old('nomor_perkara_pk') }}"
                                    placeholder="(Untuk putusan PK)">
                                @error('nomor_perkara_pk')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_putusan" class="form-label">Tanggal Putusan *</label>
                                <input type="date" name="tanggal_putusan" id="tanggal_putusan"
                                    class="form-control" value="{{ old('tanggal_putusan') }}" required>
                                @error('tanggal_putusan')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="file_putusan" class="form-label">File Putusan (PDF) *</label>
                                <input type="file" name="file_putusan" id="file_putusan"
                                    class="form-control" accept=".pdf" required>
                                <small class="text-muted">Maksimal 10MB, format PDF</small>
                                @error('file_putusan')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                                {{-- Preview file --}}
                                <div id="filePreview" class="mt-2 d-none">
                                    <div class="alert alert-info p-2">
                                        <i class="fas fa-file-pdf text-danger"></i>
                                        <span id="fileName"></span>
                                        <small id="fileSize" class="text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Petunjuk Upload:</h6>
                            <ol class="mb-0">
                                <li>Pastikan data yang diisi sesuai dengan putusan</li>
                                <li>File harus dalam format PDF</li>
                                <li>Maksimal ukuran file: 10MB</li>
                                <li>Putusan akan diverifikasi oleh admin sebelum ditampilkan</li>
                                <li>Status upload dapat dilihat di menu "History"</li>
                            </ol>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('user.upload.history') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-upload"></i> Upload Putusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Script untuk menampilkan field sesuai jenis putusan
    document.getElementById('jenis_putusan').addEventListener('change', function() {
        const jenis = this.value;
        const kasasiField = document.getElementById('nomor_perkara_kasasi');
        const pkField = document.getElementById('nomor_perkara_pk');

        if (jenis === 'kasasi') {
            kasasiField.required = true;
            pkField.required = false;
            pkField.value = '';
            kasasiField.placeholder = '(Wajib untuk kasasi)';
            pkField.placeholder = '(Tidak perlu)';
        } else if (jenis === 'pk') {
            kasasiField.required = false;
            kasasiField.value = '';
            pkField.required = true;
            kasasiField.placeholder = '(Tidak perlu)';
            pkField.placeholder = '(Wajib untuk PK)';
        } else {
            kasasiField.required = false;
            pkField.required = false;
            kasasiField.value = '';
            pkField.value = '';
            kasasiField.placeholder = '(Untuk putusan kasasi)';
            pkField.placeholder = '(Untuk putusan PK)';
        }
    });

    // Preview file yang dipilih
    document.getElementById('file_putusan').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');

        if (file) {
            preview.classList.remove('d-none');
            fileName.textContent = file.name;
            fileSize.textContent = ' (' + formatFileSize(file.size) + ')';

            // Validasi client-side
            if (file.type !== 'application/pdf') {
                alert('Error: File harus dalam format PDF!');
                e.target.value = '';
                preview.classList.add('d-none');
                return;
            }

            if (file.size > 10 * 1024 * 1024) { // 10MB
                alert('Error: Ukuran file maksimal 10MB!');
                e.target.value = '';
                preview.classList.add('d-none');
                return;
            }
        } else {
            preview.classList.add('d-none');
        }
    });

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form validation sebelum submit
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        // JANGAN GUNAKAN e.preventDefault() kecuali untuk validasi AJAX

        const pengadilan = document.getElementById('pengadilan_id');
        const jenis = document.getElementById('jenis_putusan');
        const nomorPa = document.getElementById('nomor_perkara_pa');
        const tanggal = document.getElementById('tanggal_putusan');
        const file = document.getElementById('file_putusan');

        let isValid = true;
        let errorMessage = '';

        // Validasi required fields
        if (!pengadilan.value) {
            isValid = false;
            errorMessage += '- Pilih pengadilan\n';
        }

        if (!jenis.value) {
            isValid = false;
            errorMessage += '- Pilih jenis putusan\n';
        }

        if (!nomorPa.value.trim()) {
            isValid = false;
            errorMessage += '- Isi nomor perkara PA\n';
        }

        if (!tanggal.value) {
            isValid = false;
            errorMessage += '- Pilih tanggal putusan\n';
        }

        if (!file.files.length) {
            isValid = false;
            errorMessage += '- Pilih file PDF\n';
        }

        // Validasi conditional fields
        if (jenis.value === 'kasasi' && !document.getElementById('nomor_perkara_kasasi').value.trim()) {
            isValid = false;
            errorMessage += '- Nomor perkara kasasi wajib untuk putusan kasasi\n';
        }

        if (jenis.value === 'pk' && !document.getElementById('nomor_perkara_pk').value.trim()) {
            isValid = false;
            errorMessage += '- Nomor perkara PK wajib untuk putusan PK\n';
        }

        if (!isValid) {
            e.preventDefault(); // Hentikan submission
            alert('Mohon lengkapi data:\n' + errorMessage);
            return false;
        }

        // Disable button untuk mencegah double submit
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';

        return true;
    });

    // Set initial state based on old value (jika ada error dan kembali ke form)
    document.addEventListener('DOMContentLoaded', function() {
        const jenisSelect = document.getElementById('jenis_putusan');
        if (jenisSelect.value) {
            jenisSelect.dispatchEvent(new Event('change'));
        }

        // Tampilkan file yang sudah dipilih sebelumnya (jika ada)
        const fileInput = document.getElementById('file_putusan');
        if (fileInput.files.length > 0) {
            fileInput.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection