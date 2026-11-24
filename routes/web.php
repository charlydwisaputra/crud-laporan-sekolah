<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Halaman depan publik (Homepage)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Galeri perbaikan yang sudah selesai (publik)
Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri');

// Autentikasi manual (tanpa Breeze)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Rute terautentikasi untuk semua role
Route::middleware('auth')->group(function () {
    // Dashboard khusus Admin / Guru
    Route::get('/admin/dashboard', [ReportController::class, 'adminDashboard'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    // Khusus Siswa: buat, edit, hapus laporan miliknya (saat belum diterima)
    Route::middleware('role:siswa')->group(function () {
        Route::get('/reports/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/reports/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/reports/{report}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    });

    // Khusus Admin / Guru: terima & tugaskan petugas, tolak laporan, konfirmasi selesai, hapus laporan
    Route::middleware('role:admin')->group(function () {
        Route::post('/reports/{report}/assign', [ReportController::class, 'acceptAndAssign'])->name('reports.assign');
        Route::post('/reports/{report}/reject', [ReportController::class, 'rejectReport'])->name('reports.reject');
        Route::post('/reports/{report}/confirm-completion', [ReportController::class, 'confirmCompletion'])->name('reports.confirm-completion');
        Route::delete('/admin/reports/{report}', [ReportController::class, 'adminDestroy'])->name('admin.reports.destroy');
    });

    // Khusus Petugas: kembalikan tugas, klik "Kerjakan", klik "Selesai" + upload foto bukti
    Route::middleware('role:petugas')->group(function () {
        Route::post('/reports/{report}/respond', [ReportController::class, 'respondAssignment'])->name('reports.respond');
        Route::post('/reports/{report}/start', [ReportController::class, 'startWork'])->name('reports.start');
        Route::post('/reports/{report}/complete', [ReportController::class, 'submitCompletion'])->name('reports.complete');
    });

    // Daftar laporan & detail laporan (difilter otomatis sesuai role)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
});