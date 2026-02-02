@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle"></i> Edit Profile
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab"
                                data-bs-target="#profile" type="button">
                                <i class="fas fa-user"></i> Data Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="password-tab" data-bs-toggle="tab"
                                data-bs-target="#password" type="button">
                                <i class="fas fa-key"></i> Ubah Password
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="profileTabContent">
                        <!-- Tab Profile -->
                        <div class="tab-pane fade show active" id="profile" role="tabpanel">
                            <form action="{{ route('user.profile.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                        <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control"
                                            value="{{ ucfirst($user->role) }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Terdaftar Sejak</label>
                                        <input type="text" class="form-control"
                                            value="{{ $user->created_at->format('d F Y') }}" readonly>
                                    </div>
                                </div>

                                @if($user->pengadilan)
                                <div class="mb-3">
                                    <label class="form-label">Pengadilan</label>
                                    <input type="text" class="form-control"
                                        value="{{ $user->pengadilan->nama }} ({{ $user->pengadilan->kode }})" readonly>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                        Kembali ke Dashboard
                                    </a>
                                </div>
                            </form>
                        </div>

                        <!-- Tab Password -->
                        <div class="tab-pane fade" id="password" role="tabpanel">
                            <form action="{{ route('user.profile.update-password') }}" method="POST">
                                @csrf

                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle"></i>
                                    Password minimal 8 karakter. Setelah password diubah, Anda akan tetap login.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                    @error('current_password')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="new_password" class="form-control" minlength="8" required>
                                    @error('new_password')
                                    <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <input type="password" name="new_password_confirmation" class="form-control" minlength="8" required>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key"></i> Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection