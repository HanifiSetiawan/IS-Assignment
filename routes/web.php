<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrangController;

Route::get('/form', [OrangController::class, 'index']);
Route::post('/simpan-data', [OrangController::class, 'simpanData']);

