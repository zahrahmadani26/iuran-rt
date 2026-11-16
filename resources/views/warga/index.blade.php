@extends('layouts.app')

@section('title', 'Data Warga')

@section('content')
<div class="container-fluid p-0">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-users text-primary me-2"></i>Data Warga</h4>
            <p class="text-muted mb-0 small">Kelola data warga RT & Kartu RFID</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rfid.index') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="fas fa-id-card-clip me-1"></i> Monitor RFID
            </a>
            <a href="{{ route('warga.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Tambah Warga
            </a>
        </div>
    </div>

    @include('layouts.alerts')

    {{-- Table Card --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width:50px;">#</th>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>Kartu RFID (ESP32)</th>
                            <th>No Telp</th>
                            <th>Alamat</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($wargas as $key => $warga)
                        <tr>
                            <td class="ps-4 text-muted">{{ $key + 1 }}</td>
                            <td><code class="text-dark">{{ $warga->nik }}</code></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm d-flex align-items-center justify-content-center rounded-circle text-white fw-bold"
                                         style="width:34px;height:34px;font-size:.75rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                                        {{ strtoupper(substr($warga->nama_lengkap, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $warga->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td>
                                @if($warga->rfid_uid)
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill font-monospace">
                                        <i class="fas fa-id-card me-1"></i> {{ $warga->rfid_uid }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted px-2 py-1 rounded-pill small">
                                        <i class="fas fa-minus me-1"></i> Belum ada
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $warga->no_telp }}</td>
                            <td class="text-muted" style="max-width:180px;">{{ Str::limit($warga->alamat, 40) }}</td>
                            <td>
                                @if($warga->status == 'aktif')
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                        <i class="fas fa-circle me-1" style="font-size:.5rem;vertical-align:middle;"></i> Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                                        <i class="fas fa-circle me-1" style="font-size:.5rem;vertical-align:middle;"></i> Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('warga.edit', $warga->id) }}"
                                   class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger delete-confirm"
                                        data-id="{{ $warga->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <form id="delete-form-{{ $warga->id }}"
                                      action="{{ route('warga.destroy', $warga->id) }}"
                                      method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-2">Belum ada data warga</p>
                                <a href="{{ route('warga.create') }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                    <i class="fas fa-plus me-1"></i> Tambah Warga
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($wargas->count() > 0)
        <div class="card-footer bg-transparent border-0 px-4 py-3">
            <small class="text-muted">Total: <strong>{{ $wargas->count() }}</strong> warga</small>
        </div>
        @endif
    </div>
</div>
@endsection
