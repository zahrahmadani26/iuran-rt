@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-wallet text-primary me-2"></i>Laporan Keuangan</h4>
            <p class="text-muted mb-0 small">Catatan pemasukan dan pengeluaran kas RT</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('keuangan.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Tambah Transaksi
            </a>
            <a href="{{ route('keuangan.export') }}" class="btn btn-success rounded-pill px-4">
                <i class="fas fa-file-excel me-2"></i> Export Excel
            </a>
        </div>
    </div>

    @include('layouts.alerts')

    {{-- Summary cards --}}
    @php
        $totalPemasukan = $keuangans->where('jenis','pemasukan')->sum('nominal');
        $totalPengeluaran = $keuangans->where('jenis','pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 rounded-4 h-100" style="background:linear-gradient(135deg,#059669,#047857);">
                <div class="card-body d-flex align-items-center gap-3 text-white p-4">
                    <div class="rounded-3 p-3 bg-white bg-opacity-25">
                        <i class="fas fa-arrow-circle-up fa-lg"></i>
                    </div>
                    <div>
                        <div class="small opacity-75">Total Pemasukan</div>
                        <div class="fw-bold fs-5">Rp {{ number_format($totalPemasukan,0,',','.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 h-100" style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
                <div class="card-body d-flex align-items-center gap-3 text-white p-4">
                    <div class="rounded-3 p-3 bg-white bg-opacity-25">
                        <i class="fas fa-arrow-circle-down fa-lg"></i>
                    </div>
                    <div>
                        <div class="small opacity-75">Total Pengeluaran</div>
                        <div class="fw-bold fs-5">Rp {{ number_format($totalPengeluaran,0,',','.') }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 rounded-4 h-100" style="background:linear-gradient(135deg,#4f46e5,#3730a3);">
                <div class="card-body d-flex align-items-center gap-3 text-white p-4">
                    <div class="rounded-3 p-3 bg-white bg-opacity-25">
                        <i class="fas fa-balance-scale fa-lg"></i>
                    </div>
                    <div>
                        <div class="small opacity-75">Saldo Kas</div>
                        <div class="fw-bold fs-5">Rp {{ number_format($saldo,0,',','.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keuangans as $key => $keuangan)
                        <tr>
                            <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                            <td class="text-muted small">{{ date('d M Y', strtotime($keuangan->tanggal)) }}</td>
                            <td>
                                @if($keuangan->jenis == 'pemasukan')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                        <i class="fas fa-arrow-up me-1"></i> Pemasukan
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                        <i class="fas fa-arrow-down me-1"></i> Pengeluaran
                                    </span>
                                @endif
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $keuangan->kategori }}</span></td>
                            <td class="text-muted">{{ $keuangan->deskripsi }}</td>
                            <td class="fw-semibold {{ $keuangan->jenis=='pemasukan'?'text-success':'text-danger' }}">
                                {{ $keuangan->jenis=='pemasukan' ? '+' : '-' }} Rp {{ number_format($keuangan->nominal,0,',','.') }}
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('keuangan.edit', $keuangan->id) }}"
                                   class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-confirm"
                                        data-id="{{ $keuangan->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $keuangan->id }}"
                                      action="{{ route('keuangan.destroy', $keuangan->id) }}"
                                      method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-2">Belum ada data keuangan</p>
                                <a href="{{ route('keuangan.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="fas fa-plus me-1"></i> Tambah Transaksi
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
