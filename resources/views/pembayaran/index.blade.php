@extends('layouts.app')

@section('title', 'Pembayaran Iuran')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-credit-card text-primary me-2"></i>Pembayaran Iuran</h4>
            <p class="text-muted mb-0 small">Riwayat dan verifikasi pembayaran iuran warga</p>
        </div>
        <a href="{{ route('pembayaran.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus-circle me-2"></i> Bayar Iuran
        </a>
    </div>

    @include('layouts.alerts')

    @if($pembayarans->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                <h5 class="text-muted">Belum Ada Pembayaran</h5>
                <p class="text-muted small">Belum ada data pembayaran iuran tercatat.</p>
                <a href="{{ route('pembayaran.create') }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                @if(auth()->user()->role != 'warga')
                                    <th>Nama Warga</th>
                                    <th>NIK</th>
                                @endif
                                <th>Jenis Iuran</th>
                                <th>Jumlah</th>
                                <th>Tanggal Bayar</th>
                                <th>Status</th>
                                <th>Bukti</th>
                                @if(auth()->user()->role != 'warga')
                                    <th class="text-center pe-4">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayarans as $key => $item)
                            <tr>
                                <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                                @if(auth()->user()->role != 'warga')
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                                 style="width:32px;height:32px;font-size:.7rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                                                {{ strtoupper(substr($item->warga->nama_lengkap ?? 'W', 0, 1)) }}
                                            </div>
                                            <strong>{{ $item->warga->nama_lengkap ?? '-' }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-muted"><code>{{ $item->warga->nik ?? '-' }}</code></td>
                                @endif
                                <td class="fw-semibold">{{ $item->iuran->nama_iuran ?? '-' }}</td>
                                <td class="text-success fw-bold">Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}</td>
                                <td class="text-muted small">{{ date('d M Y', strtotime($item->tanggal_bayar)) }}</td>
                                <td>
                                    @if($item->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2 rounded-pill">
                                            <i class="fas fa-clock me-1"></i> Pending
                                        </span>
                                    @elseif($item->status == 'verified')
                                        <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                            <i class="fas fa-times-circle me-1"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->bukti_transfer)
                                        <a href="{{ asset('storage/' . $item->bukti_transfer) }}" target="_blank"
                                           class="btn btn-sm btn-outline-info rounded-pill">
                                            <i class="fas fa-eye me-1"></i> Lihat
                                        </a>
                                    @elseif(str_contains($item->keterangan ?? '', 'RFID'))
                                        <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill small" title="{{ $item->keterangan }}">
                                            <i class="fas fa-id-card me-1"></i> Tap RFID
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                @if(auth()->user()->role != 'warga')
                                    <td class="text-center pe-4">
                                        @if($item->status == 'pending')
                                            <a href="{{ route('pembayaran.verify', $item->id) }}"
                                               class="btn btn-sm btn-success me-1 rounded-pill"
                                               onclick="return confirm('Verifikasi pembayaran ini?')">
                                                <i class="fas fa-check me-1"></i> Verif
                                            </a>
                                            <a href="{{ route('pembayaran.reject', $item->id) }}"
                                               class="btn btn-sm btn-danger rounded-pill"
                                               onclick="return confirm('Tolak pembayaran ini?')">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(auth()->user()->role == 'warga')
        <div class="row g-3 mt-2">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 border-start border-success border-4">
                    <div class="card-body">
                        <small class="text-muted">Total Dibayar (Lunas)</small>
                        <h4 class="text-success fw-bold mb-0">
                            Rp {{ number_format($pembayarans->where('status','verified')->sum('jumlah_dibayar'), 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 border-start border-warning border-4">
                    <div class="card-body">
                        <small class="text-muted">Total Pending</small>
                        <h4 class="text-warning fw-bold mb-0">
                            Rp {{ number_format($pembayarans->where('status','pending')->sum('jumlah_dibayar'), 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection
