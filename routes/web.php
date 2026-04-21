<?php

use App\Http\Controllers\Admin\IndikatorController;
use App\Http\Controllers\Admin\KMeansController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WilayahController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Route Data Wilayah
Route::get('/wilayah', [WilayahController::class, 'index'])->name('wilayah.index');

// Route User
Route::get('/users', [UserController::class, 'index'])->name('users.index');

// Route Indikator
Route::get('/indikator', [IndikatorController::class, 'index'])->name('indikator.index');

// Route Clustering
Route::get('/kmeans', [KMeansController::class, 'index'])->name('kmeans.index');

// Route untuk import Excel
Route::post('/kmeans/import', [KMeansController::class, 'importExcel'])->name('kmeans.import');


require __DIR__ . '/auth.php';
