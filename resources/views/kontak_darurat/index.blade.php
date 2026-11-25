@extends('layouts.app')

@section('content')
@php $isPengurus = in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']); @endphp

<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-phone-volume text-danger me-2"></i>Kontak Darurat</h4>
            <p class="text-muted mb-0 small">Nomor telepon penting pengurus RT, keamanan, layanan medis, damkar, dan utilitas</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('panic.index') }}" class="btn btn-danger rounded-pill px-3">
                <i class="fas fa-triangle-exclamation me-1"></i> Tombol SOS
            </a>
            @if($isPengurus)
            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambahKontak">
                <i class="fas fa-plus me-2"></i>Tambah Kontak
            </button>
            @endif
        </div>
    </div>

    @include('layouts.alerts')

    <div class="row g-4">
        @forelse($kontaks as $kontak)
        <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-4 d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width:52px;height:52px;background:linear-gradient(135deg,#ef4444,#b91c1c);font-size:1.3rem;">
                                <i class="{{ $kontak->icon ?? 'fas fa-phone' }}"></i>
                            </div>
                            <div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2 py-1 small">{{ $kontak->kategori }}</span>
                                <h6 class="fw-bold text-dark mt-1 mb-0">{{ $kontak->nama }}</h6>
                            </div>
                        </div>

                        @if($isPengurus)
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-link text-warning p-1" data-bs-toggle="modal" data-bs-target="#modalEditKontak{{ $kontak->id }}"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-link text-danger p-1 delete-confirm" data-id="kontak-{{ $kontak->id }}"><i class="fas fa-trash"></i></button>
                            <form id="delete-form-kontak-{{ $kontak->id }}" action="{{ route('kontak_darurat.destroy', $kontak->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </div>
                        @endif
                    </div>

                    <div class="p-3 rounded-3 bg-light mb-3 text-center">
                        <div class="fs-3 fw-bold text-dark font-monospace">{{ $kontak->nomor_telepon }}</div>
                    </div>

                    @if($kontak->keterangan)
                    <p class="text-muted small mb-3">{{ $kontak->keterangan }}</p>
                    @endif

                    <div class="mt-auto d-flex gap-2">
                        <a href="tel:{{ preg_replace('/[^0-9\+]/', '', $kontak->nomor_telepon) }}" class="btn btn-danger rounded-pill flex-grow-1 fw-semibold">
                            <i class="fas fa-phone me-1"></i> Panggil
                        </a>
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" onclick="copyPhone('{{ $kontak->nomor_telepon }}')" title="Salin Nomor">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if($isPengurus)
        <div class="modal fade" id="modalEditKontak{{ $kontak->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow">
                    <form action="{{ route('kontak_darurat.update', $kontak->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-header border-0 px-4 pt-4">
                            <h5 class="modal-title fw-bold">Edit Kontak Darurat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Instansi / Petugas</label>
                                <input type="text" name="nama" class="form-control rounded-3" value="{{ $kontak->nama }}" required>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nomor Telepon</label>
                                    <input type="text" name="nomor_telepon" class="form-control rounded-3" value="{{ $kontak->nomor_telepon }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Kategori</label>
                                    <input type="text" name="kategori" class="form-control rounded-3" value="{{ $kontak->kategori }}" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Ikon FontAwesome</label>
                                    <input type="text" name="icon" class="form-control rounded-3" value="{{ $kontak->icon }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Urutan</label>
                                    <input type="number" name="urutan" class="form-control rounded-3" value="{{ $kontak->urutan }}">
                                </div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control rounded-3" value="{{ $kontak->keterangan }}">
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
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
                <i class="fas fa-phone-slash fa-3x opacity-50 mb-3"></i>
                <p class="mb-0">Belum ada data kontak darurat.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@if($isPengurus)
<div class="modal fade" id="modalTambahKontak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('kontak_darurat.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-phone-plus text-danger me-2"></i>Tambah Kontak Darurat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Instansi / Petugas <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Contoh: Puskesmas Kecamatan" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_telepon" class="form-control rounded-3" placeholder="021-xxxxxxx" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="kategori" class="form-control rounded-3" placeholder="Medis / Keamanan / PLN" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Ikon (Opsional)</label>
                            <select name="icon" class="form-select rounded-3">
                                <option value="fas fa-phone">Telepon (Default)</option>
                                <option value="fas fa-shield-halved">Keamanan / Satpam</option>
                                <option value="fas fa-truck-medical">Medis / Ambulans</option>
                                <option value="fas fa-fire-extinguisher">Pemadam Kebakaran</option>
                                <option value="fas fa-building-shield">Polisi</option>
                                <option value="fas fa-bolt">Listrik / PLN</option>
                                <option value="fas fa-user-tie">Pengurus RT</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Urutan</label>
                            <input type="number" name="urutan" class="form-control rounded-3" value="10">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control rounded-3" placeholder="Layanan 24 jam...">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Kontak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function copyPhone(num) {
        navigator.clipboard.writeText(num).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Nomor ' + num + ' disalin!',
                showConfirmButton: false,
                timer: 2000
            });
        });
    }
</script>
@endpush
