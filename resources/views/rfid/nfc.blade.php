@extends('layouts.app')

@section('title', 'Tap NFC Android - Iuran RT')

@push('styles')
<style>
    /* NFC Pulsing Radar Animation */
    .nfc-terminal-card {
        background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 100%);
        border: 2px solid #e2e8f0;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
    }

    .nfc-radar-wrapper {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nfc-icon-circle {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        z-index: 2;
        box-shadow: 0 10px 25px rgba(79, 70, 229, 0.4);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .nfc-radar-pulse {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(79, 70, 229, 0.15);
        opacity: 0;
        pointer-events: none;
    }

    .nfc-scanning .nfc-icon-circle {
        transform: scale(1.08);
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
    }

    .nfc-scanning .nfc-radar-pulse {
        animation: nfcPulse 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }

    .nfc-scanning .pulse-2 {
        animation-delay: 0.6s;
    }

    .nfc-scanning .pulse-3 {
        animation-delay: 1.2s;
    }

    @keyframes nfcPulse {
        0% {
            transform: scale(0.6);
            opacity: 0.8;
        }
        50% {
            opacity: 0.4;
        }
        100% {
            transform: scale(1.6);
            opacity: 0;
        }
    }

    .mode-tab-btn {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px 16px;
        transition: all 0.2s;
    }

    .mode-tab-btn.active {
        background-color: #4f46e5 !important;
        color: white !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
    }

    .device-guide-step {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #4f46e5;
        color: white;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-mobile-screen-button text-primary me-2"></i>Terminal Tap NFC (HP Android)</h4>
            <p class="text-muted mb-0 small">Gunakan sensor NFC bawaan HP Android Anda untuk tap kartu RFID / e-KTP / e-Money secara langsung</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('rfid.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-microchip me-1"></i> Perangkat ESP32
            </a>
            <a href="{{ route('pembayaran.index') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="fas fa-receipt me-1"></i> Data Pembayaran
            </a>
        </div>
    </div>

    @include('layouts.alerts')

    {{-- NFC Support Alert Banner --}}
    <div id="nfc-support-alert" class="alert border-0 rounded-4 shadow-sm mb-4 d-none">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div id="nfc-support-icon" class="rounded-circle p-2 px-3 bg-white shadow-sm"></div>
                <div>
                    <h6 class="fw-bold mb-0" id="nfc-support-title">Mengecek Dukungan Web NFC...</h6>
                    <small id="nfc-support-desc" class="text-muted"></small>
                </div>
            </div>
            <span id="nfc-support-badge" class="badge rounded-pill px-3 py-2"></span>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Main NFC Terminal Card --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-4 nfc-terminal-card">
                
                {{-- Mode Switcher --}}
                <div class="d-flex bg-light p-1 rounded-3 mb-4">
                    <button type="button" class="btn flex-fill mode-tab-btn active" id="tab-mode-pay" onclick="setNfcMode('pay')">
                        <i class="fas fa-credit-card me-1"></i> Mode Bayar Iuran
                    </button>
                    <button type="button" class="btn flex-fill mode-tab-btn text-muted" id="tab-mode-check" onclick="setNfcMode('check')">
                        <i class="fas fa-id-badge me-1"></i> Mode Cek Kartu
                    </button>
                </div>

                {{-- Visual NFC Radar & Tap Target --}}
                <div class="text-center py-3">
                    <div class="nfc-radar-wrapper mb-3" id="nfc-radar-box">
                        <div class="nfc-radar-pulse pulse-1"></div>
                        <div class="nfc-radar-pulse pulse-2"></div>
                        <div class="nfc-radar-pulse pulse-3"></div>
                        <div class="nfc-icon-circle">
                            <i class="fas fa-wifi fa-rotate-90"></i>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-1" id="nfc-status-heading">Sensor NFC Siap Dinyalakan</h5>
                    <p class="text-muted small mb-3" id="nfc-status-subtext">
                        Klik tombol di bawah ini, lalu tempelkan kartu ke bagian belakang HP Android Anda.
                    </p>

                    {{-- Main Action Button --}}
                    <div class="d-flex justify-content-center gap-2 mb-3">
                        <button type="button" class="btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow" id="btn-start-nfc" onclick="toggleNfcScanner()">
                            <i class="fas fa-play me-2"></i> Aktifkan Sensor NFC HP
                        </button>
                    </div>

                    <div id="nfc-listening-indicator" class="d-none">
                        <div class="spinner-grow spinner-grow-sm text-success me-1" role="status"></div>
                        <span class="text-success fw-bold small">Mendengarkan tap kartu di belakang HP...</span>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Configuration Option for Pay Mode --}}
                <div id="section-pay-options">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted text-uppercase">Pilih Jenis Iuran yang Akan Ditap</label>
                        <select id="nfc_iuran_select" class="form-select form-select-lg rounded-3">
                            @foreach($iurans as $i)
                                <option value="{{ $i->id }}">
                                    {{ $i->nama_iuran }} - Rp {{ number_format($i->nominal, 0, ',', '.') }} ({{ ucfirst($i->periode) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Fallback / Manual UID Input (Jika HP tidak ada NFC atau saat testing di Laptop) --}}
                <div class="mt-2 pt-2 border-top">
                    <a class="text-decoration-none small text-muted fw-semibold" data-bs-toggle="collapse" href="#collapseManualUid" role="button">
                        <i class="fas fa-keyboard me-1"></i> Input / Simulasi Manual (Untuk Laptop / HP tanpa NFC)
                    </a>
                    <div class="collapse mt-3" id="collapseManualUid">
                        <div class="p-3 bg-white rounded-3 border">
                            <label class="form-label small fw-semibold">UID Kartu RFID / NFC (Hex):</label>
                            <div class="input-group mb-2">
                                <input type="text" id="manual_uid_input" class="form-control font-monospace" placeholder="Contoh: A1B2C3D4">
                                <button type="button" class="btn btn-outline-secondary" onclick="generateRandomNfcUid()">
                                    <i class="fas fa-dice"></i> Random
                                </button>
                                <button type="button" class="btn btn-dark" onclick="submitManualTap()">
                                    <i class="fas fa-paper-plane me-1"></i> Proses Tap
                                </button>
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                Pilih warga dari dropdown untuk otomatis mengisi UID terdaftar:
                            </small>
                            <select class="form-select form-select-sm mt-1" onchange="document.getElementById('manual_uid_input').value = this.value">
                                <option value="">-- Pilih Warga Terdaftar --</option>
                                @foreach($wargas->where('rfid_uid', '!=', null) as $w)
                                    <option value="{{ $w->rfid_uid }}">{{ $w->nama_lengkap }} ({{ $w->rfid_uid }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Live Result Box --}}
                <div id="nfc_result_box" class="mt-4 d-none">
                    <div class="p-4 rounded-4" id="nfc_alert_box">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <i id="nfc_result_icon" class="fas fa-check-circle fa-2x"></i>
                            <div>
                                <h6 class="fw-bold mb-0" id="nfc_result_title">Status Transaksi</h6>
                                <small id="nfc_result_sub" class="text-muted"></small>
                            </div>
                        </div>
                        <div id="nfc_result_details" class="mt-3"></div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Side Info: Cara Kerja & Panduan Android --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1"><i class="fab fa-android text-success me-2"></i>Panduan Tap NFC di HP Android</h5>
                    <p class="text-muted small mb-0">Cara menggunakan fitur NFC tanpa perlu install aplikasi apapun:</p>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex gap-3 mb-3">
                        <div class="device-guide-step">1</div>
                        <div>
                            <h6 class="fw-bold mb-1">Nyalakan Fitur NFC di HP</h6>
                            <p class="text-muted small mb-0">Buka menu pengaturan HP Android Anda ➔ cari <b>NFC</b> ➔ Geser ke posisi <b>ON / Aktif</b>.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-3">
                        <div class="device-guide-step">2</div>
                        <div>
                            <h6 class="fw-bold mb-1">Buka di Browser Google Chrome</h6>
                            <p class="text-muted small mb-0">Web NFC adalah teknologi standar resmi W3C yang didukung langsung di <b>Google Chrome</b> atau <b>Edge</b> di Android.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-3">
                        <div class="device-guide-step">3</div>
                        <div>
                            <h6 class="fw-bold mb-1">Klik Tombol "Aktifkan Sensor"</h6>
                            <p class="text-muted small mb-0">Jika browser meminta izin akses NFC, klik tombol <b>Izinkan (Allow)</b>.</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="device-guide-step">4</div>
                        <div>
                            <h6 class="fw-bold mb-1">Tempelkan Kartu ke Belakang HP</h6>
                            <p class="text-muted small mb-0">Tempelkan kartu RFID 13.56 MHz, e-KTP, kartu Flazz, e-Money, atau JakCard ke bagian punggung/kamera HP. HP akan bergetar dan otomatis memverifikasi iuran!</p>
                        </div>
                    </div>

                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-3 mt-4 mb-0 small">
                        <i class="fas fa-lightbulb text-primary me-1"></i>
                        <b>Jenis Kartu yang Didukung:</b> Kartu RFID frekuensi 13.56 MHz (Mifare Classic, Mifare Ultralight, NTAG213/215, e-KTP, dan e-Money / Flazz bank).
                    </div>
                </div>
            </div>

            {{-- Stat Kartu Terakhir Ditap --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-clock-rotate-left text-primary me-2"></i>Kartu Terakhir Terdeteksi</h6>
                        <span class="badge bg-light text-dark font-monospace" id="nfc-last-uid">{{ $lastScan['uid'] ?? 'Belum ada' }}</span>
                    </div>
                    <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">Waktu Scan Terakhir</small>
                            <span class="fw-semibold small" id="nfc-last-time">{{ $lastScan['time'] ?? 'Belum pernah discan' }}</span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Status Kartu</small>
                            <span class="badge bg-info-subtle text-info">Tersimpan di Cache</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Pembayaran via NFC / RFID --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1"><i class="fas fa-list-check text-primary me-2"></i>Riwayat Pembayaran via Tap (NFC & RFID)</h5>
                <p class="text-muted small mb-0">Transaksi iuran yang masuk melalui sensor NFC HP Android maupun alat ESP32</p>
            </div>
            <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Semua Data
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Tanggal & Waktu</th>
                            <th>Nama Warga</th>
                            <th>Jenis Iuran</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="nfc-history-tbody">
                        @forelse($nfcPembayarans as $idx => $p)
                            <tr>
                                <td class="ps-4 text-muted small">{{ $idx + 1 }}</td>
                                <td class="small">{{ date('d/m/Y H:i', strtotime($p->created_at)) }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->warga->nama_lengkap ?? 'Warga Tidak Diketahui' }}</div>
                                    <small class="text-muted font-monospace">{{ $p->warga->rfid_uid ?? '-' }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $p->iuran->nama_iuran ?? '-' }}</span></td>
                                <td class="fw-bold text-success">Rp {{ number_format($p->jumlah_dibayar, 0, ',', '.') }}</td>
                                <td>
                                    @if(str_contains($p->keterangan, 'NFC'))
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                            <i class="fas fa-mobile-alt me-1"></i> NFC Android
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                                            <i class="fas fa-microchip me-1"></i> RFID ESP32
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success rounded-pill px-3">
                                        <i class="fas fa-check-circle me-1"></i> Lunas (Verified)
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada transaksi pembayaran via Tap NFC / RFID.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
let currentNfcMode = 'pay'; // 'pay' atau 'check'
let isNfcScanning = false;
let ndefReaderInstance = null;

// Audio Beep generator menggunakan Web Audio API
function playDigitalBeep(type = 'success') {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();

        if (type === 'success') {
            // Suara dua nada naik (ting-ting)
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, audioCtx.currentTime); // D5
            osc.frequency.setValueAtTime(880.00, audioCtx.currentTime + 0.08); // A5
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.25);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.25);
        } else if (type === 'warning') {
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(440, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.2);
        } else {
            // Suara error nada rendah
            osc.type = 'sawtooth';
            osc.frequency.setValueAtTime(220, audioCtx.currentTime);
            gain.gain.setValueAtTime(0.25, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
            osc.connect(gain);
            gain.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.35);
        }
    } catch (e) {
        console.warn('Audio Context tidak diizinkan atau tidak didukung:', e);
    }
}

