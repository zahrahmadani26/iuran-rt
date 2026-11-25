@extends('layouts.app')

@section('content')
@php $isPengurus = in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']); @endphp

<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-calendar-days text-primary me-2"></i>Jadwal Kegiatan RT</h4>
            <p class="text-muted mb-0 small">Agenda gotong royong, rapat warga, posyandu, dan kegiatan lingkungan</p>
        </div>
        @if($isPengurus)
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
            <i class="fas fa-plus me-2"></i>Tambah Kegiatan
        </button>
        @endif
    </div>

    @include('layouts.alerts')

    {{-- Filter Kategori --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('jadwal.index') }}" class="btn btn-sm rounded-pill px-3 {{ !request('kategori') ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
        @foreach(['gotong_royong'=>'Gotong Royong','rapat'=>'Rapat Warga','posyandu'=>'Posyandu','arisan'=>'Arisan','olahraga'=>'Olahraga','lainnya'=>'Lainnya'] as $k => $lbl)
            <a href="{{ route('jadwal.index', ['kategori' => $k]) }}" class="btn btn-sm rounded-pill px-3 {{ request('kategori') == $k ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $lbl }}</a>
        @endforeach
    </div>

    {{-- Agenda Mendatang --}}
    <h6 class="fw-bold text-uppercase text-muted small mb-3"><i class="fas fa-clock me-1 text-primary"></i> Agenda Mendatang</h6>
    <div class="row g-3 mb-5">
        @forelse($jadwalMendatang as $j)
        @php
            $katBadge = [
                'rapat' => 'bg-primary-subtle text-primary',
                'gotong_royong' => 'bg-success-subtle text-success',
                'posyandu' => 'bg-danger-subtle text-danger',
                'arisan' => 'bg-warning-subtle text-warning',
                'olahraga' => 'bg-info-subtle text-info',
                'lainnya' => 'bg-secondary-subtle text-secondary',
            ][$j->kategori] ?? 'bg-secondary-subtle text-secondary';
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex gap-3">
                    <div class="text-center rounded-4 p-2 d-flex flex-column justify-content-center flex-shrink-0" style="width:68px;height:74px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;">
                        <span class="fw-bold fs-4 lh-1">{{ $j->tanggal->format('d') }}</span>
                        <span class="small text-uppercase" style="font-size:.7rem;">{{ $j->tanggal->format('M Y') }}</span>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-start gap-1">
                            <span class="badge {{ $katBadge }} rounded-pill px-2 py-1 small">{{ ucfirst(str_replace('_',' ',$j->kategori)) }}</span>
                            @if($isPengurus)
                            <div class="d-flex gap-1">
                                <button class="btn btn-sm btn-link text-warning p-0 px-1" data-bs-toggle="modal" data-bs-target="#modalEditJadwal{{ $j->id }}"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-link text-danger p-0 px-1 delete-confirm" data-id="jadwal-{{ $j->id }}"><i class="fas fa-trash"></i></button>
                                <form id="delete-form-jadwal-{{ $j->id }}" action="{{ route('jadwal.destroy', $j->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                            </div>
                            @endif
                        </div>
                        <h6 class="fw-bold text-dark mt-2 mb-1">{{ $j->judul }}</h6>
                        <div class="small text-muted mb-1">
                            <i class="fas fa-clock me-1 text-primary"></i> {{ substr($j->jam_mulai, 0, 5) }} {{ $j->jam_selesai ? '- '.substr($j->jam_selesai,0,5) : 'WIB' }}
                        </div>
                        @if($j->lokasi)
                        <div class="small text-muted mb-1"><i class="fas fa-location-dot me-1 text-danger"></i> {{ $j->lokasi }}</div>
                        @endif
                        @if($j->deskripsi)
                        <p class="small text-secondary mb-0 mt-2">{{ $j->deskripsi }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center text-muted">
                <i class="fas fa-calendar-check fa-2x mb-2 opacity-50"></i>
                <p class="mb-0 small">Belum ada agenda kegiatan mendatang.</p>
            </div>
        </div>
        @endforelse
    </div>

    @if($jadwalSelesai->count() > 0)
    <h6 class="fw-bold text-uppercase text-muted small mb-3"><i class="fas fa-history me-1"></i> Kegiatan Terlaksana</h6>
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Kegiatan</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        @if($isPengurus)<th class="text-center pe-4">Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalSelesai as $j)
                    <tr>
                        <td class="ps-4 text-muted small">{{ $j->tanggal->format('d M Y') }}</td>
                        <td class="fw-semibold">{{ $j->judul }}</td>
                        <td><span class="badge bg-light text-dark border rounded-pill">{{ ucfirst(str_replace('_',' ',$j->kategori)) }}</span></td>
                        <td class="text-muted small">{{ $j->lokasi ?? '-' }}</td>
                        @if($isPengurus)
                        <td class="text-center pe-4">
                            <button class="btn btn-sm btn-outline-danger rounded-3 delete-confirm" data-id="jadwal-{{ $j->id }}"><i class="fas fa-trash"></i></button>
                            <form id="delete-form-jadwal-{{ $j->id }}" action="{{ route('jadwal.destroy', $j->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@if($isPengurus)
@foreach($jadwalMendatang as $j)
<div class="modal fade" id="modalEditJadwal{{ $j->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('jadwal.update', $j->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">Edit Jadwal Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Kegiatan</label>
                        <input type="text" name="judul" class="form-control rounded-3" value="{{ $j->judul }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori" class="form-select rounded-3">
                                @foreach(['gotong_royong'=>'Gotong Royong','rapat'=>'Rapat Warga','posyandu'=>'Posyandu','arisan'=>'Arisan','olahraga'=>'Olahraga','lainnya'=>'Lainnya'] as $k=>$lbl)
                                    <option value="{{ $k }}" {{ $j->kategori == $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="{{ $j->tanggal->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control rounded-3" value="{{ substr($j->jam_mulai,0,5) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control rounded-3" value="{{ $j->jam_selesai ? substr($j->jam_selesai,0,5) : '' }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control rounded-3" value="{{ $j->lokasi }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="form-control rounded-3">{{ $j->deskripsi }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<div class="modal fade" id="modalTambahJadwal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-calendar-plus text-primary me-2"></i>Tambah Jadwal Kegiatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Kerja Bakti Bersih Saluran" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori" class="form-select rounded-3">
                                <option value="gotong_royong">Gotong Royong</option>
                                <option value="rapat">Rapat Warga</option>
                                <option value="posyandu">Posyandu</option>
                                <option value="arisan">Arisan</option>
                                <option value="olahraga">Olahraga</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="jam_mulai" class="form-control rounded-3" value="08:00" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control rounded-3">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Contoh: Balai Warga RT 01">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="form-control rounded-3" placeholder="Bawa alat kebersihan masing-masing..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
