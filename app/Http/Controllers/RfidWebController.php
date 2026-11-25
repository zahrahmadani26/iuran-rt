<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\Iuran;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RfidWebController extends Controller
{
    public function index()
    {
        $totalWarga = Warga::count();
        $wargaWithRfid = Warga::whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->count();
        $wargaWithoutRfid = $totalWarga - $wargaWithRfid;

        $wargas = Warga::orderBy('nama_lengkap', 'asc')->get();
        $iurans = Iuran::all();
        $lastScan = Cache::get('last_scanned_rfid');

        // Transaksi terakhir yang menggunakan RFID
        $rfidPembayarans = Pembayaran::with(['warga', 'iuran'])
            ->where('keterangan', 'like', '%RFID%')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('rfid.index', compact(
            'totalWarga',
            'wargaWithRfid',
            'wargaWithoutRfid',
            'wargas',
            'iurans',
            'lastScan',
            'rfidPembayarans'
        ));
    }

    public function nfc()
    {
        $totalWarga = Warga::count();
        $wargaWithRfid = Warga::whereNotNull('rfid_uid')->where('rfid_uid', '!=', '')->count();
        $wargas = Warga::orderBy('nama_lengkap', 'asc')->get();
        $iurans = Iuran::all();
        $lastScan = Cache::get('last_scanned_rfid');

        // Transaksi pembayaran via NFC atau RFID
        $nfcPembayarans = Pembayaran::with(['warga', 'iuran'])
            ->where(function($q) {
                $q->where('keterangan', 'like', '%NFC%')
                  ->orWhere('keterangan', 'like', '%RFID%');
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('rfid.nfc', compact(
            'totalWarga',
            'wargaWithRfid',
            'wargas',
            'iurans',
            'lastScan',
            'nfcPembayarans'
        ));
    }
}
