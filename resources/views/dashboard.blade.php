@extends('layouts.app')

@section('content')
<style>
    .dashboard-container {
        max-width: 100%;
        overflow-x: hidden;
    }
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    .chart-container {
        position: relative;
        width: 100%;
        min-height: 300px;
    }
    .progress-bar-custom {
        height: 8px;
        border-radius: 4px;
        background: #e0e0e0;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 1s ease;
    }
    @media (max-width: 768px) {
        .stat-card .h2 { font-size: 1.5rem; }
        .icon-circle { width: 40px; height: 40px; font-size: 18px; }
    }
</style>

<div class="dashboard-container">
    <!-- Welcome Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="text-white mb-2">
                                <i class="fas fa-home me-2"></i> Selamat Datang, {{ auth()->user()->name }}!
                            </h3>
                            <p class="text-white-50 mb-0">
                                <i class="fas fa-calendar-alt me-2"></i> {{ now()->format('l, d F Y') }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-clock me-2"></i> <span id="liveClock"></span>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <i class="fas fa-chart-line fa-4x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Pemasukan</span>
                            <h3 class="mb-0 text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-10 text-success">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress-bar-custom">
                            <div class="progress-fill bg-success" style="width: {{ $persenPemasukan ?? 75 }}%"></div>
                        </div>
                        <small class="text-muted">{{ $persenPemasukan ?? 75 }}% dari target Rp 10.000.000</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Pengeluaran</span>
                            <h3 class="mb-0 text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon-circle bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress-bar-custom">
                            <div class="progress-fill bg-danger" style="width: {{ $persenPengeluaran ?? 40 }}%"></div>
                        </div>
                        <small class="text-muted">{{ $persenPengeluaran ?? 40 }}% dari total pemasukan</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Saldo Kas</span>
                            <h3 class="mb-0 text-info">Rp {{ number_format($saldoKas, 0, ',', '.') }}</h3>
                        </div>
                        <div class="icon-circle bg-info bg-opacity-10 text-info">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-{{ $saldoKas >= 0 ? 'success' : 'danger' }}">
                            <i class="fas fa-{{ $saldoKas >= 0 ? 'check-circle' : 'exclamation-triangle' }}"></i>
                            {{ $saldoKas >= 0 ? 'Keuangan Sehat' : 'Defisit, perlu evaluasi' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Warga</span>
                            <h3 class="mb-0 text-primary">{{ $totalWarga }} KK</h3>
                        </div>
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-warning">
                            <i class="fas fa-exclamation-triangle"></i> {{ $telatBayar }} orang telat bayar
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Iuran Terkumpul</span>
                            <h4 class="mb-0 text-success">{{ $totalIuranTerkumpul ?? 0 }} / {{ $totalTargetIuran ?? 0 }}</h4>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-10 text-success">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress-bar-custom">
                            <div class="progress-fill bg-success" style="width: {{ $persenIuranTerkumpul ?? 0 }}%"></div>
                        </div>
                        <small class="text-muted">{{ $persenIuranTerkumpul ?? 0 }}% iuran sudah terbayar</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Pending Verifikasi</span>
                            <h4 class="mb-0 text-warning">{{ $pendingCount ?? 0 }}</h4>
                        </div>
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Menunggu verifikasi pembayaran</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Warga Aktif</span>
                            <h4 class="mb-0 text-success">{{ $wargaAktif ?? $totalWarga }} / {{ $totalWarga }}</h4>
                        </div>
                        <div class="icon-circle bg-success bg-opacity-10 text-success">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="progress-bar-custom">
                            <div class="progress-fill bg-success" style="width: {{ $persenWargaAktif ?? 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Transaksi Bulan Ini</span>
                            <h4 class="mb-0 text-info">{{ $transaksiBulanIni ?? 0 }}</h4>
                        </div>
                        <div class="icon-circle bg-info bg-opacity-10 text-info">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">Pemasukan & Pengeluaran</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i> Grafik Keuangan Bulanan</h5>
                        <div class="btn-group btn-group-sm mt-2 mt-sm-0">
                            <button class="btn btn-outline-primary" onclick="changeChartType('line')">Line</button>
                            <button class="btn btn-outline-primary" onclick="changeChartType('bar')">Bar</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="cashChart" style="width:100%; height:300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-primary me-2"></i> Pengeluaran per Kategori</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="categoryChart" style="width:100%; height:300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Upcoming -->
    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0"><i class="fas fa-history text-primary me-2"></i> Transaksi Terbaru</h5>
                    <a href="{{ route('keuangan.index') }}" class="btn btn-sm btn-link">Lihat Semua →</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th class="text-end">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru ?? [] as $transaksi)
                                <tr>
                                    <td class="small">{{ date('d/m/Y', strtotime($transaksi->tanggal)) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaksi->jenis == 'pemasukan' ? 'success' : 'danger' }} rounded-pill">
                                            {{ ucfirst($transaksi->jenis) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaksi->kategori }}</td>
                                    <td>{{ Str::limit($transaksi->deskripsi, 30) }}</td>
                                    <td class="text-end fw-bold">
                                        <span class="text-{{ $transaksi->jenis == 'pemasukan' ? 'success' : 'danger' }}">
                                            {{ $transaksi->jenis == 'pemasukan' ? '+' : '-' }}
                                            Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        Belum ada transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center flex-wrap">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-danger me-2"></i> Jatuh Tempo</h5>
                    <a href="{{ route('pembayaran.create') }}" class="btn btn-sm btn-success">Bayar →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($iuranJatuhTempo ?? [] as $iuran)
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $iuran->nama_iuran }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ date('d/m/Y', strtotime($iuran->tanggal_jatuh_tempo)) }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-warning rounded-pill">Belum Bayar</span>
                                <h6 class="mb-0 mt-1">Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</h6>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
                        <p>Tidak ada iuran jatuh tempo</p>
                    </div>
                    @endforelse
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm rounded-4 mt-3">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i> Aksi Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('pembayaran.create') }}" class="btn btn-outline-primary w-100 py-3 rounded-4">
                                <i class="fas fa-credit-card fa-2x d-block mb-2"></i>
                                <small>Bayar Iuran</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('keuangan.create') }}" class="btn btn-outline-success w-100 py-3 rounded-4">
                                <i class="fas fa-plus-circle fa-2x d-block mb-2"></i>
                                <small>Tambah Transaksi</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('warga.create') }}" class="btn btn-outline-info w-100 py-3 rounded-4">
                                <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                                <small>Tambah Warga</small>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('keuangan.export') }}" class="btn btn-outline-danger w-100 py-3 rounded-4">
                                <i class="fas fa-file-excel fa-2x d-block mb-2"></i>
                                <small>Export Laporan</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Live Clock
function updateClock() {
    const now = new Date();
    document.getElementById('liveClock').innerHTML = now.toLocaleTimeString('id-ID');
}
setInterval(updateClock, 1000);
updateClock();

let cashChart = null;

function changeChartType(type) {
    if(cashChart) cashChart.destroy();
    createChart(type);
}

function createChart(type = 'line') {
    const chartData = @json($chartData ?? collect([]));
    const labels = chartData.map(item => item.bulan);
    const pemasukan = chartData.map(item => Number(item.pemasukan) || 0);
    const pengeluaran = chartData.map(item => Number(item.pengeluaran) || 0);
    
    const ctx = document.getElementById('cashChart').getContext('2d');
    cashChart = new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: pemasukan,
                    borderColor: '#28a745',
                    backgroundColor: type === 'line' ? 'rgba(40,167,69,0.1)' : '#28a745',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: type === 'line'
                },
                {
                    label: 'Pengeluaran',
                    data: pengeluaran,
                    borderColor: '#dc3545',
                    backgroundColor: type === 'line' ? 'rgba(220,53,69,0.1)' : '#dc3545',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: type === 'line'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

// Category Chart
const kategoriData = @json($kategoriPengeluaran ?? collect([]));
const kategoriCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(kategoriCtx, {
    type: 'doughnut',
    data: {
        labels: kategoriData.map(item => item.kategori),
        datasets: [{
            data: kategoriData.map(item => Number(item.total) || 0),
            backgroundColor: ['#667eea', '#f093fb', '#4facfe', '#fa709a', '#43e97b', '#f6c23e', '#e74a3b'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 10 } },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const value = context.raw;
                        const total = context.dataset.data.reduce((a,b) => a+b, 0);
                        const percent = total > 0 ? ((value/total)*100).toFixed(1) : 0;
                        return `${context.label}: Rp ${value.toLocaleString('id-ID')} (${percent}%)`;
                    }
                }
            }
        }
    }
});

createChart('line');
</script>
@endpush