<!DOCTYPE html>
<html lang="id" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Sistem Putusan Kasasi/PK')</title>
    <meta name="description" content="@yield('description', 'Aplikasi manajemen putusan kasasi dan peninjauan kembali')">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        main {
            flex: 1;
        }
        
        .navbar-brand {
            font-weight: 600;
        }
        
        .nav-link.active {
            font-weight: 500;
            position: relative;
        }
        
        .nav-link.active:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 10%;
            width: 80%;
            height: 2px;
            background-color: white;
        }
        
        .dropdown-item:active {
            background-color: var(--primary-color);
        }
        
        footer {
            border-top: 1px solid #dee2e6;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                padding-left: 15px;
                padding-right: 15px;
            }
            
            .nav-link.active:after {
                display: none;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Navigation -->
    @auth
        @if(auth()->user()->role === 'admin' && request()->is('admin*'))
            @include('layouts.partials.navbar-admin')
        @else
            @include('layouts.partials.navbar-user')
        @endif
    @else
        @include('layouts.partials.navbar-guest')
    @endauth

    <!-- Main Content -->
    <main class="py-4">
        <div class="container">
            <!-- Flash Messages -->
            <div class="flash-messages">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            <!-- Page Title (Optional) -->
            @hasSection('page-title')
                <div class="row mb-4">
                    <div class="col">
                        <h1 class="h3 mb-0">
                            @yield('page-title')
                            @hasSection('page-subtitle')
                                <small class="text-muted d-block mt-1">
                                    @yield('page-subtitle')
                                </small>
                            @endif
                        </h1>
                    </div>
                    @hasSection('page-actions')
                        <div class="col-auto">
                            @yield('page-actions')
                        </div>
                    @endif
                </div>
            @endif

            <!-- Content -->
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-auto">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <i class="fas fa-balance-scale text-primary me-1"></i>
                        <strong>Sistem Putusan Kasasi/PK</strong> &copy; {{ date('Y') }}
                    </p>
                    <small class="text-muted">Pengadilan Agama Jawa Barat</small>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        <i class="fas fa-code me-1"></i>Versi 1.0.0
                        @auth
                            | <i class="fas fa-user me-1 ms-2"></i>{{ Auth::user()->name }}
                        @endauth
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom Scripts -->
    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
            
            // Set active menu based on current URL
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });
            
            // CSRF token for AJAX requests
            window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        });
    </script>

    @stack('scripts')
</body>
</html>