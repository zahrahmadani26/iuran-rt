@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-house-user text-primary me-2"></i>Data Kartu Keluarga (KK)</h4>
            <p class="text-muted mb-0 small">Pendataan anggota keluarga per Kepala Keluarga / rumah di lingkungan RT</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                <i class="fas fa-house me-1"></i> {{ $wargas->total() }} KK · {{ $totalAnggota }} Anggota Terdaftar
            </span>
        </div>
    </div>

    @include('layouts.alerts')

    @if($isPengurus)
    <form method="GET" action="{{ route('kartu_keluarga.index') }}" class="mb-4">
        <div class="input-group" style="max-width: 440px;">
            <input type="text" name="q" class="form-control rounded-start-pill ps-4" placeholder="Cari No. KK, NIK, atau Nama Kepala Keluarga..." value="{{ request('q') }}">
            <button class="btn btn-primary rounded-end-pill px-4" type="submit"><i class="fas fa-search"></i></button>
            @if(request('q'))
                <a href="{{ route('kartu_keluarga.index') }}" class="btn btn-outline-secondary rounded-pill ms-2">Reset</a>
            @endif
        </div>
    </form>
    @endif

    @forelse($wargas as $warga)
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-4 d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0" style="width:48px;height:48px;background:linear-gradient(135deg,#4f46e5,#7c3aed);font-size:1.1rem;">
                    {{ strtoupper(substr($warga->nama_lengkap, 0, 1)) }}
                </div>
                <div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h6 class="fw-bold mb-0 text-dark">{{ $warga->nama_lengkap }}</h6>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">Kepala Keluarga</span>
                        <span class="badge bg-light text-dark border rounded-pill">No. KK: <code>{{ $warga->no_kk }}</code></span>
                    </div>
                    <small class="text-muted"><i class="fas fa-location-dot me-1"></i> {{ $warga->alamat }} · {{ $warga->no_telp }}</small>
                </div>
            </div>
            <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalTambahAnggota{{ $warga->id }}">
                <i class="fas fa-user-plus me-1"></i> Tambah Anggota
            </button>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Nama Lengkap</th>
                            <th>NIK</th>
                            <th>Hubungan</th>
                            <th>L/P</th>
                            <th>TTL</th>
                            <th>Status Kawin</th>
                            <th>Pekerjaan</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warga->kartuKeluargas as $idx => $anggota)
                        <tr>
                            <td class="ps-4 text-muted">{{ $idx + 1 }}</td>
                            <td class="fw-semibold">{{ $anggota->nama_lengkap }}</td>
                            <td><code>{{ $anggota->nik ?? '-' }}</code></td>
                            <td><span class="badge bg-info-subtle text-info rounded-pill">{{ ucfirst(str_replace('_', ' ', $anggota->hubungan)) }}</span></td>
                            <td>{{ $anggota->jenis_kelamin === 'laki_laki' ? 'L' : 'P' }}</td>
                            <td class="small text-muted">
                                {{ $anggota->tempat_lahir ?? '-' }}{{ $anggota->tanggal_lahir ? ', ' . $anggota->tanggal_lahir->format('d/m/Y') : '' }}
                            </td>
                            <td class="small">{{ ucfirst(str_replace('_', ' ', $anggota->status_kawin)) }}</td>
                            <td class="small text-muted">{{ $anggota->pekerjaan ?? '-' }}</td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-outline-warning rounded-3" data-bs-toggle="modal" data-bs-target="#modalEditAnggota{{ $anggota->id }}"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger rounded-3 delete-confirm" data-id="kk-{{ $anggota->id }}"><i class="fas fa-trash"></i></button>
                                <form id="delete-form-kk-{{ $anggota->id }}" action="{{ route('kartu_keluarga.destroy', $anggota->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                            </td>
                        </tr>

                        {{-- Modal Edit Anggota --}}
                        <div class="modal fade" id="modalEditAnggota{{ $anggota->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content rounded-4 border-0 shadow">
                                    <form action="{{ route('kartu_keluarga.update', $anggota->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-header border-0 px-4 pt-4">
                                            <h5 class="modal-title fw-bold">Edit Anggota Keluarga</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body px-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Nama Lengkap</label>
                                                    <input type="text" name="nama_lengkap" class="form-control rounded-3" value="{{ $anggota->nama_lengkap }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">NIK</label>
                                                    <input type="text" name="nik" class="form-control rounded-3" value="{{ $anggota->nik }}" maxlength="16">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Hubungan Keluarga</label>
                                                    <select name="hubungan" class="form-select rounded-3" required>
                                                        @foreach(['kepala_keluarga'=>'Kepala Keluarga','istri'=>'Istri','anak'=>'Anak','orang_tua'=>'Orang Tua','mertua'=>'Mertua','cucu'=>'Cucu','lainnya'=>'Lainnya'] as $k=>$lbl)
                                                            <option value="{{ $k }}" {{ $anggota->hubungan == $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Jenis Kelamin</label>
                                                    <select name="jenis_kelamin" class="form-select rounded-3" required>
                                                        <option value="laki_laki" {{ $anggota->jenis_kelamin == 'laki_laki' ? 'selected' : '' }}>Laki-Laki</option>
                                                        <option value="perempuan" {{ $anggota->jenis_kelamin == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Tempat Lahir</label>
                                                    <input type="text" name="tempat_lahir" class="form-control rounded-3" value="{{ $anggota->tempat_lahir }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                                                    <input type="date" name="tanggal_lahir" class="form-control rounded-3" value="{{ $anggota->tanggal_lahir ? $anggota->tanggal_lahir->format('Y-m-d') : '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Status Perkawinan</label>
                                                    <select name="status_kawin" class="form-select rounded-3">
                                                        @foreach(['belum_kawin'=>'Belum Kawin','kawin'=>'Kawin','cerai_hidup'=>'Cerai Hidup','cerai_mati'=>'Cerai Mati'] as $k=>$lbl)
                                                            <option value="{{ $k }}" {{ $anggota->status_kawin == $k ? 'selected' : '' }}>{{ $lbl }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold">Pekerjaan</label>
                                                    <input type="text" name="pekerjaan" class="form-control rounded-3" value="{{ $anggota->pekerjaan }}">
                                                </div>
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
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted small">
                                Belum ada anggota keluarga tambahan yang didata pada KK ini. Klik <strong>+ Tambah Anggota</strong> untuk melengkapi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Anggota --}}
    <div class="modal fade" id="modalTambahAnggota{{ $warga->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <form action="{{ route('kartu_keluarga.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="warga_id" value="{{ $warga->id }}">
                    <div class="modal-header border-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold">Tambah Anggota KK ({{ $warga->no_kk }})</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body px-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap" class="form-control rounded-3" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">NIK (Opsional)</label>
                                <input type="text" name="nik" class="form-control rounded-3" maxlength="16">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Hubungan Keluarga</label>
                                <select name="hubungan" class="form-select rounded-3" required>
                                    <option value="istri">Istri</option>
                                    <option value="anak">Anak</option>
                                    <option value="kepala_keluarga">Kepala Keluarga</option>
                                    <option value="orang_tua">Orang Tua</option>
                                    <option value="mertua">Mertua</option>
                                    <option value="cucu">Cucu</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select rounded-3" required>
                                    <option value="laki_laki">Laki-Laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control rounded-3">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status Perkawinan</label>
                                <select name="status_kawin" class="form-select rounded-3">
                                    <option value="belum_kawin">Belum Kawin</option>
                                    <option value="kawin">Kawin</option>
                                    <option value="cerai_hidup">Cerai Hidup</option>
                                    <option value="cerai_mati">Cerai Mati</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control rounded-3" placeholder="Misal: Pelajar / Karyawan">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Anggota</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
        <i class="fas fa-house-user fa-3x opacity-50 mb-3"></i>
        <p class="mb-0">Belum ada data Kepala Keluarga / Warga yang terdaftar untuk akun ini.</p>
    </div>
    @endforelse

    @if($wargas->hasPages())
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <small class="text-muted">Menampilkan {{ $wargas->firstItem() }}–{{ $wargas->lastItem() }} dari {{ $wargas->total() }} KK</small>
        {{ $wargas->links() }}
    </div>
    @endif
</div>
@endsection
