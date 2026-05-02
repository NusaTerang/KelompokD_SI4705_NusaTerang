<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DesaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profil.update');
});

Route::prefix('desa')->name('desa.')->group(function () {
    Route::get('input', [DesaController::class, 'create'])->name('input');
    Route::post('/', [DesaController::class, 'store'])->name('store');
    Route::get('kelola', [DesaController::class, 'kelola'])->name('kelola');
    Route::get('daftar', [DesaController::class, 'index'])->name('daftar');
});

Route::get('/profil-preview', [ProfileController::class, 'edit']);