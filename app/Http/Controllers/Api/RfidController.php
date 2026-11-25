<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\Iuran;
use App\Models\Pembayaran;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class RfidController extends Controller
{
    /**
     * Helper untuk menormalisasi format UID dari RFID reader (misal: "A1:B2:C3:D4" -> "A1B2C3D4")
     */
    protected function cleanUid($uid)
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string)$uid));
    }

    /**
     * Endpoint utama ketika kartu RFID di-tap pada ESP32
     * POST /api/rfid/tap
     */
    public function tap(Request $request)
    {
        $rawUid = $request->input('rfid_uid') ?? $request->input('uid');

        if (!$rawUid) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_INPUT',
                'message' => 'Parameter rfid_uid tidak ditemukan.'
            ], 400);
        }

        $uid = $this->cleanUid($rawUid);

        // Simpan kartu yang terakhir discan ke cache (berlaku 10 menit) untuk kemudahan registrasi di Web Admin
        Cache::put('last_scanned_rfid', [
            'uid' => $uid,
            'time' => now()->format('H:i:s d/m/Y')
        ], now()->addMinutes(10));

        // Cari data warga berdasarkan UID RFID
        $warga = Warga::where('rfid_uid', $uid)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'code' => 'NOT_REGISTERED',
                'message' => 'Kartu RFID belum terdaftar!',
                'rfid_uid' => $uid,
                'warga' => null
            ], 404);
        }

        if ($warga->status !== 'aktif') {
            return response()->json([
                'success' => false,
                'code' => 'WARGA_INACTIVE',
                'message' => 'Warga berstatus Non-Aktif.',
                'rfid_uid' => $uid,
                'warga' => $warga->nama_lengkap
            ], 403);
        }

        // Tentukan jenis iuran yang akan dibayar (opsional kirim iuran_id dari ESP32, default: iuran aktif pertama)
        $iuranId = $request->input('iuran_id');
        $iuran = $iuranId ? Iuran::find($iuranId) : Iuran::orderBy('id', 'asc')->first();

        if (!$iuran) {
            return response()->json([
                'success' => false,
                'code' => 'NO_IURAN',
                'message' => 'Belum ada data iuran aktif dalam sistem.',
                'rfid_uid' => $uid,
                'warga' => $warga->nama_lengkap
            ], 400);
        }

        // Cek apakah warga sudah membayar iuran ini pada periode berjalan (misal: bulan ini)
        $currentMonthYear = date('Y-m');
        $isSqlite = DB::getDriverName() === 'sqlite';

        $alreadyPaidQuery = Pembayaran::where('warga_id', $warga->id)
            ->where('iuran_id', $iuran->id)
            ->where('status', 'verified');

        if ($isSqlite) {
            $alreadyPaidQuery->whereRaw("strftime('%Y-%m', tanggal_bayar) = ?", [$currentMonthYear]);
        } else {
            $alreadyPaidQuery->whereYear('tanggal_bayar', date('Y'))
                             ->whereMonth('tanggal_bayar', date('m'));
        }

        $alreadyPaid = $alreadyPaidQuery->first();

        if ($alreadyPaid) {
            return response()->json([
                'success' => false,
                'code' => 'ALREADY_PAID',
                'message' => 'Iuran ' . $iuran->nama_iuran . ' bulan ini sudah lunas!',
                'rfid_uid' => $uid,
                'warga' => $warga->nama_lengkap,
                'iuran' => $iuran->nama_iuran,
                'nominal' => (int)$iuran->nominal,
                'tanggal_bayar' => date('d/m/Y', strtotime($alreadyPaid->tanggal_bayar)),
            ], 200);
        }

        // Catat Pembayaran baru otomatis berstatus 'verified'
        $source = $request->input('source', 'rfid_esp32');
        $isNfc = ($source === 'nfc_android' || $source === 'nfc');
        $keteranganBayar = $isNfc 
            ? 'Pembayaran via NFC HP Android (UID: ' . $uid . ')'
            : 'Pembayaran via RFID Tap ESP32 (UID: ' . $uid . ')';

        $pembayaran = Pembayaran::create([
            'warga_id' => $warga->id,
            'iuran_id' => $iuran->id,
            'jumlah_dibayar' => $iuran->nominal,
            'tanggal_bayar' => now()->toDateString(),
            'status' => 'verified',
            'keterangan' => $keteranganBayar
        ]);

        // Otomatis masukkan ke Kas RT (Keuangan - Pemasukan)
        $deskripsiKas = $isNfc
            ? 'Iuran ' . $iuran->nama_iuran . ' - ' . $warga->nama_lengkap . ' (NFC HP)'
            : 'Iuran ' . $iuran->nama_iuran . ' - ' . $warga->nama_lengkap . ' (RFID Tap)';

        Keuangan::create([
            'jenis' => 'pemasukan',
            'kategori' => 'Iuran Warga',
            'deskripsi' => $deskripsiKas,
            'nominal' => $iuran->nominal,
            'tanggal' => now()->toDateString(),
        ]);

        // Hitung saldo kas terbaru
        $totalPemasukan = Keuangan::where('jenis', 'pemasukan')->sum('nominal') ?? 0;
        $totalPengeluaran = Keuangan::where('jenis', 'pengeluaran')->sum('nominal') ?? 0;
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'success' => true,
            'code' => 'PAYMENT_SUCCESS',
            'message' => 'Pembayaran Iuran Berhasil!',
            'rfid_uid' => $uid,
            'warga' => $warga->nama_lengkap,
            'iuran' => $iuran->nama_iuran,
            'nominal' => (int)$iuran->nominal,
            'tanggal' => now()->format('d/m/Y H:i'),
            'pembayaran_id' => $pembayaran->id,
            'saldo_kas' => (int)$saldoKas
        ], 200);
    }

    /**
     * Endpoint untuk mode scan saja (misal untuk mendaftarkan kartu tanpa memotong iuran)
     * POST /api/rfid/scan
     */
    public function scan(Request $request)
    {
        $rawUid = $request->input('rfid_uid') ?? $request->input('uid');

        if (!$rawUid) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter rfid_uid tidak ditemukan.'
            ], 400);
        }

        $uid = $this->cleanUid($rawUid);

        // Simpan ke cache
        Cache::put('last_scanned_rfid', [
            'uid' => $uid,
            'time' => now()->format('H:i:s d/m/Y')
        ], now()->addMinutes(10));

        $warga = Warga::where('rfid_uid', $uid)->first();

        return response()->json([
            'success' => true,
            'rfid_uid' => $uid,
            'is_registered' => $warga !== null,
            'warga' => $warga ? [
                'id' => $warga->id,
                'nama' => $warga->nama_lengkap,
                'nik' => $warga->nik,
                'status' => $warga->status
            ] : null,
            'message' => $warga ? 'Kartu terdaftar atas nama ' . $warga->nama_lengkap : 'Kartu belum terdaftar.'
        ]);
    }

    /**
     * Cek data kartu RFID yang terakhir kali discan di alat ESP32
     * GET /api/rfid/last-scan
     */
    public function getLastScan()
    {
        $lastScan = Cache::get('last_scanned_rfid');

        if (!$lastScan) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada kartu yang di-scan.'
            ]);
        }

        // Cari tahu apakah UID ini sudah milik seseorang
        $warga = Warga::where('rfid_uid', $lastScan['uid'])->first();

        return response()->json([
            'success' => true,
            'rfid_uid' => $lastScan['uid'],
            'time' => $lastScan['time'],
            'is_registered' => $warga !== null,
            'warga_nama' => $warga ? $warga->nama_lengkap : null
        ]);
    }

    /**
     * Cek info kartu berdasarkan UID
     * GET /api/rfid/check/{uid}
     */
    public function checkCard($uid)
    {
        $cleaned = $this->cleanUid($uid);
        $warga = Warga::where('rfid_uid', $cleaned)->first();

        if (!$warga) {
            return response()->json([
                'success' => false,
                'rfid_uid' => $cleaned,
                'is_registered' => false,
                'message' => 'Kartu tidak terdaftar'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'rfid_uid' => $cleaned,
            'is_registered' => true,
            'warga' => [
                'id' => $warga->id,
                'nama' => $warga->nama_lengkap,
                'nik' => $warga->nik,
                'alamat' => $warga->alamat,
                'status' => $warga->status
            ]
        ]);
    }
}
