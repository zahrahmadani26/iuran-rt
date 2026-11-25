@extends('layouts.app')

@section('content')
@php $isPengurus = in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']); @endphp

<div class="container-fluid p-0">
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-bullhorn text-primary me-2"></i>Pengumuman RT</h4>
            <p class="text-muted mb-0 small">Informasi resmi, himbauan, dan berita lingkungan RT</p>
        </div>
        @if($isPengurus)
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahPengumuman">
            <i class="fas fa-plus me-2"></i>Buat Pengumuman
        </button>
        @endif
    </div>

    @include('layouts.alerts')

    {{-- Filter Kategori --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('pengumuman.index') }}" class="btn btn-sm rounded-pill px-3 {{ !request('kategori') ? 'btn-primary' : 'btn-outline-secondary' }}">
            Semua
        </a>
        <a href="{{ route('pengumuman.index', ['kategori' => 'umum']) }}" class="btn btn-sm rounded-pill px-3 {{ request('kategori') == 'umum' ? 'btn-info text-white' : 'btn-outline-secondary' }}">
            <i class="fas fa-circle-info me-1"></i> Umum
        </a>
        <a href="{{ route('pengumuman.index', ['kategori' => 'penting']) }}" class="btn btn-sm rounded-pill px-3 {{ request('kategori') == 'penting' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">
            <i class="fas fa-star me-1"></i> Penting
        </a>
        <a href="{{ route('pengumuman.index', ['kategori' => 'darurat']) }}" class="btn btn-sm rounded-pill px-3 {{ request('kategori') == 'darurat' ? 'btn-danger' : 'btn-outline-secondary' }}">
            <i class="fas fa-triangle-exclamation me-1"></i> Darurat
        </a>
    </div>

    {{-- Daftar Pengumuman --}}
    <div class="row g-4">
        @forelse($pengumumans as $item)
        @php
            $katStyles = [
                'umum'    => ['bg' => 'bg-info-subtle', 'text' => 'text-info', 'border' => '#06b6d4', 'label' => 'Umum', 'icon' => 'fa-circle-info'],
                'penting' => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning', 'border' => '#f59e0b', 'label' => 'Penting', 'icon' => 'fa-star'],
                'darurat' => ['bg' => 'bg-danger-subtle', 'text' => 'text-danger', 'border' => '#ef4444', 'label' => 'Darurat', 'icon' => 'fa-triangle-exclamation'],
            ];
            $st = $katStyles[$item->kategori] ?? $katStyles['umum'];
        @endphp
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="border-left: 5px solid {{ $st['border'] }} !important;">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <span class="badge {{ $st['bg'] }} {{ $st['text'] }} rounded-pill px-3 py-1">
                                <i class="fas {{ $st['icon'] }} me-1"></i> {{ $st['label'] }}
                            </span>
                            @if($item->is_pinned)
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                                <i class="fas fa-thumbtack me-1"></i> Disematkan
                            </span>
                            @endif
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i> {{ $item->created_at->diffForHumans() }} ({{ $item->created_at->format('d M Y, H:i') }})
                            </small>
                        </div>

                        @if($isPengurus)
                        <div class="d-flex gap-1">
                            <form action="{{ route('pengumuman.pin', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $item->is_pinned ? 'btn-primary' : 'btn-outline-secondary' }} rounded-3" title="{{ $item->is_pinned ? 'Lepas Sematan' : 'Sematkan' }}">
                                    <i class="fas fa-thumbtack"></i>
                                </button>
                            </form>
                            <button class="btn btn-sm btn-outline-warning rounded-3" data-bs-toggle="modal" data-bs-target="#modalEditPengumuman{{ $item->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger rounded-3 delete-confirm" data-id="pengumuman-{{ $item->id }}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-form-pengumuman-{{ $item->id }}" action="{{ route('pengumuman.destroy', $item->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                        @endif
                    </div>

                    <h5 class="fw-bold text-dark mt-2 mb-2">{{ $item->judul }}</h5>
                    <div class="text-secondary" style="white-space: pre-line; line-height: 1.65;">{{ $item->konten }}</div>

                    <div class="mt-3 pt-3 border-top d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width:28px;height:28px;font-size:.75rem;">
                            {{ strtoupper(substr($item->user->name ?? 'A', 0, 1)) }}
                        </div>
                        <small class="text-muted">Diposting oleh <strong>{{ $item->user->name ?? 'Pengurus RT' }}</strong></small>
                    </div>
                </div>
            </div>
        </div>

        @if($isPengurus)
        {{-- Modal Edit Pengumuman --}}
        <div class="modal fade" id="modalEditPengumuman{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <form action="{{ route('pengumuman.update', $item->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header border-0 px-4 pt-4">
                            <h5 class="modal-title fw-bold">Edit Pengumuman</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Judul Pengumuman</label>
                                <input type="text" name="judul" class="form-control rounded-3" value="{{ $item->judul }}" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kategori</label>
                                    <select name="kategori" class="form-select rounded-3" required>
                                        <option value="umum" {{ $item->kategori == 'umum' ? 'selected' : '' }}>Umum</option>
                                        <option value="penting" {{ $item->kategori == 'penting' ? 'selected' : '' }}>Penting</option>
                                        <option value="darurat" {{ $item->kategori == 'darurat' ? 'selected' : '' }}>Darurat</option>
                                    </select>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="pinEdit{{ $item->id }}" {{ $item->is_pinned ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="pinEdit{{ $item->id }}">Sematkan di paling atas</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Isi Pengumuman</label>
                                <textarea name="konten" rows="5" class="form-control rounded-3" required>{{ $item->konten }}</textarea>
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
        @endif
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="fas fa-bullhorn fa-3x text-muted opacity-50 mb-3"></i>
                <h6 class="fw-bold text-dark">Belum Ada Pengumuman</h6>
                <p class="text-muted small mb-0">Pengumuman dari Ketua RT / Pengurus akan tampil di halaman ini.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@if($isPengurus)
{{-- Modal Tambah Pengumuman --}}
<div class="modal fade" id="modalTambahPengumuman" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('pengumuman.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-bullhorn text-primary me-2"></i>Buat Pengumuman Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Kerja Bakti Minggu Pagi" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori" class="form-select rounded-3" required>
                                <option value="umum">Umum</option>
                                <option value="penting">Penting</option>
                                <option value="darurat">Darurat</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_pinned" value="1" id="pinNew">
                                <label class="form-check-label fw-semibold" for="pinNew">Sematkan di paling atas</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea name="konten" rows="5" class="form-control rounded-3" placeholder="Tuliskan detail informasi untuk seluruh warga..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-paper-plane me-1"></i> Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
