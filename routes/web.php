<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenugasanController;

use App\Http\Controllers\ProyekController;
use App\Http\Controllers\PenyediaController;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/assign', [PenugasanController::class, 'assign']);
Route::post('/respon/{id}', [PenugasanController::class, 'respon']);
Route::post('/detail', [PenugasanController::class, 'isiDetail']);

Route::prefix('proyek')->name('proyek.')->group(function () {
    Route::get('/buat', [ProyekController::class, 'create'])->name('create');
    Route::post('/buat', [ProyekController::class, 'saveStep1'])->name('save.step1');
    
    Route::get('/{id}/pilih-penyedia', [ProyekController::class, 'step2'])->name('step2');
    Route::post('/{id}/pilih-penyedia', [ProyekController::class, 'saveStep2'])->name('save.step2');
    
    Route::get('/{id}/review', [ProyekController::class, 'review'])->name('review');
    Route::post('/{id}/kirim', [ProyekController::class, 'kirimKePenyedia'])->name('kirim');
    
    Route::get('/{id}', [ProyekController::class, 'show'])->name('show');
});

Route::get('/api/penyedia/rekomendasi', [PenyediaController::class, 'getRekomendasi'])->name('api.penyedia.rekomendasi');
