{{--
    FORM TAMBAH PENGADILAN BARU
    View ini menampilkan form untuk menambahkan pengadilan baru.
    Layout: layouts.app
--}}

@extends('layouts.app')

{{-- Set judul halaman --}}
@section('title', 'Tambah Pengadilan Baru')

@section('content')
<div class="container">
    {{-- ============================================ --}}
    {{<!-- BARIS UTAMA: FORM TAMBAH PENGADILAN -->}}
    {{-- ============================================ --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- ============================================ --}}
            {{<!-- KARTU UTAMA FORM -->}}
            {{-- ============================================ --}}
            <div class="card">
                {{-- Header kartu dengan judul dan tombol kembali --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus"></i> Tambah Pengadilan Baru
                    </h5>
                    {{-- Tombol kembali ke halaman daftar pengadilan --}}
                    <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                {{-- Body kartu berisi form --}}
                <div class="card-body">
                    {{-- ============================================ --}}
                    {{<!-- FORM TAMBAH PENGADILAN -->}}
                    {{-- ============================================ --}}
                    <form action="{{ route('admin.pengadilan.store') }}" method="POST">
                        {{-- CSRF Token untuk keamanan --}}
                        @csrf

                        {{-- ============================================ --}}
                        {{<!-- ROW 1: KODE DAN NAMA PENGADILAN -->}}
                        {{-- ============================================ --}}
                        <div class="row mb-3">
                            {{-- Kolom Kode Pengadilan --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Kode Pengadilan <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="kode"
                                    class="form-control @error('kode') is-invalid @enderror"
                                    placeholder="Contoh: PA-BDG"
                                    value="{{ old('kode') }}"
                                    required
                                    maxlength="10">
                                {{-- Helper text untuk panduan pengguna --}}
                                <small class="text-muted">
                                    Kode unik untuk pengadilan (max 10 karakter)
                                </small>
                                {{-- Error message untuk validasi --}}
                                @error('kode')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- End Kolom Kode --}}

                            {{-- Kolom Nama Pengadilan --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Nama Pengadilan <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="nama"
                                    class="form-control @error('nama') is-invalid @enderror"
                                    placeholder="Contoh: Pengadilan Agama Bandung"
                                    value="{{ old('nama') }}"
                                    required>
                                {{-- Error message untuk validasi --}}
                                @error('nama')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- End Kolom Nama --}}
                        </div>
                        {{-- End Row 1 --}}

                        {{-- ============================================ --}}
                        {{<!-- ROW 2: WILAYAH DAN KELAS -->}}
                        {{-- ============================================ --}}
                        <div class="row mb-3">
                            {{-- Kolom Wilayah --}}
                            <div class="col-md-6">
                                <label class="form-label">
                                    Wilayah <span class="text-danger">*</span>
                                </label>
                                <select name="wilayah"
                                    class="form-select @error('wilayah') is-invalid @enderror"
                                    required>
                                    <option value="">Pilih Wilayah</option>
                                    {{-- Loop melalui daftar wilayah dari controller --}}
                                    @foreach($wilayahs as $wilayah)
                                    <option value="{{ $wilayah }}"
                                        {{ old('wilayah') == $wilayah ? 'selected' : '' }}>
                                        {{ $wilayah }}
                                    </option>
                                    @endforeach
                                </select>
                                {{-- Error message untuk validasi --}}
                                @error('wilayah')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- End Kolom Wilayah --}}

                            {{-- Kolom Kelas (Opsional) --}}
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <select name="kelas"
                                    class="form-select @error('kelas') is-invalid @enderror">
                                    <option value="">Pilih Kelas (Opsional)</option>
                                    {{-- Loop melalui daftar kelas dari controller --}}
                                    @foreach($kelasOptions as $kelas)
                                    <option value="{{ $kelas }}"
                                        {{ old('kelas') == $kelas ? 'selected' : '' }}>
                                        {{ $kelas ?? 'Tidak Ada Kelas' }}
                                    </option>
                                    @endforeach
                                </select>
                                {{-- Error message untuk validasi --}}
                                @error('kelas')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- End Kolom Kelas --}}
                        </div>
                        {{-- End Row 2 --}}

                        {{-- ============================================ --}}
                        {{<!-- ROW 3: ALAMAT LENGKAP -->}}
                        {{-- ============================================ --}}
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat"
                                class="form-control @error('alamat') is-invalid @enderror"
                                rows="3"
                                placeholder="Alamat lengkap pengadilan...">{{ old('alamat') }}</textarea>
                            {{-- Error message untuk validasi --}}
                            @error('alamat')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- End Row 3 --}}

                        {{-- ============================================ --}}
                        {{<!-- TOMBOL AKSI FORM -->}}
                        {{-- ============================================ --}}
                        <div class="mb-3">
                            {{-- Tombol Simpan --}}
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengadilan
                            </button>

                            {{-- Tombol Reset Form --}}
                            <button type="reset" class="btn btn-secondary">
                                Reset Form
                            </button>
                        </div>
                        {{-- End Tombol Aksi --}}
                    </form>
                    {{-- End Form --}}
                </div>
                {{-- End Card Body --}}
            </div>
            {{-- End Card --}}
        </div>
        {{-- End Column --}}
    </div>
    {{-- End Row --}}
</div>
{{-- End Container --}}
@endsection

{{-- ============================================ --}}
{{<!-- SCRIPT JAVASCRIPT TAMBAHAN -->}}
{{-- ============================================ --}}
@push('scripts')
<script>
    // ============================================
    // VALIDASI FORM CLIENT-SIDE
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');

        form.addEventListener('submit', function(event) {
            let isValid = true;
            const errors = [];

            // Validasi Kode Pengadilan
            const kodeInput = document.querySelector('input[name="kode"]');
            if (kodeInput.value.trim() === '') {
                isValid = false;
                errors.push('Kode pengadilan harus diisi');
            }

            // Validasi Nama Pengadilan
            const namaInput = document.querySelector('input[name="nama"]');
            if (namaInput.value.trim() === '') {
                isValid = false;
                errors.push('Nama pengadilan harus diisi');
            }

            // Validasi Wilayah
            const wilayahSelect = document.querySelector('select[name="wilayah"]');
            if (wilayahSelect.value === '') {
                isValid = false;
                errors.push('Wilayah harus dipilih');
            }

            // Tampilkan error jika ada
            if (!isValid) {
                event.preventDefault();
                alert('Silakan periksa form:\n' + errors.join('\n'));
            }
        });

        // ============================================
        // AUTO-FORMAT KODE PENGADILAN
        // ============================================
        const kodeInput = document.querySelector('input[name="kode"]');
        kodeInput.addEventListener('input', function(e) {
            // Ubah ke uppercase
            this.value = this.value.toUpperCase();

            // Hapus karakter khusus
            this.value = this.value.replace(/[^A-Z0-9\-]/g, '');
        });
    });
</script>
@endpush

{{-- ============================================ --}}
{{<!-- STYLE CSS TAMBAHAN -->}}
{{-- ============================================ --}}
@push('styles')
<style>
    /* Style khusus untuk form pengadilan */
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .text-danger {
        color: #dc3545;
    }

    /* Style untuk asterisk merah */
    .form-label .text-danger {
        font-size: 1.2em;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .card-header .btn {
            margin-top: 0.5rem;
            align-self: flex-end;
        }
    }
</style>
@endpush