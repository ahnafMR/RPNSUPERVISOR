<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CheckInController as AdminCheckInController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HasilTemuanController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\LokasiController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\ProsesTemuanController;
use App\Http\Controllers\Admin\TemuanController as AdminTemuanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Supervisor\CheckInController as SupervisorCheckInController;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboardController;
use App\Http\Controllers\Supervisor\LaporanController as SupervisorLaporanController;
use App\Http\Controllers\Supervisor\TemuanController as SupervisorTemuanController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/storage/{path}', function ($path) {
    $file = storage_path('app/public/' . $path);
    if (file_exists($file)) {
        return response()->file($file);
    }
    abort(404);
})->where('path', '.*');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('lokasi', LokasiController::class);
    Route::get('checkin', [AdminCheckInController::class, 'index'])->name('checkin.index');
    Route::get('checkin/{checkin}', [AdminCheckInController::class, 'show'])->name('checkin.show');

    Route::get('laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('laporan/export-pdf-bulk', [AdminLaporanController::class, 'exportPdfBulk'])->name('laporan.export-pdf-bulk');
    Route::get('laporan/{laporan}', [AdminLaporanController::class, 'show'])->name('laporan.show');
    Route::post('laporan/{laporan}/approve', [AdminLaporanController::class, 'approve'])->name('laporan.approve');
    Route::post('laporan/{laporan}/reject', [AdminLaporanController::class, 'reject'])->name('laporan.reject');
    Route::get('laporan/{laporan}/export-pdf', [AdminLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');

    Route::get('temuan', [AdminTemuanController::class, 'index'])->name('temuan.index');
    Route::get('temuan/export-excel', [AdminTemuanController::class, 'exportExcel'])->name('temuan.export-excel');
    Route::get('temuan/{temuan}', [AdminTemuanController::class, 'show'])->name('temuan.show');
    Route::get('temuan/{temuan}/edit', [AdminTemuanController::class, 'edit'])->name('temuan.edit');
    Route::put('temuan/{temuan}', [AdminTemuanController::class, 'update'])->name('temuan.update');
    Route::delete('temuan/{temuan}', [AdminTemuanController::class, 'destroy'])->name('temuan.destroy');
    Route::patch('temuan/{temuan}/status', [AdminTemuanController::class, 'updateStatus'])->name('temuan.update-status');

    Route::get('temuan/{temuan}/proses/create', [ProsesTemuanController::class, 'create'])->name('proses.create');
    Route::post('temuan/{temuan}/proses', [ProsesTemuanController::class, 'store'])->name('proses.store');
    Route::get('temuan/{temuan}/hasil/create', [HasilTemuanController::class, 'create'])->name('hasil.create');
    Route::post('temuan/{temuan}/hasil', [HasilTemuanController::class, 'store'])->name('hasil.store');

    Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit.index');

    // User management
    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
});

Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorDashboardController::class, 'index'])->name('dashboard');

    Route::get('checkin', [SupervisorCheckInController::class, 'index'])->name('checkin.index');
    Route::post('checkin', [SupervisorCheckInController::class, 'store'])->name('checkin.store');
    Route::post('checkin/checkout', [SupervisorCheckInController::class, 'checkout'])->name('checkin.checkout');
    Route::post('checkin/validate-gps', [SupervisorCheckInController::class, 'validateGps'])->name('checkin.validate-gps');

    Route::get('laporan', [SupervisorLaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/create', [SupervisorLaporanController::class, 'create'])->middleware('checkin.active')->name('laporan.create');
    Route::post('laporan', [SupervisorLaporanController::class, 'store'])->name('laporan.store');
    Route::get('laporan/{laporan}', [SupervisorLaporanController::class, 'show'])->name('laporan.show');

    Route::get('laporan/{laporan}/temuan/create', [SupervisorTemuanController::class, 'create'])->name('temuan.create');
    Route::post('laporan/{laporan}/temuan', [SupervisorTemuanController::class, 'store'])->name('temuan.store');
    Route::get('temuan/{temuan}', [SupervisorTemuanController::class, 'show'])->name('temuan.show');
});
