<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrangController;
use App\Http\Controllers\LoginController;

Route::get('/form', [OrangController::class, 'index']);
Route::post('/simpan-data', [OrangController::class, 'simpanData'])->name('simpan-data');

Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login'])->name('login');
