<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\UploadController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PengadilanController;
use App\Http\Controllers\Admin\AdminUploadController as AdminUploadController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        $user = auth()->user();
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    })->name('home');

    // ==================== USER ROUTES ====================
    Route::prefix('user')->name('user.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

        // Uploads - RESTful Routes
        Route::prefix('uploads')->name('uploads.')->group(function () {
            // RESTful Resource Routes
            Route::get('/', [UploadController::class, 'index'])->name('index');           // List all uploads
            Route::get('/create', [UploadController::class, 'create'])->name('create');   // Create form
            Route::post('/', [UploadController::class, 'store'])->name('store');          // Store new upload
            Route::get('/{upload}', [UploadController::class, 'show'])->name('show');     // Show single upload
            Route::get('/{upload}/edit', [UploadController::class, 'edit'])->name('edit'); // Edit form
            Route::put('/{upload}', [UploadController::class, 'update'])->name('update'); // Update upload
            Route::delete('/{upload}', [UploadController::class, 'destroy'])->name('destroy'); // Delete upload

            // Custom Routes
            Route::get('/history', [UploadController::class, 'history'])->name('history');
            Route::get('/{upload}/download', [UploadController::class, 'download'])->name('download');
            Route::get('/{upload}/preview', [UploadController::class, 'preview'])->name('preview');

            // ✅ ROUTES UNTUK TRASH
            Route::prefix('trash')->name('trash.')->group(function () {
                Route::get('/trash-page', [UploadController::class, 'trashIndex'])->name('index');
                Route::post('/restore/{id}', [UploadController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}', [UploadController::class, 'forceDelete'])->name('force-delete');
                Route::post('/empty', [UploadController::class, 'emptyTrash'])->name('empty');
                Route::get('/test-controller', [App\Http\Controllers\User\UploadController::class, 'trashIndex']);
            });
        });
    });

    // ==================== ADMIN ROUTES ====================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('reset-password');
        });

        // Uploads Management
        Route::prefix('uploads')->name('uploads.')->group(function () {
            // RESTful Resource Routes
            Route::get('/', [AdminUploadController::class, 'index'])->name('index');
            Route::get('/create', [AdminUploadController::class, 'create'])->name('create');
            Route::post('/', [AdminUploadController::class, 'store'])->name('store');
            Route::get('/{upload}', [AdminUploadController::class, 'show'])->name('show');
            Route::get('/{upload}/edit', [AdminUploadController::class, 'edit'])->name('edit');
            Route::put('/{upload}', [AdminUploadController::class, 'update'])->name('update');
            Route::delete('/{upload}', [AdminUploadController::class, 'destroy'])->name('destroy');

            // Custom Routes
            Route::get('/export/excel', [AdminUploadController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [AdminUploadController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/{upload}/preview', [AdminUploadController::class, 'preview'])->name('preview');
            Route::get('/{upload}/download', [AdminUploadController::class, 'download'])->name('download');
            Route::post('/{upload}/verify', [AdminUploadController::class, 'verify'])->name('verify');
            Route::post('/{upload}/reject', [AdminUploadController::class, 'reject'])->name('reject');
            Route::post('/{upload}/update-status', [AdminUploadController::class, 'updateStatus'])->name('update-status');
        });

        // Pengadilan Management
        Route::prefix('pengadilan')->name('pengadilan.')->group(function () {
            Route::get('/', [PengadilanController::class, 'index'])->name('index');
            Route::get('/create', [PengadilanController::class, 'create'])->name('create');
            Route::post('/', [PengadilanController::class, 'store'])->name('store');
            Route::get('/{pengadilan}', [PengadilanController::class, 'show'])->name('show');
            Route::get('/{pengadilan}/edit', [PengadilanController::class, 'edit'])->name('edit');
            Route::put('/{pengadilan}', [PengadilanController::class, 'update'])->name('update');
            Route::delete('/{pengadilan}', [PengadilanController::class, 'destroy'])->name('destroy');
        });
    });
});
