@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3 class="fw-bold">
                <i class="fas fa-credit-card text-primary me-2"></i> 
                Riwayat Pembayaran Iuran
            </h3>
            <p class="text-muted">Semua pembayaran iuran yang telah dilakukan</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('pembayaran.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus-circle me-2"></i> Bayar Iuran
            </a>
        </div>
    </div>
    
    @if($pembayarans->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5>Belum Ada Pembayaran</h5>
                <p class="text-muted">Belum ada data pembayaran iuran.</p>
                <a href="{{ route('pembayaran.create') }}" class="btn btn-primary">
                    <i class="fas fa-credit-card me-2"></i> Bayar Sekarang
                </a>
            </div>
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">#</th>
                                @if(auth()->user()->role != 'warga')
                                    <th>Nama Warga</th>
                                    <th>NIK</th>
                                @endif
                                <th>Jenis Iuran</th>
                                <th>Jumlah Dibayar</th>
                                <th>Tanggal Bayar</th>
                                <th>Status</th>
                                <th>Bukti</th>
                                @if(auth()->user()->role != 'warga')
                                    <th class="text-end pe-3">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pembayarans as $key => $item)
                            <tr>
                                <td class="ps-3">{{ $key + 1 }}</td>
                                @if(auth()->user()->role != 'warga')
                                    <td>
                                        <strong>{{ $item->warga->nama_lengkap ?? '-' }}</strong>
                                    </td>
                                    <td>{{ $item->warga->nik ?? '-' }}</td>
                                @endif
                                <td>{{ $item->iuran->nama_iuran ?? '-' }}</td>
                                <td class="text-success fw-bold">
                                    Rp {{ number_format($item->jumlah_dibayar, 0, ',', '.') }}
                                </td>
                                <td>{{ date('d/m/Y', strtotime($item->tanggal_bayar)) }}</td>
                                <td>
                                    @if($item->status == 'pending')
                                        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                            <i class="fas fa-clock me-1"></i> Pending
                                        </span>
                                    @elseif($item->status == 'verified')
                                        <span class="badge bg-success px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Verified
                                        </span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2 rounded-pill">
                                            <i class="fas fa-times-circle me-1"></i> Ditolak
                                        </span>
                                    @endif
                                 </td>
                                 <td>
                                    @if($item->bukti_transfer)
                                        <a href="{{ asset('storage/' . $item->bukti_transfer) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                 </td>
                                @if(auth()->user()->role != 'warga')
                                    <td class="text-end pe-3">
                                        @if($item->status == 'pending')
                                            <a href="{{ route('pembayaran.verify', $item->id) }}" 
                                               class="btn btn-sm btn-success me-1"
                                               onclick="return confirm('Verifikasi pembayaran ini?')">
                                                <i class="fas fa-check"></i> Verif
                                            </a>
                                            <a href="{{ route('pembayaran.reject', $item->id) }}" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Tolak pembayaran ini?')">
                                                <i class="fas fa-times"></i> Tolak
                                            </a>
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
        
        <!-- Ringkasan untuk warga -->
        @if(auth()->user()->role == 'warga')
        <div class="card mt-4 bg-light">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Total Pembayaran (Lunas)</small>
                        <h4 class="text-success">
                            Rp {{ number_format($pembayarans->where('status', 'verified')->sum('jumlah_dibayar'), 0, ',', '.') }}
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Total Pembayaran (Pending)</small>
                        <h4 class="text-warning">
                            Rp {{ number_format($pembayarans->where('status', 'pending')->sum('jumlah_dibayar'), 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection