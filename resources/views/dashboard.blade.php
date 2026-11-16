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
        height: 300px;
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
        padding: 16px 12px;
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
        font-size: 24px;
        margin-bottom: 8px;
        transition: transform 0.2s ease;
    }
    .action-tile:hover i {
        transform: scale(1.1);
    }
    .table > :not(caption) > * > * {
        padding: 12px 16px;
    }
</style>

<div class="container-fluid p-0">
    <!-- Welcome Header Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                <div class="card-body p-4 text-white">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-3 mb-lg-0">
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-2">
                                <i class="fas fa-sparkles me-1"></i> Dashboard Pengurus RT
                            </span>
                            <h2 class="fw-bold mb-1">
                                Selamat Datang, {{ auth()->user()->name }}! 👋
                            </h2>
                            <p class="text-white-50 mb-0 small">
                                Ringkasan keuangan kas dan status pembayaran iuran warga lingkungan hari ini.
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

    <!-- Row 1: Key Financial Figures -->
    <div class="row g-3 mb-4">
        <!-- Pemasukan -->
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

        <!-- Pengeluaran -->
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

        <!-- Saldo Kas -->
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

        <!-- Data Warga -->
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase">Data Warga</span>
                        <h4 class="fw-bold text-dark mt-1 mb-0">{{ $totalWarga }} <span class="fs-6 fw-normal text-muted">KK</span></h4>
                    </div>
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="mt-auto">
                    @if($telatBayar > 0)
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1">
                        <i class="fas fa-clock-rotate-left me-1"></i> {{ $telatBayar }} KK Menunggak
                    </span>
                    @else
                    <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                        <i class="fas fa-check me-1"></i> Seluruh Warga Tertib
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Secondary Quick Status -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Iuran Terbayar</span>
                        <h5 class="fw-bold mb-0">{{ $totalIuranTerkumpul ?? 0 }} / {{ $totalTargetIuran ?? 0 }} <small class="text-muted fw-normal">Iuran</small></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Menunggu Verifikasi</span>
                        <h5 class="fw-bold mb-0 text-warning">{{ $pendingCount ?? 0 }} <small class="text-muted fw-normal">Bukti Bayar</small></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Warga Aktif</span>
                        <h5 class="fw-bold mb-0">{{ $wargaAktif ?? $totalWarga }} / {{ $totalWarga }} <small class="text-muted fw-normal">KK</small></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card h-100 p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box bg-info bg-opacity-10 text-info">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Transaksi Bulan Ini</span>
                        <h5 class="fw-bold mb-0">{{ $transaksiBulanIni ?? 0 }} <small class="text-muted fw-normal">Catatan</small></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Charts -->
    <div class="row g-4 mb-4">
        <!-- Cashflow Chart -->
        <div class="col-lg-7 col-xl-8">
            <div class="card stat-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Arus Kas Bulanan ({{ date('Y') }})</h5>
                        <small class="text-muted">Perbandingan Pemasukan vs Pengeluaran</small>
                    </div>
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary active" id="btnChartLine" onclick="changeChartType('line')">
                            <i class="fas fa-chart-line me-1"></i> Garis
                        </button>
                        <button type="button" class="btn btn-outline-primary" id="btnChartBar" onclick="changeChartType('bar')">
                            <i class="fas fa-chart-bar me-1"></i> Batang
                        </button>
                    </div>
                </div>
                <div class="chart-box">
                    <canvas id="cashChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Expense Category Pie -->
        <div class="col-lg-5 col-xl-4">
            <div class="card stat-card h-100 p-4">
                <div class="mb-3">
                    <h5 class="fw-bold mb-0">Kategori Pengeluaran</h5>
                    <small class="text-muted">Proporsi alokasi dana operasional RT</small>
                </div>
                <div class="chart-box d-flex align-items-center justify-content-center">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Recent Transactions & Quick Actions -->
    <div class="row g-4">
        <!-- Recent Transactions -->
        <div class="col-lg-7 col-xl-8">
            <div class="card stat-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0">Transaksi Kas Terbaru</h5>
                        <small class="text-muted">5 catatan keuangan terkini</small>
                    </div>
                    <a href="{{ route('keuangan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
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
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                        {{ $transaksi->deskripsi }}
                                    </span>
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

        <!-- Quick Actions & Due Reminders -->
        <div class="col-lg-5 col-xl-4">
            <!-- Aksi Cepat -->
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
                        <a href="{{ route('keuangan.create') }}" class="action-tile">
                            <i class="fas fa-circle-plus text-success"></i>
                            <span class="small fw-semibold">+ Pemasukan</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('warga.create') }}" class="action-tile">
                            <i class="fas fa-user-plus text-info"></i>
                            <span class="small fw-semibold">Tambah Warga</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('keuangan.export') }}" class="action-tile">
                            <i class="fas fa-file-excel text-success"></i>
                            <span class="small fw-semibold">Unduh Excel</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Jatuh Tempo -->
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
                            <small class="text-muted">
                                <i class="fas fa-clock me-1 text-danger"></i> {{ date('d M Y', strtotime($iuran->tanggal_jatuh_tempo)) }}
                            </small>
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
    // Live Clock with Indonesian format
    function updateClock() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const el = document.getElementById('liveClock');
        if (el) el.textContent = timeStr.replace(/\./g, ':');
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Chart.js Setup
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
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ctx.dataset.label + ': Rp ' + Number(ctx.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(v) {
                                if (v >= 1000000) return 'Rp ' + (v / 1000000) + ' Jt';
                                if (v >= 1000) return 'Rp ' + (v / 1000) + ' Rb';
                                return 'Rp ' + v;
                            },
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }
                        }
                    }
                }
            }
        });
    }

    // Category Doughnut Chart
    function createCategoryChart() {
        const kategoriData = @json($kategoriPengeluaran ?? collect([]));
        const ctx = document.getElementById('categoryChart');
        if (!ctx) return;

        const labels = kategoriData.map(item => item.kategori);
        const data = kategoriData.map(item => Number(item.total) || 0);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
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
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
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

    createCashChart('line');
    createCategoryChart();
</script>
@endpush
