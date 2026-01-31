@extends('layouts.app')

@section('title', 'Tambah Pengadilan Baru')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-plus"></i> Tambah Pengadilan Baru
                    </h5>
                    <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pengadilan.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Pengadilan <span class="text-danger">*</span></label>
                                <input type="text" name="kode" class="form-control" 
                                       placeholder="Contoh: PA-BDG" required maxlength="10">
                                <small class="text-muted">Kode unik untuk pengadilan (max 10 karakter)</small>
                                @error('kode')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Pengadilan <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" 
                                       placeholder="Contoh: Pengadilan Agama Bandung" required>
                                @error('nama')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Wilayah <span class="text-danger">*</span></label>
                                <select name="wilayah" class="form-select" required>
                                    <option value="">Pilih Wilayah</option>
                                    @foreach($wilayahs as $wilayah)
                                    <option value="{{ $wilayah }}" {{ old('wilayah') == $wilayah ? 'selected' : '' }}>
                                        {{ $wilayah }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('wilayah')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" class="form-select">
                                    <option value="">Pilih Kelas (Opsional)</option>
                                    @foreach($kelasOptions as $kelas)
                                    <option value="{{ $kelas }}" {{ old('kelas') == $kelas ? 'selected' : '' }}>
                                        {{ $kelas ?? 'Tidak Ada Kelas' }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('kelas')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="3" 
                                      placeholder="Alamat lengkap pengadilan...">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengadilan
                            </button>
                            <button type="reset" class="btn btn-secondary">Reset Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection