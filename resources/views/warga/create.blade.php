@extends('layouts.app')

@section('title', 'Tambah Warga')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-user-plus text-primary me-2"></i>Tambah Warga Baru</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('warga.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nik"
                                       class="form-control rounded-3 @error('nik') is-invalid @enderror"
                                       placeholder="16 digit NIK" value="{{ old('nik') }}" required>
                                @error('nik')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No KK <span class="text-danger">*</span></label>
                                <input type="text" name="no_kk"
                                       class="form-control rounded-3 @error('no_kk') is-invalid @enderror"
                                       placeholder="16 digit No KK" value="{{ old('no_kk') }}" required>
                                @error('no_kk')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap"
                                       class="form-control rounded-3 @error('nama_lengkap') is-invalid @enderror"
                                       placeholder="Nama sesuai KTP" value="{{ old('nama_lengkap') }}" required>
                                @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No Telp <span class="text-danger">*</span></label>
                                <input type="text" name="no_telp"
                                       class="form-control rounded-3 @error('no_telp') is-invalid @enderror"
                                       placeholder="08xx-xxxx-xxxx" value="{{ old('no_telp') }}" required>
                                @error('no_telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- RFID Card UID Input --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-id-card text-primary me-1"></i> UID Kartu RFID (ESP32)
                                    <span class="text-muted fw-normal small">(Opsional, untuk tap iuran otomatis)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-wifi text-muted"></i></span>
                                    <input type="text" name="rfid_uid" id="rfid_uid_input"
                                           class="form-control font-monospace @error('rfid_uid') is-invalid @enderror"
                                           placeholder="Contoh: A1B2C3D4" value="{{ old('rfid_uid') }}">
                                    <button type="button" class="btn btn-outline-primary" id="btn-fetch-rfid" onclick="fetchLastRfid()">
                                        <i class="fas fa-sync-alt me-1"></i> Ambil dari Tap Scanner
                                    </button>
                                </div>
                                <small id="rfid-status-text" class="text-muted mt-1 d-block">
                                    Tempelkan kartu ke modul RC522 pada ESP32, lalu klik tombol <b>Ambil dari Tap Scanner</b>.
                                </small>
                                @error('rfid_uid')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" rows="3"
                                          class="form-control rounded-3 @error('alamat') is-invalid @enderror"
                                          placeholder="Alamat lengkap" required>{{ old('alamat') }}</textarea>
                                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                       class="form-control rounded-3 @error('email') is-invalid @enderror"
                                       placeholder="email@example.com" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password"
                                       class="form-control rounded-3 @error('password') is-invalid @enderror"
                                       placeholder="Min. 8 karakter" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="aktif" {{ old('status','aktif')=='aktif'?'selected':'' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status')=='nonaktif'?'selected':'' }}>Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="fas fa-save me-2"></i> Simpan
                            </button>
                            <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary px-4 rounded-pill">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function fetchLastRfid() {
    const btn = document.getElementById('btn-fetch-rfid');
    const input = document.getElementById('rfid_uid_input');
    const statusText = document.getElementById('rfid-status-text');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengambil...';

    fetch('/api/rfid/last-scan')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.rfid_uid) {
                input.value = data.rfid_uid;
                if (data.is_registered) {
                    statusText.innerHTML = `<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Peringatan: Kartu UID <b>${data.rfid_uid}</b> sudah dipakai oleh ${data.warga_nama}!</span>`;
                } else {
                    statusText.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Berhasil! Kartu terdeteksi: <b>${data.rfid_uid}</b> (${data.time})</span>`;
                }
            } else {
                statusText.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> Belum ada kartu yang discan di ESP32 dalam 10 menit terakhir.</span>`;
            }
        })
        .catch(err => {
            statusText.innerHTML = `<span class="text-danger"><i class="fas fa-times-circle"></i> Gagal menghubungi scanner API.</span>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-sync-alt me-1"></i> Ambil dari Tap Scanner';
        });
}
</script>
@endpush
@endsection
