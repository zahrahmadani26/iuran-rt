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
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\SuratPengantarController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\KontakDaruratController;
use App\Http\Controllers\KartuKeluargaController;
use App\Http\Controllers\PanicButtonController;

// === HALAMAN WELCOME ===
Route::get('/', function () {
    if (auth()->check()) return redirect('/dashboard');
    return view('welcome');
})->name('welcome');

// Preview welcome page kapan saja (termasuk saat sudah login)
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome.page');

// === REGISTRASI WARGA (publik) ===
Route::get('/daftar', [RegisterWargaController::class, 'showForm'])->name('register.warga');
Route::post('/daftar', [RegisterWargaController::class, 'store'])->name('register.warga.store');

// === AUTH (login/logout bawaan Laravel) ===
Auth::routes(['register' => false]);

Route::get('/home', function () {
    return redirect('/dashboard');
});

// === ROUTE TERAUTENTIKASI ===
Route::middleware(['auth', 'check.status'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === PENGUMUMAN RT ===
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::middleware('role:super_admin,admin,bendahara')->group(function () {
        Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
        Route::put('/pengumuman/{pengumuman}', [PengumumanController::class, 'update'])->name('pengumuman.update');
        Route::post('/pengumuman/{pengumuman}/pin', [PengumumanController::class, 'togglePin'])->name('pengumuman.pin');
        Route::delete('/pengumuman/{pengumuman}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    });

    // === SURAT PENGANTAR DIGITAL ===
    Route::get('/surat', [SuratPengantarController::class, 'index'])->name('surat.index');
    Route::post('/surat', [SuratPengantarController::class, 'store'])->name('surat.store');
    Route::get('/surat/{surat}/pdf', [SuratPengantarController::class, 'downloadPdf'])->name('surat.pdf');
    Route::delete('/surat/{surat}', [SuratPengantarController::class, 'destroy'])->name('surat.destroy');
    Route::middleware('role:super_admin,admin,bendahara')->group(function () {
        Route::post('/surat/{surat}/process', [SuratPengantarController::class, 'process'])->name('surat.process');
        Route::post('/surat/{surat}/approve', [SuratPengantarController::class, 'approve'])->name('surat.approve');
        Route::post('/surat/{surat}/reject', [SuratPengantarController::class, 'reject'])->name('surat.reject');
    });

    // === LAPORAN / ASPIRASI WARGA ===
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan', [LaporanController::class, 'store'])->name('laporan.store');
    Route::delete('/laporan/{laporan}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::middleware('role:super_admin,admin,bendahara')->group(function () {
        Route::post('/laporan/{laporan}/status', [LaporanController::class, 'updateStatus'])->name('laporan.status');
    });

    // === JADWAL KEGIATAN RT ===
    Route::get('/jadwal', [JadwalKegiatanController::class, 'index'])->name('jadwal.index');
    Route::middleware('role:super_admin,admin,bendahara')->group(function () {
        Route::post('/jadwal', [JadwalKegiatanController::class, 'store'])->name('jadwal.store');
        Route::put('/jadwal/{jadwal}', [JadwalKegiatanController::class, 'update'])->name('jadwal.update');
        Route::delete('/jadwal/{jadwal}', [JadwalKegiatanController::class, 'destroy'])->name('jadwal.destroy');
    });

    // === KONTAK DARURAT ===
    Route::get('/kontak-darurat', [KontakDaruratController::class, 'index'])->name('kontak_darurat.index');
    Route::middleware('role:super_admin,admin,bendahara')->group(function () {
        Route::post('/kontak-darurat', [KontakDaruratController::class, 'store'])->name('kontak_darurat.store');
        Route::put('/kontak-darurat/{kontakDarurat}', [KontakDaruratController::class, 'update'])->name('kontak_darurat.update');
        Route::delete('/kontak-darurat/{kontakDarurat}', [KontakDaruratController::class, 'destroy'])->name('kontak_darurat.destroy');
    });

    // === DATA KARTU KELUARGA (KK) ===
    Route::get('/kartu-keluarga', [KartuKeluargaController::class, 'index'])->name('kartu_keluarga.index');
    Route::post('/kartu-keluarga', [KartuKeluargaController::class, 'store'])->name('kartu_keluarga.store');
    Route::put('/kartu-keluarga/{kartuKeluarga}', [KartuKeluargaController::class, 'update'])->name('kartu_keluarga.update');
    Route::delete('/kartu-keluarga/{kartuKeluarga}', [KartuKeluargaController::class, 'destroy'])->name('kartu_keluarga.destroy');

    // === PANIC BUTTON SOS ===
    Route::get('/panic-button', [PanicButtonController::class, 'index'])->name('panic.index');
    Route::post('/panic-button', [PanicButtonController::class, 'trigger'])->name('panic.trigger');
    Route::middleware('role:super_admin,admin,bendahara')->group(function () {
        Route::post('/panic-button/{panic}/tangani', [PanicButtonController::class, 'tangani'])->name('panic.tangani');
        Route::post('/panic-button/{panic}/selesai', [PanicButtonController::class, 'selesai'])->name('panic.selesai');
    });

    // === ADMINISTRASI & KEUANGAN EXISTING ===
    Route::resource('warga', WargaController::class)->middleware('role:super_admin,admin,bendahara');
    Route::resource('iuran', IuranController::class)->middleware('role:super_admin,admin,bendahara');

    Route::resource('pembayaran', PembayaranController::class);
    Route::get('/pembayaran/{id}/verify', [PembayaranController::class, 'verify'])->name('pembayaran.verify');
    Route::get('/pembayaran/{id}/reject', [PembayaranController::class, 'reject'])->name('pembayaran.reject');

    Route::resource('keuangan', KeuanganController::class)->middleware('role:super_admin,admin,bendahara');
    Route::get('/keuangan/export/excel', [KeuanganController::class, 'exportExcel'])->name('keuangan.export');
    Route::get('/pengeluaran/create', [KeuanganController::class, 'createPengeluaran'])->name('pengeluaran.create');

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