// Deteksi awal apakah browser mendukung Web NFC API
document.addEventListener('DOMContentLoaded', () => {
    const alertBox = document.getElementById('nfc-support-alert');
    const icon = document.getElementById('nfc-support-icon');
    const title = document.getElementById('nfc-support-title');
    const desc = document.getElementById('nfc-support-desc');
    const badge = document.getElementById('nfc-support-badge');

    alertBox.classList.remove('d-none');

    if ('NDEFReader' in window) {
        alertBox.className = 'alert bg-success-subtle border border-success-subtle rounded-4 shadow-sm mb-4';
        icon.innerHTML = '<i class="fas fa-check-circle text-success fa-2x"></i>';
        title.className = 'fw-bold mb-0 text-success';
        title.textContent = 'Sensor Web NFC Didukung di Browser Ini!';
        desc.textContent = 'Perangkat HP Android dan browser Anda siap untuk membaca tap kartu NFC langsung.';
        badge.className = 'badge bg-success rounded-pill px-3 py-2';
        badge.innerHTML = '<i class="fas fa-check me-1"></i> Siap Digunakan';
    } else {
        alertBox.className = 'alert bg-warning-subtle border border-warning-subtle rounded-4 shadow-sm mb-4';
        icon.innerHTML = '<i class="fas fa-mobile-screen text-warning fa-2x"></i>';
        title.className = 'fw-bold mb-0 text-warning-emphasis';
        title.textContent = 'Web NFC Belum Terdeteksi di Browser Ini';
        desc.textContent = 'Untuk tap kartu via HP: Buka web ini di Google Chrome pada HP Android berfitur NFC. Di laptop, Anda tetap bisa pakai input simulasi manual.';
        badge.className = 'badge bg-warning text-dark rounded-pill px-3 py-2';
        badge.innerHTML = '<i class="fas fa-info-circle me-1"></i> Buka di Android';
    }
});

