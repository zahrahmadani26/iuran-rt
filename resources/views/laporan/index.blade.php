@extends('layouts.app')

@section('content')
@php $isPengurus = in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']); @endphp

<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-comments text-primary me-2"></i>Laporan & Aspirasi Warga</h4>
            <p class="text-muted mb-0 small">Laporkan kerusakan fasilitas, masalah kebersihan, atau berikan saran untuk kemajuan RT</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalBuatLaporan">
            <i class="fas fa-plus me-2"></i>Buat Laporan
        </button>
    </div>

    @include('layouts.alerts')

    {{-- Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-muted small">Laporan Baru</span>
                <h4 class="fw-bold text-primary mb-0">{{ $stats['baru'] }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-muted small">Sedang Diproses</span>
                <h4 class="fw-bold text-warning mb-0">{{ $stats['diproses'] }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-muted small">Selesai Ditangani</span>
                <h4 class="fw-bold text-success mb-0">{{ $stats['selesai'] }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 rounded-4 shadow-sm p-3">
                <span class="text-muted small">Total Laporan</span>
                <h4 class="fw-bold text-dark mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('laporan.index') }}" class="btn btn-sm rounded-pill px-3 {{ !request('scope') && !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">Semua Laporan</a>
        <a href="{{ route('laporan.index', ['scope' => 'saya']) }}" class="btn btn-sm rounded-pill px-3 {{ request('scope') == 'saya' ? 'btn-primary' : 'btn-outline-secondary' }}">Laporan Saya</a>
        <span class="vr mx-1"></span>
        @foreach(['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai'] as $k => $lbl)
            <a href="{{ route('laporan.index', ['status' => $k]) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == $k ? 'btn-dark' : 'btn-outline-secondary' }}">{{ $lbl }}</a>
        @endforeach
    </div>

    {{-- List --}}
    <div class="row g-4">
        @forelse($laporans as $lap)
        @php
            $statusStyle = [
                'baru'     => 'bg-primary-subtle text-primary',
                'diproses' => 'bg-warning-subtle text-warning',
                'selesai'  => 'bg-success-subtle text-success',
                'ditolak'  => 'bg-danger-subtle text-danger',
            ][$lap->status] ?? 'bg-secondary-subtle text-secondary';
        @endphp
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge {{ $statusStyle }} rounded-pill px-3 py-1">{{ ucfirst($lap->status) }}</span>
                            <span class="badge bg-light text-dark border rounded-pill px-3 py-1">{{ ucfirst($lap->kategori) }}</span>
                        </div>
                        <small class="text-muted">{{ $lap->created_at->diffForHumans() }}</small>
                    </div>

                    <h5 class="fw-bold text-dark mt-2 mb-1">{{ $lap->judul }}</h5>
                    @if($lap->lokasi)
                        <small class="text-muted mb-2"><i class="fas fa-location-dot text-danger me-1"></i> {{ $lap->lokasi }}</small>
                    @endif
                    <p class="text-secondary small mb-3" style="white-space: pre-line;">{{ $lap->deskripsi }}</p>

                    @if($lap->foto)
                        <div class="mb-3">
                            <a href="{{ asset('storage/' . $lap->foto) }}" target="_blank">
                                <img src="{{ asset('storage/' . $lap->foto) }}" alt="Foto laporan" class="img-fluid rounded-3 border" style="max-height: 200px; object-fit: cover;">
                            </a>
                        </div>
                    @endif

                    @if($lap->tanggapan_admin)
                        <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 mb-3">
                            <small class="fw-bold text-success d-block mb-1"><i class="fas fa-reply me-1"></i> Tanggapan Pengurus RT:</small>
                            <small class="text-dark">{{ $lap->tanggapan_admin }}</small>
                        </div>
                    @endif

                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i> {{ $lap->warga->nama_lengkap ?? $lap->user->name ?? 'Warga' }}
                        </small>
                        <div class="d-flex gap-1">
                            @if($isPengurus)
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTanggapi{{ $lap->id }}">
                                <i class="fas fa-reply me-1"></i> Tindak Lanjut
                            </button>
                            @endif
                            @if($isPengurus || $lap->user_id === auth()->id())
                            <button class="btn btn-sm btn-outline-danger rounded-3 delete-confirm" data-id="lap-{{ $lap->id }}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-form-lap-{{ $lap->id }}" action="{{ route('laporan.destroy', $lap->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($isPengurus)
        <div class="modal fade" id="modalTanggapi{{ $lap->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <form action="{{ route('laporan.status', $lap->id) }}" method="POST">
                        @csrf
                        <div class="modal-header border-0 px-4 pt-4">
                            <h5 class="modal-title fw-bold">Tindak Lanjut Laporan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status Penanganan</label>
                                <select name="status" class="form-select rounded-3" required>
                                    <option value="baru" {{ $lap->status == 'baru' ? 'selected' : '' }}>Baru</option>
                                    <option value="diproses" {{ $lap->status == 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                                    <option value="selesai" {{ $lap->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                    <option value="ditolak" {{ $lap->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Tanggapan Pengurus RT</label>
                                <textarea name="tanggapan_admin" rows="3" class="form-control rounded-3" placeholder="Tuliskan keterangan tindak lanjut...">{{ $lap->tanggapan_admin }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <i class="fas fa-comments fa-3x text-muted opacity-50 mb-3"></i>
                <h6 class="fw-bold">Belum Ada Laporan Warga</h6>
                <p class="text-muted small mb-0">Jadilah yang pertama melaporkan kendala atau aspirasi di lingkungan RT.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- Modal Buat Laporan --}}
<div class="modal fade" id="modalBuatLaporan" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('laporan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-bullhorn text-primary me-2"></i>Buat Laporan / Aspirasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Laporan <span class="text-danger">*</span></label>
                        <input type="text" name="judul" class="form-control rounded-3" placeholder="Contoh: Lampu Jalan Blok C Mati" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="kategori" class="form-select rounded-3" required>
                                <option value="infrastruktur">Infrastruktur / Jalan</option>
                                <option value="kebersihan">Kebersihan / Sampah</option>
                                <option value="keamanan">Keamanan</option>
                                <option value="sosial">Sosial Warga</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lokasi Kejadian</label>
                            <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Contoh: Depan Rumah No. 12">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Detail Masalah / Aspirasi <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" rows="4" class="form-control rounded-3" placeholder="Jelaskan kondisi secara lengkap..." required></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Foto Bukti (Opsional)</label>
                        <input type="file" name="foto" class="form-control rounded-3" accept="image/*">
                        <small class="text-muted">Maks. 2MB (JPG, PNG, WEBP)</small>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
