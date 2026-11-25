<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RfidWebController;
use App\Http\Controllers\RegisterWargaController;
use App\Http\Controllers\SuperAdminController;

// === HALAMAN WELCOME ===
Route::get('/', function () {
    if (auth()->check()) return redirect('/dashboard');
    return view('welcome');
})->name('welcome');

// === REGISTRASI WARGA (publik) ===
Route::get('/daftar', [RegisterWargaController::class, 'showForm'])->name('register.warga');
Route::post('/daftar', [RegisterWargaController::class, 'store'])->name('register.warga.store');

// === AUTH (login/logout bawaan Laravel) ===
Auth::routes(['register' => false]); // Nonaktifkan register bawaan, pakai custom

Route::get('/home', function() {
    return redirect('/dashboard');
});

// === ROUTE TERAUTENTIKASI ===
Route::middleware(['auth', 'check.status'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Warga (hanya admin)
    Route::resource('warga', WargaController::class)->middleware('role:super_admin,admin,bendahara');

    // Iuran (hanya admin)
    Route::resource('iuran', IuranController::class)->middleware('role:super_admin,admin,bendahara');

    // Pembayaran
    Route::resource('pembayaran', PembayaranController::class);
    Route::get('/pembayaran/{id}/verify', [PembayaranController::class, 'verify'])->name('pembayaran.verify');
    Route::get('/pembayaran/{id}/reject', [PembayaranController::class, 'reject'])->name('pembayaran.reject');

    // Keuangan (hanya admin)
    Route::resource('keuangan', KeuanganController::class)->middleware('role:super_admin,admin,bendahara');
    Route::get('/keuangan/export/excel', [KeuanganController::class, 'exportExcel'])->name('keuangan.export');
    Route::get('/pengeluaran/create', [KeuanganController::class, 'createPengeluaran'])->name('pengeluaran.create');

    // RFID / IoT & NFC (hanya admin)
    Route::get('/rfid', [RfidWebController::class, 'index'])->name('rfid.index')->middleware('role:super_admin,admin,bendahara');
    Route::get('/nfc', [RfidWebController::class, 'nfc'])->name('nfc.index')->middleware('role:super_admin,admin,bendahara');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // === SUPER ADMIN PANEL ===
    Route::prefix('super-admin')->name('super_admin.')->middleware('role:super_admin')->group(function () {
        Route::get('/', [SuperAdminController::class, 'index'])->name('index');
        Route::post('/verifikasi/{id}', [SuperAdminController::class, 'verifikasi'])->name('verifikasi');
        Route::post('/tolak/{id}', [SuperAdminController::class, 'tolak'])->name('tolak');
        Route::patch('/ubah-role/{id}', [SuperAdminController::class, 'ubahRole'])->name('ubah_role');
        Route::delete('/hapus/{id}', [SuperAdminController::class, 'hapus'])->name('hapus');
    });
});
