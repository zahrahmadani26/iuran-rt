<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\Warga;
use App\Models\Pembayaran;
use App\Models\Iuran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Pemasukan & Pengeluaran
        $totalPemasukan = Keuangan::where('jenis', 'pemasukan')->sum('nominal') ?? 0;
        $totalPengeluaran = Keuangan::where('jenis', 'pengeluaran')->sum('nominal') ?? 0;
        $saldoKas = $totalPemasukan - $totalPengeluaran;
        
        // Persentase
        $targetPemasukan = 10000000;
        $persenPemasukan = $targetPemasukan > 0 ? round(($totalPemasukan / $targetPemasukan) * 100) : 0;
        $persenPengeluaran = $totalPemasukan > 0 ? round(($totalPengeluaran / $totalPemasukan) * 100) : 0;
        
        // Data Warga
        $totalWarga = Warga::count();
        $wargaAktif = Warga::where('status', 'aktif')->count();
        $persenWargaAktif = $totalWarga > 0 ? round(($wargaAktif / $totalWarga) * 100) : 0;
        
        // Pembayaran
        $telatBayar = Pembayaran::where('status', 'pending')->where('tanggal_bayar', '<', now())->count();
        $pendingCount = Pembayaran::where('status', 'pending')->count();
        
        // Iuran
        $totalTargetIuran = Iuran::count();
        $totalIuranTerkumpul = Pembayaran::where('status', 'verified')->count();
        $persenIuranTerkumpul = $totalTargetIuran > 0 ? round(($totalIuranTerkumpul / $totalTargetIuran) * 100) : 0;
        
        // Transaksi bulan ini
        $transaksiBulanIni = Keuangan::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->count();
        
                // Chart Data - Perbaikan query
        $chartData = Keuangan::select(
            DB::raw('DATE_FORMAT(tanggal, "%b") as bulan'),
            DB::raw('MONTH(tanggal) as bulan_num'),
            DB::raw('SUM(CASE WHEN jenis = "pemasukan" THEN nominal ELSE 0 END) as pemasukan'),
            DB::raw('SUM(CASE WHEN jenis = "pengeluaran" THEN nominal ELSE 0 END) as pengeluaran')
        )
        ->whereYear('tanggal', date('Y'))
        ->groupBy('bulan', 'bulan_num')
        ->orderBy('bulan_num', 'asc')
        ->limit(6)
        ->get();
        
        if($chartData->isEmpty()) {
            $chartData = collect([
                (object)['bulan'=>'Jan','pemasukan'=>0,'pengeluaran'=>0],
                (object)['bulan'=>'Feb','pemasukan'=>0,'pengeluaran'=>0],
                (object)['bulan'=>'Mar','pemasukan'=>0,'pengeluaran'=>0],
                (object)['bulan'=>'Apr','pemasukan'=>0,'pengeluaran'=>0],
                (object)['bulan'=>'Mei','pemasukan'=>0,'pengeluaran'=>0],
                (object)['bulan'=>'Jun','pemasukan'=>0,'pengeluaran'=>0],
            ]);
        }
        
        // Kategori Pengeluaran
        $kategoriPengeluaran = Keuangan::where('jenis', 'pengeluaran')
            ->select('kategori', DB::raw('SUM(nominal) as total'))
            ->groupBy('kategori')
            ->get();
        
        if($kategoriPengeluaran->isEmpty()) {
            $kategoriPengeluaran = collect([
                (object)['kategori'=>'Kegiatan','total'=>0],
                (object)['kategori'=>'Kebersihan','total'=>0],
                (object)['kategori'=>'Perawatan','total'=>0],
                (object)['kategori'=>'Lainnya','total'=>0],
            ]);
        }
        
        // Transaksi Terbaru
        $transaksiTerbaru = Keuangan::orderBy('tanggal', 'desc')->limit(5)->get();
        
        // Iuran Jatuh Tempo
        $iuranJatuhTempo = Iuran::where('tanggal_jatuh_tempo', '>=', now())
            ->where('tanggal_jatuh_tempo', '<=', now()->addDays(7))
            ->get();
        
        return view('dashboard', compact(
            'totalPemasukan', 'totalPengeluaran', 'saldoKas',
            'totalWarga', 'telatBayar', 'chartData', 'kategoriPengeluaran',
            'persenPemasukan', 'persenPengeluaran', 'wargaAktif', 'persenWargaAktif',
            'totalTargetIuran', 'totalIuranTerkumpul', 'persenIuranTerkumpul',
            'pendingCount', 'transaksiBulanIni', 'transaksiTerbaru', 'iuranJatuhTempo'
        ));
    }
}