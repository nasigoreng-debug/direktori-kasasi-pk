<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\UploadController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PengadilanController;
use App\Http\Controllers\Admin\UploadController as AdminUploadController;

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

        // Uploads
        Route::prefix('upload')->name('upload.')->group(function () {
            // Existing upload routes
            Route::get('/', [UploadController::class, 'create'])->name('create');
            Route::post('/', [UploadController::class, 'store'])->name('store');
            Route::get('/history', [UploadController::class, 'history'])->name('history');
            Route::get('/download/{id}', [UploadController::class, 'download'])->name('download');
            Route::get('/preview/{id}', [UploadController::class, 'preview'])->name('preview');
            Route::get('/edit/{id}', [UploadController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [UploadController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [UploadController::class, 'destroy'])->name('destroy');

            // ✅ ROUTES UNTUK TRASH
            Route::prefix('trash')->name('trash.')->group(function () {
                Route::get('/', [UploadController::class, 'trashIndex'])->name('index');
                Route::post('/restore/{id}', [UploadController::class, 'restore'])->name('restore');
                Route::delete('/force-delete/{id}', [UploadController::class, 'forceDelete'])->name('force-delete');
                Route::post('/empty', [UploadController::class, 'emptyTrash'])->name('empty');
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
            Route::put('/{id}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('reset-password');
        });

        // Uploads Management
        Route::prefix('uploads')->name('uploads.')->group(function () {
            Route::get('/', [AdminUploadController::class, 'index'])->name('index');
            Route::get('/create', [AdminUploadController::class, 'create'])->name('create');
            Route::post('/', [AdminUploadController::class, 'store'])->name('store');
            Route::get('/{id}', [AdminUploadController::class, 'show'])->name('show');
            Route::get('/{id}/preview', [AdminUploadController::class, 'preview'])->name('preview');
            Route::get('/{id}/edit', [AdminUploadController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminUploadController::class, 'update'])->name('update');
            Route::delete('/{id}', [AdminUploadController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/verify', [AdminUploadController::class, 'verify'])->name('verify');
            Route::post('/{id}/reject', [AdminUploadController::class, 'reject'])->name('reject');
            Route::post('/{id}/update-status', [AdminUploadController::class, 'updateStatus'])->name('update-status');
            Route::get('/{id}/download', [AdminUploadController::class, 'download'])->name('download');
            Route::get('/export/excel', [AdminUploadController::class, 'exportExcel'])->name('export.excel');
            Route::get('/export/pdf', [AdminUploadController::class, 'exportPdf'])->name('export.pdf');
        });

        // Pengadilan Management
        Route::prefix('pengadilan')->name('pengadilan.')->group(function () {
            Route::get('/', [PengadilanController::class, 'index'])->name('index');
            Route::get('/create', [PengadilanController::class, 'create'])->name('create');
            Route::post('/', [PengadilanController::class, 'store'])->name('store');
            Route::get('/{id}', [PengadilanController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [PengadilanController::class, 'edit'])->name('edit');
            Route::put('/{id}', [PengadilanController::class, 'update'])->name('update');
            Route::delete('/{id}', [PengadilanController::class, 'destroy'])->name('destroy');
        });
    });
});
