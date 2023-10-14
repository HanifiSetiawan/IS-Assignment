<?php

// routes/web.php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrangController;
use App\Http\Controllers\LoginController;

Route::get('/', [HomeController::class, 'index'])->middleware('auth');

Route::get('/form', [OrangController::class, 'index']);
Route::post('/simpan-data', [OrangController::class, 'simpanData'])->name('simpan-data');

Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'register'])->name('register');