// Ganti Mode (Bayar vs Cek Kartu)
function setNfcMode(mode) {
    currentNfcMode = mode;
    const tabPay = document.getElementById('tab-mode-pay');
    const tabCheck = document.getElementById('tab-mode-check');
    const secPay = document.getElementById('section-pay-options');

    if (mode === 'pay') {
        tabPay.classList.add('active');
        tabPay.classList.remove('text-muted');
        tabCheck.classList.remove('active');
        tabCheck.classList.add('text-muted');
        secPay.classList.remove('d-none');
    } else {
        tabCheck.classList.add('active');
        tabCheck.classList.remove('text-muted');
        tabPay.classList.remove('active');
        tabPay.classList.add('text-muted');
        secPay.classList.add('d-none');
    }
}

// Aktifkan / Matikan Sensor NFC HP Android
async function toggleNfcScanner() {
    if (!('NDEFReader' in window)) {
        alert('Browser ini tidak mendukung Web NFC API. Silakan buka halaman ini di Google Chrome pada HP Android yang memiliki sensor NFC.');
        return;
    }

    const btn = document.getElementById('btn-start-nfc');
    const radar = document.getElementById('nfc-radar-box');
    const heading = document.getElementById('nfc-status-heading');
    const subtext = document.getElementById('nfc-status-subtext');
    const indicator = document.getElementById('nfc-listening-indicator');

    if (isNfcScanning) {
        // Hentikan scan
        isNfcScanning = false;
        radar.classList.remove('nfc-scanning');
        btn.className = 'btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow';
        btn.innerHTML = '<i class="fas fa-play me-2"></i> Aktifkan Sensor NFC HP';
        heading.textContent = 'Sensor NFC Dimatikan';
        subtext.textContent = 'Klik tombol di atas untuk mulai mendengarkan tap kartu lagi.';
        indicator.classList.add('d-none');
        return;
    }

    try {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Meminta Izin Sensor...';

        ndefReaderInstance = new NDEFReader();
        await ndefReaderInstance.scan();

        isNfcScanning = true;
        radar.classList.add('nfc-scanning');
        btn.disabled = false;
        btn.className = 'btn btn-danger btn-lg rounded-pill px-4 fw-bold shadow';
        btn.innerHTML = '<i class="fas fa-stop me-2"></i> Hentikan Sensor NFC';
        heading.textContent = 'Sensor NFC Aktif & Mendengarkan';
        subtext.textContent = 'Silakan TEMPELKAN KARTU ke bagian belakang / kamera HP Anda sekarang!';
        indicator.classList.remove('d-none');

        ndefReaderInstance.onreading = (event) => {
            // Dapatkan serial number (UID) kartu
            let rawSerial = event.serialNumber || '';
            let cleanUid = rawSerial.replace(/[^A-Za-z0-9]/g, '').toUpperCase();

            // Jika serial kosong (beberapa tag NDEF tidak expose serial), generate fallback id
            if (!cleanUid) {
                cleanUid = 'NFC-' + Math.random().toString(36).substring(2, 10).toUpperCase();
            }

            // Haptic getar HP
            if (navigator.vibrate) {
                navigator.vibrate([100, 50, 100]);
            }

            // Proses tap sesuai mode yang dipilih
            handleCardTap(cleanUid);
        };

        ndefReaderInstance.onreadingerror = (error) => {
            playDigitalBeep('error');
            console.error('NFC reading error:', error);
            showResult({
                status: 'error',
                title: 'Gagal Membaca Kartu',
                message: 'Kartu terdeteksi namun data tidak dapat dibaca. Coba tempelkan kembali dengan posisi lebih stabil.'
            });
        };

    } catch (err) {
        btn.disabled = false;
        btn.className = 'btn btn-primary btn-lg rounded-pill px-4 fw-bold shadow';
        btn.innerHTML = '<i class="fas fa-play me-2"></i> Aktifkan Sensor NFC HP';
        alert('Gagal mengaktifkan NFC: ' + err.message + '\n\nPastikan fitur NFC di Pengaturan HP Anda sudah aktif.');
    }
}

// Handler utama saat kartu di-tap
function handleCardTap(uid) {
    // Update live badge kartu terakhir
    document.getElementById('nfc-last-uid').textContent = uid;
    document.getElementById('nfc-last-time').textContent = 'Baru saja';

    if (currentNfcMode === 'pay') {
        processPayNfc(uid);
    } else {
        processCheckNfc(uid);
    }
}

// 1. Eksekusi Pembayaran Iuran via NFC
function processPayNfc(uid) {
    const iuranId = document.getElementById('nfc_iuran_select').value;

    fetch('/api/rfid/tap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            rfid_uid: uid,
            iuran_id: iuranId,
            source: 'nfc_android'
        })
    })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(({ status, data }) => {
        if (status === 200 && data.code === 'PAYMENT_SUCCESS') {
            playDigitalBeep('success');
            showResult({
                status: 'success',
                title: 'Pembayaran Berhasil! (Lunas)',
                sub: `Waktu: ${data.tanggal}`,
                detailsHtml: `
                    <div class="row g-2 mb-2">
                        <div class="col-6"><span class="text-muted small">Nama Warga:</span><div class="fw-bold fs-6">${data.warga}</div></div>
                        <div class="col-6"><span class="text-muted small">UID Kartu:</span><div class="font-monospace text-primary fw-bold">${data.rfid_uid}</div></div>
                        <div class="col-6"><span class="text-muted small">Jenis Iuran:</span><div class="fw-bold">${data.iuran}</div></div>
                        <div class="col-6"><span class="text-muted small">Nominal:</span><div class="fw-bold text-success fs-5">Rp ${Number(data.nominal).toLocaleString('id-ID')}</div></div>
                    </div>
                    <div class="alert alert-success bg-white border-0 py-2 px-3 rounded-3 small mb-0 d-flex justify-content-between">
                        <span><i class="fas fa-wallet text-success me-1"></i> Saldo Kas RT Terkini:</span>
                        <b class="text-success">Rp ${Number(data.saldo_kas).toLocaleString('id-ID')}</b>
                    </div>
                `
            });

            // Tambahkan baris baru ke tabel riwayat secara dinamis
            prependHistoryRow({
                time: data.tanggal,
                warga: data.warga,
                uid: data.rfid_uid,
                iuran: data.iuran,
                nominal: data.nominal
            });

        } else if (status === 200 && data.code === 'ALREADY_PAID') {
            playDigitalBeep('warning');
            showResult({
                status: 'warning',
                title: 'Iuran Sudah Lunas!',
                sub: data.message,
                detailsHtml: `
                    <div class="small">
                        Warga <b>${data.warga}</b> (UID: <code>${data.rfid_uid}</code>) tercatat sudah melunasi iuran <b>${data.iuran}</b> untuk periode berjalan pada tanggal <b>${data.tanggal_bayar}</b>.
                    </div>
                `
            });
        } else if (status === 404) {
            playDigitalBeep('error');
            showResult({
                status: 'danger',
                title: 'Kartu Belum Terdaftar!',
                sub: `UID: ${data.rfid_uid}`,
                detailsHtml: `
                    <div class="small mb-3">
                        Kartu dengan UID <b>${data.rfid_uid}</b> belum diasosiasikan dengan warga manapun di RT.
                    </div>
                    <a href="{{ route('warga.create') }}" class="btn btn-sm btn-danger rounded-pill px-3">
                        <i class="fas fa-user-plus me-1"></i> Daftarkan Kartu Ini ke Warga
                    </a>
                `
            });
        } else {
            playDigitalBeep('error');
            showResult({
                status: 'danger',
                title: 'Transaksi Gagal (' + (data.code || status) + ')',
                sub: data.message || 'Terjadi kesalahan sistem.',
                detailsHtml: `<small class="text-muted">${data.message}</small>`
            });
        }
    })
    .catch(err => {
        playDigitalBeep('error');
        showResult({
            status: 'danger',
            title: 'Kesalahan Jaringan',
            sub: 'Gagal terhubung ke server',
            detailsHtml: `<small>${err.message}</small>`
        });
    });
}

