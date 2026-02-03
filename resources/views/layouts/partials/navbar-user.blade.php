<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('user.dashboard') }}">
            <i class="fas fa-balance-scale me-2"></i>
            <span>Putusan Kasasi/PK</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarUser">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarUser">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"
                        href="{{ route('user.dashboard') }}">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.uploads.create') ? 'active' : '' }}"
                        href="{{ route('user.uploads.create') }}">
                        <i class="fas fa-upload me-1"></i> Upload
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.uploads.index') ? 'active' : '' }}"
                        href="{{ route('user.uploads.index') }}">
                        <i class="fas fa-history me-1"></i> History
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('user.uploads.trash.*') ? 'active' : '' }}"
                        href="{{ route('user.uploads.trash.index') }}">
                        <i class="fas fa-trash me-1"></i> Trash
                        @php
                        $trashCount = auth()->user()->uploads()->onlyTrashed()->count();
                        @endphp
                        @if($trashCount > 0)
                        <span class="badge bg-danger rounded-pill ms-1">{{ $trashCount }}</span>
                        @endif
                    </a>
                </li>

                @if(auth()->user()->role === 'admin')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-user-shield me-1"></i> Admin Panel
                    </a>
                </li>
                @endif
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                        data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <div class="d-flex flex-column">
                            <span>{{ Auth::user()->name }}</span>
                            <small class="text-light opacity-75">{{ Auth::user()->email }}</small>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
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

                        @if(auth()->user()->role === 'admin')
                        <li>
                            <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-user-shield me-2"></i> Admin Panel
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @endif

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