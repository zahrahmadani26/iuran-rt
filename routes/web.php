<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RfidWebController;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes();

Route::get('/home', function() {
    return redirect('/dashboard');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Warga
    Route::resource('warga', WargaController::class);
    
    // Iuran
    Route::resource('iuran', IuranController::class);
    
    // Pembayaran
    Route::resource('pembayaran', PembayaranController::class);
    Route::get('/pembayaran/{id}/verify', [PembayaranController::class, 'verify'])->name('pembayaran.verify');
    Route::get('/pembayaran/{id}/reject', [PembayaranController::class, 'reject'])->name('pembayaran.reject');
    
    // Keuangan
    Route::resource('keuangan', KeuanganController::class);
    Route::get('/keuangan/export/excel', [KeuanganController::class, 'exportExcel'])->name('keuangan.export');
    
    // ROUTE KHUSUS PENGELUARAN - PAKAI DIRECT ROUTE
    Route::get('/pengeluaran/create', [KeuanganController::class, 'createPengeluaran'])->name('pengeluaran.create');
    
    // RFID / IoT & NFC
    Route::get('/rfid', [RfidWebController::class, 'index'])->name('rfid.index');
    Route::get('/nfc', [RfidWebController::class, 'nfc'])->name('nfc.index');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});