<?php

// routes/web.php

use App\Http\Controllers\datacontroller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrangController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\AccessController;


Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::get('/register', [RegisterController::class, 'index']);
Route::post('/register', [RegisterController::class, 'register'])->name('register');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/form', [OrangController::class, 'index'])->name('form');
    Route::post('/form', [OrangController::class, 'simpanData'])->name('simpan-data');

    Route::get('/Data', [datacontroller::class, 'index'])->name('Data');
    Route::get('/download/{orang_id}/{ext}/{file}', [datacontroller::class, 'download'])
        ->name('download')
        ->where('orang_id', '.*')
        ->where('ext', '.*')
        ->where('file', '.*');
    
    Route::get('/access', [AccessController::class, 'index'])->name('access');
    Route::post('/access', [AccessController::class, 'submit'])->name('access.submit');
});

Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');