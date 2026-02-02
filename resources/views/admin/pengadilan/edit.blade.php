{{--
    FORM EDIT PENGADILAN
    View ini menampilkan form untuk mengedit data pengadilan yang sudah ada.
    Layout: layouts.app
--}}

@extends('layouts.app')

{{-- Set judul halaman --}}
@section('title', 'Edit Pengadilan')

@section('content')
<div class="container">
    {{-- ============================================ --}}
    {{<!-- BARIS UTAMA: FORM EDIT PENGADILAN -->}}
    {{-- ============================================ --}}
    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- ============================================ --}}
            {{<!-- KARTU UTAMA FORM EDIT -->}}
            {{-- ============================================ --}}
            <div class="card">
                {{-- Header kartu dengan judul dan tombol kembali --}}
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Pengadilan: {{ $pengadilan->nama }}
                    </h5>
                    {{-- Tombol kembali ke halaman daftar pengadilan --}}
                    <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>

                {{-- Body kartu berisi form edit --}}
                <div class="card-body">
                    {{-- ============================================ --}}
                    {{<!-- FORM EDIT PENGADILAN -->}}
                    {{-- ============================================ --}}
                    <form action="{{ route('admin.pengadilan.update', $pengadilan->id) }}" method="POST">
                        {{-- CSRF Token untuk keamanan --}}
                        @csrf
                        {{-- Method spoofing untuk PUT request --}}
                        @method('PUT')

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
                                    value="{{ old('kode', $pengadilan->kode) }}"
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
                                    value="{{ old('nama', $pengadilan->nama) }}"
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
                                        {{ old('wilayah', $pengadilan->wilayah) == $wilayah ? 'selected' : '' }}>
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
                                        {{ old('kelas', $pengadilan->kelas) == $kelas ? 'selected' : '' }}>
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
                                rows="3">{{ old('alamat', $pengadilan->alamat) }}</textarea>
                            {{-- Error message untuk validasi --}}
                            @error('alamat')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        {{-- End Row 3 --}}

                        {{-- ============================================ --}}
                        {{<!-- TOMBOL AKSI FORM EDIT -->}}
                        {{-- ============================================ --}}
                        <div class="mb-3">
                            {{-- Tombol Simpan Perubahan --}}
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>

                            {{-- Tombol Batal (kembali ke list) --}}
                            <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>
                        {{-- End Tombol Aksi --}}
                    </form>
                    {{-- End Form --}}

                    {{-- ============================================ --}}
                    {{<!-- INFORMASI TAMBAHAN PENGADILAN -->}}
                    {{-- ============================================ --}}
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informasi Pengadilan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                {{-- Kolom Informasi Timestamp --}}
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Dibuat:</strong>
                                        {{ $pengadilan->created_at->format('d/m/Y H:i') }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Diupdate:</strong>
                                        {{ $pengadilan->updated_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                                {{-- End Kolom Timestamp --}}

                                {{-- Kolom Statistik --}}
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Total User:</strong>
                                        <span class="badge bg-info">
                                            {{ $pengadilan->users()->count() }}
                                        </span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Total Upload:</strong>
                                        <span class="badge bg-success">
                                            {{ $pengadilan->uploads()->count() }}
                                        </span>
                                    </p>
                                </div>
                                {{-- End Kolom Statistik --}}
                            </div>
                        </div>
                    </div>
                    {{-- End Informasi Tambahan --}}
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

        // ============================================
        // CEK PERUBAHAN DATA
        // ============================================
        let originalData = {
            kode: '{{ $pengadilan->kode }}',
            nama: '{{ $pengadilan->nama }}',
            wilayah: '{{ $pengadilan->wilayah }}',
            kelas: '{{ $pengadilan->kelas ?? "" }}',
            alamat: '{{ addslashes($pengadilan->alamat) }}'
        };

        form.addEventListener('submit', function(e) {
            const currentData = {
                kode: kodeInput.value.trim(),
                nama: document.querySelector('input[name="nama"]').value.trim(),
                wilayah: document.querySelector('select[name="wilayah"]').value,
                kelas: document.querySelector('select[name="kelas"]').value,
                alamat: document.querySelector('textarea[name="alamat"]').value.trim()
            };

            // Cek jika ada perubahan
            let hasChanges = false;
            for (const key in currentData) {
                if (currentData[key] !== originalData[key]) {
                    hasChanges = true;
                    break;
                }
            }

            if (!hasChanges) {
                e.preventDefault();
                if (confirm('Tidak ada perubahan data. Apakah Anda ingin membatalkan?')) {
                    window.location.href = '{{ route("admin.pengadilan.index") }}';
                }
            }
        });
    });
</script>
@endpush

{{-- ============================================ --}}
{{<!-- STYLE CSS TAMBAHAN -->}}
{{-- ============================================ --}}
@push('styles')
<style>
    /* Style khusus untuk form edit pengadilan */
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .text-danger {
        color: #dc3545;
    }

    /* Style untuk badge statistik */
    .badge {
        font-size: 0.9em;
        padding: 0.25em 0.6em;
    }

    /* Card informasi tambahan */
    .card.mt-4 {
        border: 1px solid rgba(0, 0, 0, .125);
    }

    .card-header.bg-light {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid rgba(0, 0, 0, .125);
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

        .card-body .row>div {
            margin-bottom: 1rem;
        }

        .card.mt-4 .row>div {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush