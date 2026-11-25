@extends('layouts.app')

@section('content')
@php $isPengurus = in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']); @endphp

<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-file-signature text-primary me-2"></i>Surat Pengantar Digital</h4>
            <p class="text-muted mb-0 small">Ajukan surat pengantar RT secara online dan unduh dokumen PDF resmi</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAjukanSurat">
            <i class="fas fa-plus me-2"></i>Ajukan Surat Pengantar
        </button>
    </div>

    @include('layouts.alerts')

    @if($isPengurus)
    {{-- Filter Status untuk Pengurus --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('surat.index') }}" class="btn btn-sm rounded-pill px-3 {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">Semua</a>
        <a href="{{ route('surat.index', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == 'pending' ? 'btn-warning text-dark' : 'btn-outline-secondary' }}">Menunggu</a>
        <a href="{{ route('surat.index', ['status' => 'diproses']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == 'diproses' ? 'btn-info text-white' : 'btn-outline-secondary' }}">Diproses</a>
        <a href="{{ route('surat.index', ['status' => 'disetujui']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == 'disetujui' ? 'btn-success' : 'btn-outline-secondary' }}">Disetujui</a>
        <a href="{{ route('surat.index', ['status' => 'ditolak']) }}" class="btn btn-sm rounded-pill px-3 {{ request('status') == 'ditolak' ? 'btn-danger' : 'btn-outline-secondary' }}">Ditolak</a>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>No. Surat</th>
                            @if($isPengurus)<th>Pemohon</th>@endif
                            <th>Jenis Surat</th>
                            <th>Keperluan</th>
                            <th>Status</th>
                            <th>Tgl Pengajuan</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($surats as $i => $surat)
                        @php
                            $statusMap = [
                                'pending'   => ['bg' => 'bg-warning-subtle text-warning', 'label' => 'Menunggu'],
                                'diproses'  => ['bg' => 'bg-info-subtle text-info', 'label' => 'Diproses'],
                                'disetujui' => ['bg' => 'bg-success-subtle text-success', 'label' => 'Disetujui'],
                                'ditolak'   => ['bg' => 'bg-danger-subtle text-danger', 'label' => 'Ditolak'],
                            ];
                            $st = $statusMap[$surat->status] ?? $statusMap['pending'];
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted">{{ $i + 1 }}</td>
                            <td>
                                @if($surat->no_surat)
                                    <code class="text-primary fw-bold">{{ $surat->no_surat }}</code>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </td>
                            @if($isPengurus)
                            <td>
                                <div class="fw-semibold">{{ $surat->warga->nama_lengkap ?? $surat->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $surat->warga->nik ?? 'Admin/User' }}</small>
                            </td>
                            @endif
                            <td>
                                <span class="fw-semibold text-dark">{{ $surat->jenis_label }}</span>
                            </td>
                            <td>
                                <div style="max-width: 220px;">{{ $surat->keperluan }}</div>
                                @if($surat->catatan_admin)
                                    <small class="text-muted d-block"><i class="fas fa-comment-dots me-1"></i> {{ $surat->catatan_admin }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $st['bg'] }} rounded-pill px-3 py-1">{{ $st['label'] }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $surat->created_at->format('d M Y') }}</small>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    @if($surat->status === 'disetujui')
                                        <a href="{{ route('surat.pdf', $surat->id) }}" class="btn btn-sm btn-success rounded-pill px-3" title="Unduh PDF">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                    @endif

                                    @if($isPengurus && in_array($surat->status, ['pending', 'diproses']))
                                        @if($surat->status === 'pending')
                                        <form action="{{ route('surat.process', $surat->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info rounded-3" title="Tandai Diproses">
                                                <i class="fas fa-spinner"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <button class="btn btn-sm btn-primary rounded-3" data-bs-toggle="modal" data-bs-target="#modalApproveSurat{{ $surat->id }}" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger rounded-3" data-bs-toggle="modal" data-bs-target="#modalRejectSurat{{ $surat->id }}" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($isPengurus || $surat->status === 'pending')
                                        <button class="btn btn-sm btn-outline-secondary rounded-3 delete-confirm" data-id="surat-{{ $surat->id }}" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-surat-{{ $surat->id }}" action="{{ route('surat.destroy', $surat->id) }}" method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        @if($isPengurus && in_array($surat->status, ['pending', 'diproses']))
                        {{-- Modal Approve --}}
                        <div class="modal fade" id="modalApproveSurat{{ $surat->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('surat.approve', $surat->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 px-4 pt-4">
                                            <h5 class="modal-title fw-bold text-success"><i class="fas fa-circle-check me-2"></i>Setujui Surat Pengantar</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4">
                                            <p class="small text-muted">Surat <strong>{{ $surat->jenis_label }}</strong> untuk <strong>{{ $surat->warga->nama_lengkap ?? $surat->user->name }}</strong> akan diterbitkan dengan nomor surat otomatis dan siap diunduh PDF.</p>
                                            <div class="mb-2">
                                                <label class="form-label fw-semibold">Catatan Pengurus (Opsional)</label>
                                                <input type="text" name="catatan_admin" class="form-control rounded-3" placeholder="Misal: Berlaku 30 hari sejak tanggal terbit">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-4">Setujui & Terbitkan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Reject --}}
                        <div class="modal fade" id="modalRejectSurat{{ $surat->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('surat.reject', $surat->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header border-0 px-4 pt-4">
                                            <h5 class="modal-title fw-bold text-danger"><i class="fas fa-circle-xmark me-2"></i>Tolak Pengajuan Surat</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4">
                                            <div class="mb-2">
                                                <label class="form-label fw-semibold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="catatan_admin" rows="3" class="form-control rounded-3" placeholder="Jelaskan alasan penolakan atau data yang perlu dilengkapi..." required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 px-4 pb-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger rounded-pill px-4">Tolak Surat</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="{{ $isPengurus ? 8 : 7 }}" class="text-center py-5 text-muted">
                                <i class="fas fa-file-signature fa-3x opacity-50 mb-3 d-block"></i>
                                Belum ada pengajuan surat pengantar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ajukan Surat --}}
<div class="modal fade" id="modalAjukanSurat" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('surat.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-circle-plus text-primary me-2"></i>Ajukan Surat Pengantar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    @if($isPengurus && $wargas->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Atas Nama Warga (Opsional)</label>
                        <select name="warga_id" class="form-select rounded-3">
                            <option value="">-- Diri Sendiri ({{ auth()->user()->name }}) --</option>
                            @foreach($wargas as $w)
                                <option value="{{ $w->id }}">{{ $w->nama_lengkap }} ({{ $w->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Surat <span class="text-danger">*</span></label>
                        <select name="jenis_surat" class="form-select rounded-3" required>
                            @foreach($jenisList as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keperluan <span class="text-danger">*</span></label>
                        <input type="text" name="keperluan" class="form-control rounded-3" placeholder="Contoh: Perpanjangan KTP / Melamar Pekerjaan" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="3" class="form-control rounded-3" placeholder="Informasi tambahan bila ada..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-paper-plane me-1"></i> Kirim Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
