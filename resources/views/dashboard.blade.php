@extends('layouts.app')

@section('content')
<style>
    .stat-card {
        border-radius: 16px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        background: #ffffff;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08) !important;
    }
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }
    .chart-box {
        position: relative;
        height: 290px;
        width: 100%;
    }
    .progress-bar-custom {
        height: 6px;
        border-radius: 9999px;
        background: #e2e8f0;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.8s ease;
    }
    .action-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 15px 10px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #334155;
        text-decoration: none;
        transition: all 0.2s ease;
        text-align: center;
    }
    .action-tile:hover {
        border-color: #4f46e5;
        background: #f8fafc;
        color: #4f46e5;
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(79, 70, 229, 0.1);
    }
    .action-tile i {
        font-size: 22px;
        margin-bottom: 8px;
        transition: transform 0.2s ease;
    }
    .action-tile:hover i { transform: scale(1.1); }
    .table > :not(caption) > * > * { padding: 12px 16px; }
</style>

<div class="container-fluid p-0">

    {{-- Alert Darurat SOS Aktif --}}
    @if(isset($sosAktifAlerts) && $sosAktifAlerts->count() > 0)
    <div class="alert border-0 shadow-sm rounded-4 mb-4 p-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: linear-gradient(135deg, #fee2e2, #fecaca); border-left: 5px solid #dc2626 !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width:44px;height:44px;">
                <i class="fas fa-triangle-exclamation fa-beat"></i>
            </div>
            <div>
                <h6 class="fw-bold text-danger mb-0">🚨 PERINGATAN DARURAT SOS AKTIF ({{ $sosAktifAlerts->count() }})</h6>
                <small class="text-dark">
                    {{ $sosAktifAlerts->first()->warga->nama_lengkap ?? $sosAktifAlerts->first()->user->name ?? 'Warga' }}
                    — <strong>{{ strtoupper($sosAktifAlerts->first()->jenis) }}</strong> di {{ $sosAktifAlerts->first()->lokasi }}
                    ({{ $sosAktifAlerts->first()->created_at->diffForHumans() }})
                </small>
            </div>
        </div>
        <a href="{{ route('panic.index') }}" class="btn btn-danger rounded-pill px-4 fw-bold">
            <i class="fas fa-eye me-1"></i> Tangani Sekarang
        </a>
    </div>
    @endif

    <!-- Welcome Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-2">
                                <i class="fas fa-sparkles me-1"></i>
                                {{ $isPengurus ? 'Dashboard Pengurus RT' : 'Portal Warga RT Digital' }}
                            </span>
                            <h2 class="fw-bold mb-1">
                                Selamat Datang, {{ auth()->user()->name }}! 👋
                            </h2>
                            <p class="text-white-50 mb-0 small">
                                Transparansi kas lingkungan, layanan surat digital, informasi warga, dan keamanan RT dalam satu portal.
                            </p>
                        </div>
                        <div class="col-lg-4 text-lg-end">
                            <div class="d-inline-flex flex-column align-items-lg-end bg-white bg-opacity-15 px-3 py-2 rounded-3 backdrop-blur">
                                <span class="small text-white-50">
                                    <i class="fas fa-calendar-day me-1"></i> {{ now()->isoFormat('dddd, D MMMM Y') }}
                                </span>
                                <span class="fs-5 fw-bold font-monospace" id="liveClock">--:--:--</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1: Key Financial & Demography Figures -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Pemasukan</span>
                        <h4 class="fw-bold text-success mt-1 mb-0">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h4>
                    </div>
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-arrow-down-left"></i>
                    </div>
                </div>
                <div class="mt-auto">
                    <div class="progress-bar-custom mb-1">
                        <div class="progress-fill bg-success" style="width: {{ min(100, $persenPemasukan ?? 0) }}%"></div>
                    </div>
                    <small class="text-muted d-flex justify-content-between">
                        <span>Target: Rp 10 Jt</span>
                        <span class="fw-semibold text-success">{{ $persenPemasukan ?? 0 }}%</span>
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Total Pengeluaran</span>
                        <h4 class="fw-bold text-danger mt-1 mb-0">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h4>
                    </div>
                    <div class="icon-box bg-danger bg-opacity-10 text-danger">
                        <i class="fas fa-arrow-up-right"></i>
                    </div>
                </div>
                <div class="mt-auto">
                    <div class="progress-bar-custom mb-1">
                        <div class="progress-fill bg-danger" style="width: {{ min(100, $persenPengeluaran ?? 0) }}%"></div>
                    </div>
                    <small class="text-muted d-flex justify-content-between">
                        <span>Rasio Pengeluaran</span>
                        <span class="fw-semibold text-danger">{{ $persenPengeluaran ?? 0 }}%</span>
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Saldo Kas Saat Ini</span>
                        <h4 class="fw-bold {{ $saldoKas >= 0 ? 'text-primary' : 'text-danger' }} mt-1 mb-0">
                            Rp {{ number_format($saldoKas, 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
                <div class="mt-auto">
                    <span class="badge {{ $saldoKas >= 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1">
                        <i class="fas fa-{{ $saldoKas >= 0 ? 'circle-check' : 'triangle-exclamation' }} me-1"></i>
                        {{ $saldoKas >= 0 ? 'Kas Surplus / Aman' : 'Kas Defisit' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Data Warga & KK</span>
                        <h4 class="fw-bold text-dark mt-1 mb-0">{{ $totalWarga }} <span class="fs-6 fw-normal text-muted">KK</span></h4>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-auto d-flex flex-wrap gap-1">
                    <span class="badge bg-info-subtle text-info rounded-pill px-2 py-1">
                        <i class="fas fa-user-group me-1"></i> {{ $totalAnggotaKK ?? 0 }} Anggota KK
                    </span>
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                        {{ $wargaAktif }} Aktif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Status Layanan & Kependudukan -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('pembayaran.index') }}" class="text-decoration-none">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Sudah Bayar Bulan Ini</span>
                            <h5 class="fw-bold mb-0 text-dark">{{ $wargaSudahBayarBulanIni ?? 0 }} / {{ $wargaAktif ?? 0 }} <small class="text-muted fw-normal">KK</small></h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('surat.index') }}" class="text-decoration-none">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-file-signature"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Surat Menunggu Proses</span>
                            <h5 class="fw-bold mb-0 text-primary">{{ $suratPendingCount ?? 0 }} <small class="text-muted fw-normal">Pengajuan</small></h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('laporan.index') }}" class="text-decoration-none">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Laporan Warga Aktif</span>
                            <h5 class="fw-bold mb-0 text-warning">{{ $laporanAktifCount ?? 0 }} <small class="text-muted fw-normal">Laporan</small></h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('pembayaran.index') }}" class="text-decoration-none">
                <div class="card stat-card h-100 p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Verifikasi Pembayaran</span>
                            <h5 class="fw-bold mb-0 text-dark">{{ $pendingCount ?? 0 }} <small class="text-muted fw-normal">Menunggu</small></h5>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Row 3: Charts (Arus Kas + Kepatuhan Iuran + Kategori Pengeluaran) -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6 col-xl-5">
            <div class="card stat-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Arus Kas Bulanan ({{ date('Y') }})</h5>
                        <small class="text-muted">Pemasukan vs Pengeluaran</small>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnChartLine" onclick="changeChartType('line')">Garis</button>
                        <button type="button" class="btn btn-outline-primary" id="btnChartBar" onclick="changeChartType('bar')">Batang</button>
                    </div>
                </div>
                <div class="chart-box">
                    <canvas id="cashChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-xl-4">
            <div class="card stat-card h-100 p-4">
                <div class="mb-3">
                    <h5 class="fw-bold mb-0">Kategori Pengeluaran</h5>
                    <small class="text-muted">Alokasi dana operasional RT</small>
                </div>
                <div class="chart-box d-flex align-items-center justify-content-center">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-12 col-xl-3">
            <div class="card stat-card h-100 p-4">
                <div class="mb-3">
                    <h5 class="fw-bold mb-0">Kepatuhan Bulan Ini</h5>
                    <small class="text-muted">Status bayar iuran ({{ date('M Y') }})</small>
                </div>
                <div class="chart-box d-flex align-items-center justify-content-center" style="height: 220px;">
                    <canvas id="complianceChart"></canvas>
                </div>
                <div class="d-flex justify-content-around text-center mt-3 pt-2 border-top">
                    <div>
                        <div class="fw-bold text-success fs-5">{{ $wargaSudahBayarBulanIni ?? 0 }}</div>
                        <small class="text-muted">Sudah Bayar</small>
                    </div>
                    <div>
                        <div class="fw-bold text-danger fs-5">{{ $wargaBelumBayarBulanIni ?? 0 }}</div>
                        <small class="text-muted">Belum Bayar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Pengumuman Terbaru & Jadwal Kegiatan -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card stat-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="fas fa-bullhorn text-primary me-2"></i>Pengumuman Terbaru</h5>
                        <small class="text-muted">Informasi terkini dari Pengurus RT</small>
                    </div>
                    <a href="{{ route('pengumuman.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                @forelse($pengumumanTerbaru ?? [] as $p)
                <div class="p-3 rounded-3 bg-light mb-2 border-start border-4 {{ $p->kategori == 'darurat' ? 'border-danger' : ($p->kategori == 'penting' ? 'border-warning' : 'border-primary') }}">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark small">
                            @if($p->is_pinned)<i class="fas fa-thumbtack text-primary me-1"></i>@endif
                            {{ $p->judul }}
                        </span>
                        <small class="text-muted" style="font-size:.75rem;">{{ $p->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="text-muted small mb-0">{{ Str::limit($p->konten, 110) }}</p>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-bullhorn fa-2x opacity-50 mb-2 d-block"></i>
                    <small>Belum ada pengumuman terbaru.</small>
                </div>
                @endforelse
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card stat-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0"><i class="fas fa-calendar-days text-success me-2"></i>Agenda Mendatang</h5>
                        <small class="text-muted">Jadwal kegiatan warga RT terdekat</small>
                    </div>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
                </div>
                @forelse($jadwalMendatang ?? [] as $j)
                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light mb-2">
                    <div class="text-center rounded-3 px-2 py-1 text-white flex-shrink-0" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);min-width:52px;">
                        <div class="fw-bold lh-1">{{ $j->tanggal->format('d') }}</div>
                        <small style="font-size:.65rem;">{{ $j->tanggal->format('M') }}</small>
                    </div>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-bold text-dark small text-truncate">{{ $j->judul }}</div>
                        <small class="text-muted d-block">
                            <i class="fas fa-clock me-1"></i> {{ substr($j->jam_mulai, 0, 5) }} WIB
                            @if($j->lokasi) · <i class="fas fa-location-dot me-1"></i> {{ $j->lokasi }} @endif
                        </small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ ucfirst(str_replace('_',' ',$j->kategori)) }}</span>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-calendar-check fa-2x opacity-50 mb-2 d-block"></i>
                    <small>Belum ada agenda kegiatan mendatang.</small>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Row 5: Recent Transactions & Quick Actions -->
    <div class="row g-4">
        <div class="col-lg-7 col-xl-8">
            <div class="card stat-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Transaksi Kas Terbaru</h5>
                        <small class="text-muted">5 catatan keuangan terkini (transparansi publik RT)</small>
                    </div>
                    @if($isPengurus)
                    <a href="{{ route('keuangan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Kategori</th>
                                <th>Deskripsi</th>
                                <th class="text-end pe-4">Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerbaru ?? [] as $transaksi)
                            <tr>
                                <td class="ps-4 text-nowrap">
                                    <span class="small fw-semibold text-dark">{{ date('d M Y', strtotime($transaksi->tanggal)) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $transaksi->jenis == 'pemasukan' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} rounded-pill px-2 py-1">
                                        {{ $transaksi->kategori }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;">{{ $transaksi->deskripsi }}</span>
                                </td>
                                <td class="text-end pe-4 text-nowrap fw-bold {{ $transaksi->jenis == 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                    {{ $transaksi->jenis == 'pemasukan' ? '+' : '-' }} Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                    Belum ada catatan transaksi kas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4">
            <div class="card stat-card p-4 mb-4">
                <h5 class="fw-bold mb-3">Aksi Cepat</h5>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('pembayaran.create') }}" class="action-tile">
                            <i class="fas fa-credit-card text-primary"></i>
                            <span class="small fw-semibold">Bayar Iuran</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('surat.index') }}" class="action-tile">
                            <i class="fas fa-file-signature text-info"></i>
                            <span class="small fw-semibold">Ajukan Surat</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('laporan.index') }}" class="action-tile">
                            <i class="fas fa-comments text-warning"></i>
                            <span class="small fw-semibold">Lapor Masalah</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('panic.index') }}" class="action-tile border-danger-subtle">
                            <i class="fas fa-triangle-exclamation text-danger"></i>
                            <span class="small fw-semibold text-danger">SOS Darurat</span>
                        </a>
                    </div>
                    @if($isPengurus)
                    <div class="col-6">
                        <a href="{{ route('keuangan.create') }}" class="action-tile">
                            <i class="fas fa-circle-plus text-success"></i>
                            <span class="small fw-semibold">+ Kas Masuk</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('keuangan.export') }}" class="action-tile">
                            <i class="fas fa-file-excel text-success"></i>
                            <span class="small fw-semibold">Unduh Excel</span>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card stat-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Iuran Jatuh Tempo</h5>
                    <span class="badge bg-danger-subtle text-danger rounded-pill px-2">7 Hari Ke Depan</span>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($iuranJatuhTempo ?? [] as $iuran)
                    <div class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-semibold">{{ $iuran->nama_iuran }}</h6>
                            <small class="text-muted"><i class="fas fa-clock me-1 text-danger"></i> {{ date('d M Y', strtotime($iuran->tanggal_jatuh_tempo)) }}</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning-subtle text-warning mb-1">Tagihan</span>
                            <div class="fw-bold text-dark">Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-circle-check text-success fa-2x mb-2 d-block"></i>
                        <span class="small">Tidak ada tagihan yang mendekati jatuh tempo.</span>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const el = document.getElementById('liveClock');
        if (el) el.textContent = timeStr.replace(/\./g, ':');
    }
    setInterval(updateClock, 1000);
    updateClock();

    let cashChart = null;

    function changeChartType(type) {
        document.getElementById('btnChartLine').classList.toggle('active', type === 'line');
        document.getElementById('btnChartBar').classList.toggle('active', type === 'bar');
        if (cashChart) cashChart.destroy();
        createCashChart(type);
    }

    function createCashChart(type = 'line') {
        const chartData = @json($chartData ?? collect([]));
        const labels = chartData.map(item => item.bulan);
        const pemasukan = chartData.map(item => Number(item.pemasukan) || 0);
        const pengeluaran = chartData.map(item => Number(item.pengeluaran) || 0);

        const ctx = document.getElementById('cashChart');
        if (!ctx) return;

        cashChart = new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan (Rp)',
                        data: pemasukan,
                        borderColor: '#10b981',
                        backgroundColor: type === 'line' ? 'rgba(16, 185, 129, 0.12)' : '#10b981',
                        borderRadius: type === 'bar' ? 6 : 0,
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: type === 'line'
                    },
                    {
                        label: 'Pengeluaran (Rp)',
                        data: pengeluaran,
                        borderColor: '#ef4444',
                        backgroundColor: type === 'line' ? 'rgba(239, 68, 68, 0.12)' : '#ef4444',
                        borderRadius: type === 'bar' ? 6 : 0,
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: type === 'line'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true, font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 } } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': Rp ' + Number(ctx.raw).toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: v => v >= 1000000 ? 'Rp ' + (v/1000000) + ' Jt' : (v >= 1000 ? 'Rp ' + (v/1000) + ' Rb' : 'Rp ' + v),
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }
                        }
                    },
                    x: { grid: { display: false }, ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } } }
                }
            }
        });
    }

    function createCategoryChart() {
        const kategoriData = @json($kategoriPengeluaran ?? collect([]));
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: kategoriData.map(item => item.kategori),
                datasets: [{
                    data: kategoriData.map(item => Number(item.total) || 0),
                    backgroundColor: ['#4f46e5', '#06b6d4', '#f59e0b', '#ec4899', '#10b981', '#8b5cf6'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } } },
                    tooltip: {
                        callbacks: {
                            label: ctx => {
                                const val = Number(ctx.raw);
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return `${ctx.label}: Rp ${val.toLocaleString('id-ID')} (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });
    }

    function createComplianceChart() {
        const ctx = document.getElementById('complianceChart');
        if (!ctx) return;

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Sudah Bayar', 'Belum Bayar'],
                datasets: [{
                    data: [{{ $wargaSudahBayarBulanIni ?? 0 }}, {{ $wargaBelumBayarBulanIni ?? 0 }}],
                    backgroundColor: ['#10b981', '#f43f5e'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    }

    createCashChart('line');
    createCategoryChart();
    createComplianceChart();
</script>
@endpush
