@extends('layouts.app')

@section('title', 'Data Iuran')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Data Iuran</h4>
            <p class="text-muted mb-0 small">Kelola jenis dan nominal iuran RT</p>
        </div>
        <a href="{{ route('iuran.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i> Tambah Iuran
        </a>
    </div>

    @include('layouts.alerts')

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Nama Iuran</th>
                            <th>Nominal</th>
                            <th>Periode</th>
                            <th>Jatuh Tempo</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($iurans as $key => $iuran)
                        <tr>
                            <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                            <td class="fw-semibold">{{ $iuran->nama_iuran }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                    {{ ucfirst($iuran->periode) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ date('d M Y', strtotime($iuran->tanggal_jatuh_tempo)) }}</td>
                            <td class="text-center pe-4">
                                <a href="{{ route('iuran.edit', $iuran->id) }}"
                                   class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-confirm"
                                        data-id="{{ $iuran->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $iuran->id }}"
                                      action="{{ route('iuran.destroy', $iuran->id) }}"
                                      method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-file-invoice fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-2">Belum ada data iuran</p>
                                <a href="{{ route('iuran.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="fas fa-plus me-1"></i> Tambah Iuran
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
