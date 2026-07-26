<?php

use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\OpdController;
use App\Http\Controllers\Admin\PertanyaanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\User\SubmissionController;
use Illuminate\Support\Facades\Route;

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

    // Area User (OPD / Kecamatan / Desa)
    Route::prefix('data-saya')->name('user.')->group(function () {
        Route::get('/', [SubmissionController::class, 'index'])->name('submissions.index');
        Route::post('/{pertanyaan}', [SubmissionController::class, 'store'])->name('submissions.store');
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

        Route::get('/opd', [OpdController::class, 'index'])->name('opd.index');
        Route::post('/opd', [OpdController::class, 'store'])->name('opd.store');
        Route::put('/opd/{opd}', [OpdController::class, 'update'])->name('opd.update');
        Route::delete('/opd/{opd}', [OpdController::class, 'destroy'])->name('opd.destroy');
    });
});
