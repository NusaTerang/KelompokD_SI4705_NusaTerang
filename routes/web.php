<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PenugasanController;

Route::get('/', function () {
    return view('welcome');
});

//PBI 14 NASYWAN TSANY FAWWAZ
Route::post('/assign', [PenugasanController::class, 'assign']);
Route::post('/respon/{id}', [PenugasanController::class, 'respon']);
Route::post('/detail', [PenugasanController::class, 'isiDetail']);