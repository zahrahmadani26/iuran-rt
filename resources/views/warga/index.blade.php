@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-6">
            <h3>Data Warga</h3>
        </div>
        <div class="col-6 text-end">
            <a href="{{ route('warga.create') }}" class="btn btn-primary btn-animated">
                <i class="fas fa-plus"></i> Tambah Warga
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
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>No KK</th>
                            <th>Alamat</th>
                            <th>No Telp</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wargas as $key => $warga)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $warga->nik }}</td>
                            <td>{{ $warga->nama_lengkap }}</td>
                            <td>{{ $warga->no_kk }}</td>
                            <td>{{ $warga->alamat }}</td>
                            <td>{{ $warga->no_telp }}</td>
                            <td>
                                <span class="badge bg-{{ $warga->status == 'aktif' ? 'success' : 'danger' }}">
                                    {{ $warga->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('warga.edit', $warga->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-danger delete-confirm" data-id="{{ $warga->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $warga->id }}" action="{{ route('warga.destroy', $warga->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data warga</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection