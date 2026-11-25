@extends('layouts.app')

@section('title', 'Edit Warga')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="{{ route('warga.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <h4 class="fw-bold mb-0"><i class="fas fa-user-edit text-primary me-2"></i>Edit Warga</h4>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('warga.update', $warga->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-muted">NIK</label>
                                <input type="text" class="form-control rounded-3 bg-light"
                                       value="{{ $warga->nik }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-muted">No KK</label>
                                <input type="text" class="form-control rounded-3 bg-light"
                                       value="{{ $warga->no_kk }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lengkap"
                                       class="form-control rounded-3 @error('nama_lengkap') is-invalid @enderror"
                                       value="{{ old('nama_lengkap', $warga->nama_lengkap) }}" required>
                                @error('nama_lengkap')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">No Telp <span class="text-danger">*</span></label>
                                <input type="text" name="no_telp"
                                       class="form-control rounded-3 @error('no_telp') is-invalid @enderror"
                                       value="{{ old('no_telp', $warga->no_telp) }}" required>
                                @error('no_telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            {{-- RFID Card UID Input --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-id-card text-primary me-1"></i> UID Kartu RFID / NFC
                                    <span class="text-muted fw-normal small">(Untuk tap iuran via ESP32 / HP Android)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-wifi text-muted"></i></span>
                                    <input type="text" name="rfid_uid" id="rfid_uid_input"
                                           class="form-control font-monospace @error('rfid_uid') is-invalid @enderror"
                                           placeholder="Contoh: A1B2C3D4" value="{{ old('rfid_uid', $warga->rfid_uid) }}">
                                    <button type="button" class="btn btn-outline-success" id="btn-scan-nfc" onclick="scanNfcWarga()">
                                        <i class="fas fa-mobile-screen-button me-1"></i> Tap via NFC HP
                                    </button>
                                    <button type="button" class="btn btn-outline-primary" id="btn-fetch-rfid" onclick="fetchLastRfid()">
                                        <i class="fas fa-sync-alt me-1"></i> Ambil dari ESP32
                                    </button>
                                </div>
                                <small id="rfid-status-text" class="text-muted mt-1 d-block">
                                    Bisa ditempelkan di <b>belakang HP Android (NFC)</b> atau ke alat <b>ESP32 RC522</b>.
                                </small>
                                @error('rfid_uid')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label fw-semibold">Alamat <span class="text-danger">*</span></label>
                                <textarea name="alamat" rows="3"
                                          class="form-control rounded-3 @error('alamat') is-invalid @enderror"
                                          required>{{ old('alamat', $warga->alamat) }}</textarea>
                                @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="aktif" {{ $warga->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ $warga->status == 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                                </select>
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4 rounded-pill">
                                <i class="fas fa-save me-2"></i> Update
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
async function scanNfcWarga() {
    const btn = document.getElementById('btn-scan-nfc');
    const input = document.getElementById('rfid_uid_input');
    const statusText = document.getElementById('rfid-status-text');

    if (!('NDEFReader' in window)) {
        alert('Fitur Web NFC tidak didukung di browser ini.\n\nSilakan gunakan Google Chrome di HP Android yang memiliki sensor NFC.');
        return;
    }

    try {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Tempelkan Kartu ke HP...';
        statusText.innerHTML = '<span class="text-primary fw-bold"><i class="fas fa-spinner fa-spin me-1"></i> Mendengarkan sensor NFC HP... Silakan tempelkan kartu ke bagian belakang HP Anda sekarang!</span>';

        const ndef = new NDEFReader();
        await ndef.scan();

        ndef.onreading = (event) => {
            let serial = event.serialNumber || '';
            let cleanUid = serial.replace(/[^A-Za-z0-9]/g, '').toUpperCase();
            if (!cleanUid) {
                cleanUid = 'NFC-' + Math.random().toString(36).substring(2, 10).toUpperCase();
            }

            if (navigator.vibrate) navigator.vibrate([100, 50, 100]);

            input.value = cleanUid;
            statusText.innerHTML = `<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Berhasil! UID kartu terbaca via NFC HP: <b>${cleanUid}</b></span>`;
            
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-mobile-screen-button me-1"></i> Tap via NFC HP';
        };

        ndef.onreadingerror = () => {
            statusText.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> Kartu terdeteksi tapi gagal dibaca. Coba tempelkan lagi.</span>';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-mobile-screen-button me-1"></i> Tap via NFC HP';
        };

    } catch (err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-mobile-screen-button me-1"></i> Tap via NFC HP';
        alert('Gagal mengaktifkan NFC: ' + err.message + '\nPastikan fitur NFC di Pengaturan HP Anda sudah aktif.');
    }
}

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
                if (data.is_registered && data.rfid_uid !== '{{ $warga->rfid_uid }}') {
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
