<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RfidController;

/*
|--------------------------------------------------------------------------
| API Routes for RFID ESP32
|--------------------------------------------------------------------------
*/

Route::prefix('rfid')->group(function () {
    // Endpoint utama saat ESP32 tap kartu
    Route::post('/tap', [RfidController::class, 'tap']);
    
    // Endpoint saat ESP32 hanya scan kartu (mode registrasi / cek UID)
    Route::post('/scan', [RfidController::class, 'scan']);
    
    // Cek kartu terakhir yang discan (dipakai untuk UI web pendaftaran warga)
    Route::get('/last-scan', [RfidController::class, 'getLastScan']);
    
    // Cek status warga berdasarkan UID
    Route::get('/check/{uid}', [RfidController::class, 'checkCard']);
});
