@extends('layouts.app')

@section('title', 'Edit Pengadilan')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit"></i> Edit Pengadilan: {{ $pengadilan->nama }}
                    </h5>
                    <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pengadilan.update', $pengadilan->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kode Pengadilan <span class="text-danger">*</span></label>
                                <input type="text" name="kode" class="form-control" 
                                       value="{{ old('kode', $pengadilan->kode) }}" required maxlength="10">
                                <small class="text-muted">Kode unik untuk pengadilan</small>
                                @error('kode')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Pengadilan <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" 
                                       value="{{ old('nama', $pengadilan->nama) }}" required>
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
                                    <option value="{{ $wilayah }}" 
                                            {{ old('wilayah', $pengadilan->wilayah) == $wilayah ? 'selected' : '' }}>
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
                                    <option value="{{ $kelas }}" 
                                            {{ old('kelas', $pengadilan->kelas) == $kelas ? 'selected' : '' }}>
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
                            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $pengadilan->alamat) }}</textarea>
                            @error('alamat')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.pengadilan.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                    
                    <!-- Info Tambahan -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informasi Pengadilan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Dibuat:</strong> {{ $pengadilan->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="mb-1"><strong>Diupdate:</strong> {{ $pengadilan->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Total User:</strong> {{ $pengadilan->users()->count() }}</p>
                                    <p class="mb-1"><strong>Total Upload:</strong> {{ $pengadilan->uploads()->count() }}</p>
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