// 2. Eksekusi Mode Cek Kartu Warga (Tanpa Potong Iuran)
function processCheckNfc(uid) {
    fetch(`/api/rfid/check/${uid}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.warga) {
                playDigitalBeep('success');
                showResult({
                    status: 'info',
                    title: 'Kartu Terdaftar!',
                    sub: `Pemilik: ${data.warga.nama}`,
                    detailsHtml: `
                        <div class="row g-2 mb-2">
                            <div class="col-6"><span class="text-muted small">Nama:</span><div class="fw-bold">${data.warga.nama}</div></div>
                            <div class="col-6"><span class="text-muted small">NIK:</span><div class="font-monospace">${data.warga.nik || '-'}</div></div>
                            <div class="col-12"><span class="text-muted small">Alamat:</span><div>${data.warga.alamat || '-'}</div></div>
                            <div class="col-6"><span class="text-muted small">Status Warga:</span><div><span class="badge bg-success">${data.warga.status}</span></div></div>
                            <div class="col-6"><span class="text-muted small">UID Kartu:</span><div class="font-monospace text-primary fw-bold">${data.rfid_uid}</div></div>
                        </div>
                    `
                });
            } else {
                playDigitalBeep('error');
                showResult({
                    status: 'danger',
                    title: 'Kartu Belum Terdaftar!',
                    sub: `UID: ${uid}`,
                    detailsHtml: `
                        <p class="small mb-2">Kartu NFC/RFID ini belum terdaftar pada sistem warga RT.</p>
                        <a href="{{ route('warga.create') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="fas fa-plus me-1"></i> Daftarkan Sekarang
                        </a>
                    `
                });
            }
        })
        .catch(err => {
            playDigitalBeep('error');
            showResult({
                status: 'danger',
                title: 'Gagal Mengecek Kartu',
                sub: err.message,
                detailsHtml: ''
            });
        });
}

// Tampilkan Hasil di Kotak Respon
function showResult({ status, title, sub, detailsHtml }) {
    const box = document.getElementById('nfc_result_box');
    const alert = document.getElementById('nfc_alert_box');
    const icon = document.getElementById('nfc_result_icon');
    const titleEl = document.getElementById('nfc_result_title');
    const subEl = document.getElementById('nfc_result_sub');
    const details = document.getElementById('nfc_result_details');

    box.classList.remove('d-none');
    titleEl.textContent = title;
    subEl.textContent = sub || '';
    details.innerHTML = detailsHtml || '';

    if (status === 'success') {
        alert.className = 'p-4 rounded-4 bg-success-subtle text-success border border-success';
        icon.className = 'fas fa-circle-check text-success fa-2x';
    } else if (status === 'warning') {
        alert.className = 'p-4 rounded-4 bg-warning-subtle text-warning-emphasis border border-warning';
        icon.className = 'fas fa-triangle-exclamation text-warning fa-2x';
    } else if (status === 'info') {
        alert.className = 'p-4 rounded-4 bg-info-subtle text-info-emphasis border border-info';
        icon.className = 'fas fa-id-card text-info fa-2x';
    } else {
        alert.className = 'p-4 rounded-4 bg-danger-subtle text-danger border border-danger';
        icon.className = 'fas fa-circle-xmark text-danger fa-2x';
    }

    // Scroll otomatis ke hasil di layar HP
    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Tambah baris riwayat instan di atas tabel
function prependHistoryRow({ time, warga, uid, iuran, nominal }) {
    const tbody = document.getElementById('nfc-history-tbody');
    const row = document.createElement('tr');
    row.className = 'table-success';
    row.innerHTML = `
        <td class="ps-4"><span class="badge bg-success">Baru</span></td>
        <td class="small">${time}</td>
        <td>
            <div class="fw-bold text-dark">${warga}</div>
            <small class="text-muted font-monospace">${uid}</small>
        </td>
        <td><span class="badge bg-light text-dark border">${iuran}</span></td>
        <td class="fw-bold text-success">Rp ${Number(nominal).toLocaleString('id-ID')}</td>
        <td>
            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1">
                <i class="fas fa-mobile-alt me-1"></i> NFC Android
            </span>
        </td>
        <td>
            <span class="badge bg-success rounded-pill px-3">
                <i class="fas fa-check-circle me-1"></i> Lunas (Verified)
            </span>
        </td>
    `;
    tbody.prepend(row);
}

// Helper Simulasi Manual
function generateRandomNfcUid() {
    const hex = '0123456789ABCDEF';
    let uid = '';
    for (let i = 0; i < 8; i++) {
        uid += hex.charAt(Math.floor(Math.random() * hex.length));
    }
    document.getElementById('manual_uid_input').value = uid;
}

function submitManualTap() {
    const uid = document.getElementById('manual_uid_input').value.trim();
    if (!uid) {
        alert('Masukkan UID terlebih dahulu.');
        return;
    }
    handleCardTap(uid);
}
</script>
@endpush
