<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-user-shield me-2"></i>
            <span>Admin Panel</span>
            <small class="ms-2 badge bg-light text-primary">v1.0</small>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarAdmin">
            <!-- Left Side Menu -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>

                <!-- Dropdown Menu: Data Master -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ 
                        request()->routeIs('admin.uploads.*') || 
                        request()->routeIs('admin.users.*') || 
                        request()->routeIs('admin.pengadilan.*') ? 'active' : '' 
                    }}" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-database me-1"></i> Data Master
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.uploads.*') ? 'active' : '' }}"
                                href="{{ route('admin.uploads.index') }}">
                                <i class="fas fa-upload me-2"></i> Semua Upload
                                @php
                                $pendingCount = App\Models\Upload::where('status', 'pending')->count();
                                @endphp
                                @if($pendingCount > 0)
                                <span class="badge bg-warning float-end">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                href="{{ route('admin.users.index') }}">
                                <i class="fas fa-users me-2"></i> Manajemen User
                                <span class="badge bg-info float-end">
                                    {{ App\Models\User::count() }}
                                </span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('admin.pengadilan.*') ? 'active' : '' }}"
                                href="{{ route('admin.pengadilan.index') }}">
                                <i class="fas fa-landmark me-2"></i> Data Pengadilan
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Laporan Menu -->
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar me-1"></i> Laporan
                    </a>
                </li>
            </ul>

            <!-- Right Side: User Menu -->
            <ul class="navbar-nav ms-auto">
                <!-- Quick Stats (Desktop Only) -->
                <li class="nav-item d-none d-lg-flex align-items-center me-3">
                    <div class="text-white">
                        <small class="d-block opacity-75">Statistik Hari Ini</small>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i>
                                {{ App\Models\Upload::whereDate('created_at', today())->count() }}
                            </span>
                            <span class="badge bg-warning">
                                <i class="fas fa-clock"></i>
                                {{ App\Models\Upload::where('status', 'pending')->count() }}
                            </span>
                        </div>
                    </div>
                </li>

                <!-- User Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown">
                        <div class="me-2 position-relative">
                            <i class="fas fa-user-shield fa-lg"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-light text-primary" style="font-size: 0.5rem;">
                                Admin
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span>{{ Auth::user()->name }}</span>
                            <small class="text-light opacity-75">Administrator</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <h6 class="dropdown-header">Menu Admin</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                <i class="fas fa-exchange-alt me-2"></i> Switch to User Mode
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <h6 class="dropdown-header">Akun Saya</h6>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                <i class="fas fa-user-edit me-2"></i> Edit Profile & Password
                            </a>
                        </li>

                        <li>
                            <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                <i class="fas fa-key me-2"></i> Ganti Password
                            </button>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Breadcrumb Navigation -->
<div class="bg-light border-bottom py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                @hasSection('breadcrumb')
                @yield('breadcrumb')
                @else
                <li class="breadcrumb-item active" aria-current="page">
                    @yield('title', 'Admin Panel')
                </li>
                @endif
            </ol>
        </nav>
    </div>
</div>

<!-- Modal untuk Ganti Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i> Ganti Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('user.profile.update-password') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>