<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\IndikatorController;
use App\Http\Controllers\Admin\KMeansController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route Data Wilayah
Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');

// CRUD Kecamatan
Route::post('/wilayah/kecamatan', [WilayahController::class, 'storeKecamatan'])->name('kecamatan.store');
Route::put('/wilayah/kecamatan/{id}', [WilayahController::class, 'updateKecamatan'])->name('kecamatan.update');
Route::delete('/wilayah/kecamatan/{id}', [WilayahController::class, 'destroyKecamatan'])->name('kecamatan.destroy');

// CRUD Desa
Route::post('/wilayah/desa', [WilayahController::class, 'storeDesa'])->name('desa.store');
Route::put('/wilayah/desa/{id}', [WilayahController::class, 'updateDesa'])->name('desa.update');
Route::delete('/wilayah/desa/{id}', [WilayahController::class, 'destroyDesa'])->name('desa.destroy');


// CRUD Data User
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

// Route Indikator
Route::get('/indikator', [IndikatorController::class, 'index'])->name('indikator.index');

// Route Clustering
Route::get('/kmeans', [KMeansController::class, 'index'])->name('kmeans.index');

// Route untuk import Excel
Route::post('/kmeans/import', [KMeansController::class, 'importExcel'])->name('kmeans.import');

// Route untuk menjalankan K-Means
Route::post('/kmeans/proses', [KMeansController::class, 'prosesKMeans'])->name('kmeans.proses');

// Route untuk menyimpan hasil agregasi (klasterisasi)
Route::post('/kmeans/agregasi', [KMeansController::class, 'simpanAgregasi'])->name('kmeans.agregasi');

// Route untuk reset data indikator
Route::delete('/kmeans/reset', [KMeansController::class, 'resetData'])->name('kmeans.reset');

// Route untuk melihat log perhitungan
Route::get('/kmeans/log', [KMeansController::class, 'logPerhitungan'])->name('kmeans.log');

// Route untuk edit indikator manual
Route::get('/indikator/{id}/edit', [IndikatorController::class, 'edit'])->name('indikator.edit');

// Route untuk update indikator manual
Route::put('/indikator/{id}', [IndikatorController::class, 'update'])->name('indikator.update');

// Route untuk laporan
Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

// Route untuk update status laporan
// Route::post('/laporan/status', [LaporanController::class, 'updateStatus'])->name('laporan.status');

// Hanya Pimpinan yang boleh POST ke URL ini
Route::post('/laporan/status', [LaporanController::class, 'updateStatus'])
    ->name('laporan.status')
    ->middleware('role:pimpinan');

// Route untuk halaman depan (public)
Route::get('/', [FrontController::class, 'index'])->name('home');

Route::get('/metadata', [FrontController::class, 'metadata'])->name('publik.metadata');
Route::get('/panduan', [FrontController::class, 'panduan'])->name('publik.panduan');

// require __DIR__ . '/auth.php';

// 1. URL Rahasia untuk Admin
Route::middleware('guest')->group(function () {
    // Ubah URL di bawah ini sesuai selera Anda
    Route::get('portal-bps-pali/admin-access', [AuthenticatedSessionController::class, 'create'])
                ->name('login'); // Name route 'login' tetap dipertahankan agar sistem auth internal Laravel tidak error

    Route::post('portal-bps-pali/admin-access', [AuthenticatedSessionController::class, 'store']);
});

// 2. Jika ada yang mencoba mengakses /login, lempar ke halaman utama
Route::get('login', function() {
    return redirect()->route('home');
});

// 3. URL Logout
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout')
            ->middleware('auth');
