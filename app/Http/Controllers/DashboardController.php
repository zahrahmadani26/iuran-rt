<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Models\Warga;
use App\Models\Pembayaran;
use App\Models\Iuran;
use App\Models\Pengumuman;
use App\Models\SuratPengantar;
use App\Models\Laporan;
use App\Models\JadwalKegiatan;
use App\Models\KartuKeluarga;
use App\Models\PanicButton;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isPengurus = in_array($user->role, ['super_admin', 'admin', 'bendahara']);

        // Total Pemasukan & Pengeluaran
        $totalPemasukan = Keuangan::where('jenis', 'pemasukan')->sum('nominal') ?? 0;
        $totalPengeluaran = Keuangan::where('jenis', 'pengeluaran')->sum('nominal') ?? 0;
        $saldoKas = $totalPemasukan - $totalPengeluaran;

        // Persentase
        $targetPemasukan = 10000000;
        $persenPemasukan = $targetPemasukan > 0 ? round(($totalPemasukan / $targetPemasukan) * 100) : 0;
        $persenPengeluaran = $totalPemasukan > 0 ? round(($totalPengeluaran / $totalPemasukan) * 100) : 0;

        // Data Warga & KK
        $totalWarga = Warga::count();
        $wargaAktif = Warga::where('status', 'aktif')->count();
        $persenWargaAktif = $totalWarga > 0 ? round(($wargaAktif / $totalWarga) * 100) : 0;
        $totalAnggotaKK = KartuKeluarga::count();

        // Pembayaran
        $telatBayar = Pembayaran::where('status', 'pending')->where('tanggal_bayar', '<', now())->count();
        $pendingCount = Pembayaran::where('status', 'pending')->count();

        // Iuran
        $totalTargetIuran = Iuran::count();
        $totalIuranTerkumpul = Pembayaran::where('status', 'verified')->count();
        $persenIuranTerkumpul = $totalTargetIuran > 0 ? round(($totalIuranTerkumpul / $totalTargetIuran) * 100) : 0;

        // Transaksi bulan ini (SQLite compatible)
        $currentMonth = date('m');
        $currentYear  = date('Y');
        $currentYearMonth = date('Y-m');
        $transaksiBulanIni = Keuangan::whereRaw("strftime('%m', tanggal) = ?", [$currentMonth])
            ->whereRaw("strftime('%Y', tanggal) = ?", [$currentYear])
            ->count();

        // Kepatuhan Bayar Bulan Ini (Warga Aktif yang sudah bayar vs belum)
        $wargaSudahBayarBulanIni = Pembayaran::where('status', 'verified')
            ->whereRaw("strftime('%Y-%m', tanggal_bayar) = ?", [$currentYearMonth])
            ->distinct('warga_id')
            ->count('warga_id');
        $wargaBelumBayarBulanIni = max(0, $wargaAktif - $wargaSudahBayarBulanIni);

        // Chart Data - SQLite compatible
        $chartData = Keuangan::select(
            DB::raw("strftime('%m', tanggal) as bulan_num"),
            DB::raw("SUM(CASE WHEN jenis = 'pemasukan' THEN nominal ELSE 0 END) as pemasukan"),
            DB::raw("SUM(CASE WHEN jenis = 'pengeluaran' THEN nominal ELSE 0 END) as pengeluaran")
        )
        ->whereRaw("strftime('%Y', tanggal) = ?", [$currentYear])
        ->groupBy('bulan_num')
        ->orderBy('bulan_num', 'asc')
        ->limit(6)
        ->get();

        $monthNames = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
            '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
            '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ];

        $chartData->transform(function ($item) use ($monthNames) {
            $item->bulan = $monthNames[$item->bulan_num] ?? ('Bln ' . $item->bulan_num);
            return $item;
        });

        if ($chartData->isEmpty()) {
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

        if ($kategoriPengeluaran->isEmpty()) {
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

        // === DATA FITUR BARU E-KAS RT ===
        $pengumumanTerbaru = Pengumuman::with('user')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        $jadwalMendatang = JadwalKegiatan::whereDate('tanggal', '>=', now()->toDateString())
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->limit(3)
            ->get();

        $suratPendingCount = SuratPengantar::whereIn('status', ['pending', 'diproses'])->count();
        $laporanAktifCount = Laporan::whereIn('status', ['baru', 'diproses'])->count();
        $sosAktifAlerts = PanicButton::with(['warga', 'user'])
            ->where('status', 'aktif')
            ->latest()
            ->get();

        return view('dashboard', compact(
            'isPengurus',
            'totalPemasukan', 'totalPengeluaran', 'saldoKas',
            'totalWarga', 'telatBayar', 'chartData', 'kategoriPengeluaran',
            'persenPemasukan', 'persenPengeluaran', 'wargaAktif', 'persenWargaAktif',
            'totalTargetIuran', 'totalIuranTerkumpul', 'persenIuranTerkumpul',
            'pendingCount', 'transaksiBulanIni', 'transaksiTerbaru', 'iuranJatuhTempo',
            'totalAnggotaKK', 'wargaSudahBayarBulanIni', 'wargaBelumBayarBulanIni',
            'pengumumanTerbaru', 'jadwalMendatang', 'suratPendingCount',
            'laporanAktifCount', 'sosAktifAlerts'
        ));
    }
}
