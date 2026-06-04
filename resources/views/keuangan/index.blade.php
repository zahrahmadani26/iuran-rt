@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-6">
            <h3>Laporan Keuangan</h3>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('keuangan.create') }}" class="btn btn-primary btn-animated">
                <i class="fas fa-plus"></i> Tambah Transaksi
            </a>
            <a href="{{ route('keuangan.export') }}" class="btn btn-success btn-animated">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
    
    <div class="card animated-card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keuangans as $key => $keuangan)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ date('d/m/Y', strtotime($keuangan->tanggal)) }}</td>
                            <td>
                                <span class="badge bg-{{ $keuangan->jenis == 'pemasukan' ? 'success' : 'danger' }}">
                                    {{ ucfirst($keuangan->jenis) }}
                                </span>
                            </td>
                            <td>{{ $keuangan->kategori }}</td>
                            <td>{{ $keuangan->deskripsi }}</td>
                            <td>Rp {{ number_format($keuangan->nominal, 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('keuangan.edit', $keuangan->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-confirm" data-id="{{ $keuangan->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $keuangan->id }}" action="{{ route('keuangan.destroy', $keuangan->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data keuangan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection