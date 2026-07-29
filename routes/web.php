<?php

use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\OpdController;
use App\Http\Controllers\Admin\PertanyaanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\DesaController;
use App\Http\Controllers\User\SubmissionController;
use Illuminate\Support\Facades\Route;

// ==================================================================================
// ROUTE SEMENTARA BUAT DEPLOY DI HOSTING TANPA SSH (misal InfinityFree).
// WAJIB DIHAPUS setelah deploy berhasil! Lihat README-INFINITYFREE.md
// ==================================================================================
Route::get('/deploy/migrate', [DeployController::class, 'migrate']);
Route::get('/deploy/seed', [DeployController::class, 'seed']);
Route::get('/deploy/fresh', [DeployController::class, 'fresh']);
Route::get('/deploy/clear-cache', [DeployController::class, 'clearCache']);
Route::get('/deploy/storage-link', [DeployController::class, 'storageLink']);

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ganti password (buat semua role: admin, opd, kecamatan, desa)
    Route::get('/ganti-password', [ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/ganti-password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Area User (OPD / Kecamatan / Desa)
    Route::prefix('data-saya')->name('user.')->group(function () {
        Route::get('/', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::post('/{pertanyaan}', [SubmissionController::class, 'store'])->name('submissions.store');
    });

    // Khusus akun Kecamatan: monitoring & input data per Desa di wilayahnya
    Route::prefix('data-desa')->name('user.desa.')->group(function () {
        Route::get('/monitoring', [DesaController::class, 'monitoring'])->name('monitoring');
        Route::get('/input', [DesaController::class, 'pilihDesa'])->name('pilih');
        Route::get('/input/{desa}', [DesaController::class, 'show'])->name('show');
        Route::post('/input/{desa}/{pertanyaan}', [DesaController::class, 'store'])->name('store');
    });

    // Area Admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/export/excel', [MonitoringController::class, 'exportExcel'])->name('monitoring.export.excel');
        Route::get('/monitoring/export/pdf', [MonitoringController::class, 'exportPdf'])->name('monitoring.export.pdf');
        Route::get('/monitoring/{user}', [MonitoringController::class, 'show'])->name('monitoring.show');
        Route::get('/monitoring/{user}/export/excel', [MonitoringController::class, 'exportUserExcel'])->name('monitoring.export.user-excel');
        Route::get('/monitoring/{user}/export/pdf', [MonitoringController::class, 'exportUserPdf'])->name('monitoring.export.user-pdf');

        Route::resource('users', UserController::class)->except(['show']);
        Route::resource('pertanyaan', PertanyaanController::class)->except(['show']);

        Route::post('/submissions/{submission}/verify', [VerificationController::class, 'verify'])->name('submissions.verify');
        Route::get('/submissions/{submission}/history', [VerificationController::class, 'history'])->name('submissions.history');

        Route::get('/opd', [OpdController::class, 'index'])->name('opd.index');
        Route::post('/opd', [OpdController::class, 'store'])->name('opd.store');
        Route::put('/opd/{opd}', [OpdController::class, 'update'])->name('opd.update');
        Route::delete('/opd/{opd}', [OpdController::class, 'destroy'])->name('opd.destroy');
    });
});
