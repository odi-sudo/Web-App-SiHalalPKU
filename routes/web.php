<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegistrasiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UmkmController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ProfilController;

/*
|--------------------------------------------------------------------------
*/

// Home page 
Route::get('/', [CustomerController::class, 'home'])->name('home');

// Peta Interaktif (Public) 
Route::get('/peta', [PetaController::class, 'loadPeta'])->name('peta'); Route::get('/api/coordinates', [PetaController::class, 'getCoordinatesJson'])->name('api.coordinates');

// Search Routes (Public) 
Route::get('/cari/umkm', [SearchController::class, 'cariUmkm'])->name('search.umkm'); Route::get('/cari/produk', [SearchController::class, 'cariProduk'])->name('search.produk'); Route::get('/api/search', [SearchController::class, 'ajaxSearch'])->name('api.search');

// Profil UMKM (Public View) 
Route::get('/umkm/{id}', [ProfilController::class, 'requestDetail'])->name('profil.detail'); Route::get('/api/umkm/{id}/reviews', [ProfilController::class, 'getReviewsJson'])->name('api.reviews');

/* |--------------------------------------------------------------------------

*/

Route::middleware(['guest.only'])->group(function () { // Registration 
Route::get('/registrasi', [RegistrasiController::class, 'bukaHalamanRegistrasi'])->name('registrasi'); 
Route::post('/registrasi', [RegistrasiController::class, 'prosesRegistrasi'])->name('registrasi.proses');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'verifikasiLogin'])->name('login.proses');

});

/* |--------------------------------------------------------------------------

*/

Route::middleware(['auth'])->group(function () { // Logout 
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Submit Review (Authenticated Users Only)
Route::post('/umkm/{id}/review', [ProfilController::class, 'simpanUlasan'])->name('review.store');

});

/* |--------------------------------------------------------------------------

*/

Route::middleware(['auth', 'role:Pengguna'])->prefix('customer')->name('customer.')->group(function () { Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard'); });

/* |--------------------------------------------------------------------------

*/

Route::middleware(['auth', 'role:Administrator'])->prefix('admin')->name('admin.')->group(function () { // Dashboard 
Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

// UMKM Management
Route::prefix('umkm')->name('umkm.')->group(function () {
    Route::get('/', [UmkmController::class, 'requestDaftarUmkm'])->name('index');
    Route::get('/create', [UmkmController::class, 'tampilkanFormKosong'])->name('create');
    Route::post('/', [UmkmController::class, 'simpanDataBaru'])->name('store');
    Route::get('/{id}/edit', [UmkmController::class, 'tampilkanFormIsi'])->name('edit');
    Route::put('/{id}', [UmkmController::class, 'updateData'])->name('update');
    Route::delete('/{id}', [UmkmController::class, 'hapusData'])->name('destroy');
    Route::get('/{id}/confirm-delete', [UmkmController::class, 'tampilkanKonfirmasi'])->name('confirm-delete');
});
});

