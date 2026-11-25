@extends('layouts.app')

@section('content')
<style>
    @keyframes pulse-sos {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        70% { box-shadow: 0 0 0 24px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    .sos-main-btn {
        width: 170px;
        height: 170px;
        border-radius: 50%;
        background: linear-gradient(145deg, #ef4444, #b91c1c);
        border: 8px solid #fee2e2;
        color: white;
        font-weight: 800;
        font-size: 1.5rem;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        animation: pulse-sos 2s infinite;
        transition: transform 0.2s;
    }
    .sos-main-btn:hover { transform: scale(1.06); color: white; }
    .sos-type-btn {
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 12px;
        background: white;
        transition: all 0.2s;
        text-align: center;
        cursor: pointer;
    }
    .sos-type-btn:hover {
        border-color: #ef4444;
        background: #fef2f2;
        transform: translateY(-2px);
    }
</style>

<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-danger"><i class="fas fa-bell-on fas fa-siren-on fa-triangle-exclamation me-2"></i>Panic Button / SOS Darurat</h4>
            <p class="text-muted mb-0 small">Kirim sinyal bahaya seketika ke Pengurus RT & Keamanan Lingkungan</p>
        </div>
        <a href="{{ route('kontak_darurat.index') }}" class="btn btn-outline-danger rounded-pill px-4">
            <i class="fas fa-phone me-1"></i> Semua Kontak Darurat
        </a>
    </div>

    @include('layouts.alerts')

    {{-- Sinyal Darurat Aktif --}}
    @if($activeAlerts->count() > 0)
    <div class="card border-0 shadow-sm rounded-4 mb-4 border-start border-danger border-5" style="background: #fef2f2;">
        <div class="card-body p-4">
            <h5 class="fw-bold text-danger mb-3">
                <i class="fas fa-circle-radiation fa-spin me-2"></i>
                DARURAT AKTIF ({{ $activeAlerts->count() }})
            </h5>
            <div class="row g-3">
                @foreach($activeAlerts as $alert)
                <div class="col-md-6">
                    <div class="bg-white rounded-4 p-3 border border-danger border-opacity-25 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-danger rounded-pill px-3 py-1 text-uppercase">{{ $alert->jenis }}</span>
                            <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                        </div>
                        <h6 class="fw-bold mb-1">{{ $alert->warga->nama_lengkap ?? $alert->user->name ?? 'Warga' }}</h6>
                        <div class="small text-danger fw-semibold mb-1"><i class="fas fa-location-dot me-1"></i> {{ $alert->lokasi }}</div>
                        @if($alert->keterangan)
                            <p class="small text-muted mb-2">{{ $alert->keterangan }}</p>
                        @endif
                        @if($isPengurus)
                        <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <form action="{{ route('panic.tangani', $alert->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 fw-semibold">
                                    <i class="fas fa-person-running me-1"></i> Sedang Ditangani
                                </button>
                            </form>
                            <form action="{{ route('panic.selesai', $alert->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                                    <i class="fas fa-check me-1"></i> Selesai / Aman
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4 mb-4">
        {{-- Panel Tombol SOS --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100">
                <h5 class="fw-bold mb-2">Butuh Pertolongan Segera?</h5>
                <p class="text-muted small mb-4">Tekan tombol darurat di bawah atau pilih jenis kejadian untuk memanggil bantuan</p>

                <div class="mb-4">
                    <button type="button" class="sos-main-btn" onclick="openSosModal('lainnya')">
                        <i class="fas fa-triangle-exclamation fs-1 mb-1"></i>
                        <span>SOS</span>
                        <span style="font-size:.7rem;font-weight:500;">TEKAN DARURAT</span>
                    </button>
                </div>

                <div class="row g-2">
                    <div class="col-6 col-sm-3">
                        <div class="sos-type-btn" onclick="openSosModal('kebakaran')">
                            <i class="fas fa-fire fs-3 text-danger mb-2"></i>
                            <div class="fw-bold small">Kebakaran</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="sos-type-btn" onclick="openSosModal('kemalingan')">
                            <i class="fas fa-user-secret fs-3 text-dark mb-2"></i>
                            <div class="fw-bold small">Kemalingan</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="sos-type-btn" onclick="openSosModal('medis')">
                            <i class="fas fa-kit-medical fs-3 text-success mb-2"></i>
                            <div class="fw-bold small">Medis</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="sos-type-btn" onclick="openSosModal('kecelakaan')">
                            <i class="fas fa-car-burst fs-3 text-warning mb-2"></i>
                            <div class="fw-bold small">Kecelakaan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kontak Darurat Cepat --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h6 class="fw-bold mb-3"><i class="fas fa-phone-volume text-danger me-2"></i>Panggilan Cepat</h6>
                <div class="d-flex flex-column gap-2">
                    @foreach($kontakDarurat as $kd)
                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light">
                        <div>
                            <div class="fw-bold small">{{ $kd->nama }}</div>
                            <div class="font-monospace text-danger fw-bold">{{ $kd->nomor_telepon }}</div>
                        </div>
                        <a href="tel:{{ preg_replace('/[^0-9\+]/', '', $kd->nomor_telepon) }}" class="btn btn-sm btn-danger rounded-pill px-3">
                            <i class="fas fa-phone me-1"></i> Telpon
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Sinyal SOS --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-4">
            <h6 class="fw-bold mb-0"><i class="fas fa-history text-muted me-2"></i>Riwayat Panggilan Darurat</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Waktu</th>
                        <th>Pelapor</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        @if($isPengurus)<th class="text-center pe-4">Aksi</th>@endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayats as $r)
                    <tr>
                        <td class="ps-4 small text-muted">{{ $r->created_at->format('d M Y, H:i') }}</td>
                        <td class="fw-semibold">{{ $r->warga->nama_lengkap ?? $r->user->name ?? '-' }}</td>
                        <td><span class="badge bg-danger-subtle text-danger rounded-pill text-uppercase">{{ $r->jenis }}</span></td>
                        <td class="small">{{ $r->lokasi }}</td>
                        <td>
                            @if($r->status === 'aktif')
                                <span class="badge bg-danger rounded-pill">Aktif</span>
                            @elseif($r->status === 'ditangani')
                                <span class="badge bg-warning text-dark rounded-pill">Ditangani</span>
                            @else
                                <span class="badge bg-success rounded-pill">Selesai</span>
                            @endif
                        </td>
                        @if($isPengurus)
                        <td class="text-center pe-4">
                            @if($r->status !== 'selesai')
                            <form action="{{ route('panic.selesai', $r->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3">Selesai</button>
                            </form>
                            @else
                            <small class="text-muted">{{ $r->penangan->name ?? 'Pengurus' }}</small>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isPengurus ? 6 : 5 }}" class="text-center py-4 text-muted small">Belum ada riwayat sinyal SOS. Lingkungan aman!</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi SOS --}}
<div class="modal fade" id="modalSos" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('panic.trigger') }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white border-0 rounded-top-4 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-triangle-exclamation me-2"></i>Kirim Sinyal Darurat SOS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Keadaan Darurat <span class="text-danger">*</span></label>
                        <select name="jenis" id="sosJenisSelect" class="form-select rounded-3" required>
                            <option value="kebakaran">🔥 Kebakaran</option>
                            <option value="kemalingan">🚨 Kemalingan / Gangguan Keamanan</option>
                            <option value="medis">🏥 Darurat Medis</option>
                            <option value="kecelakaan">🚑 Kecelakaan</option>
                            <option value="lainnya">⚠️ Darurat Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Lokasi Kejadian</label>
                        <input type="text" name="lokasi" class="form-control rounded-3" value="{{ $warga->alamat ?? '' }}" placeholder="Alamat / Blok rumah...">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Keterangan Singkat (Opsional)</label>
                        <textarea name="keterangan" rows="2" class="form-control rounded-3" placeholder="Jelaskan situasi secara singkat..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        <i class="fas fa-paper-plane me-1"></i> KIRIM SOS SEKARANG
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openSosModal(jenis) {
        document.getElementById('sosJenisSelect').value = jenis;
        new bootstrap.Modal(document.getElementById('modalSos')).show();
    }
</script>
@endpush
