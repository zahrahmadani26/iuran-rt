@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-6">
            <h3>Data Iuran</h3>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('iuran.create') }}" class="btn btn-primary btn-animated">
                <i class="fas fa-plus"></i> Tambah Iuran
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
                            <th>Nama Iuran</th>
                            <th>Nominal</th>
                            <th>Periode</th>
                            <th>Jatuh Tempo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($iurans as $key => $iuran)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $iuran->nama_iuran }}</td>
                            <td>Rp {{ number_format($iuran->nominal, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($iuran->periode) }}</td>
                            <td>{{ date('d/m/Y', strtotime($iuran->tanggal_jatuh_tempo)) }}</td>
                            <td>
                                <a href="{{ route('iuran.edit', $iuran->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-confirm" data-id="{{ $iuran->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $iuran->id }}" action="{{ route('iuran.destroy', $iuran->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data iuran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection