@extends('layouts.app')

@section('title', 'Sistem RFID & IoT ESP32')

@section('content')
<div class="container-fluid p-0">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-id-card-clip text-primary me-2"></i>Sistem RFID ESP32 (RC522)</h4>
            <p class="text-muted mb-0 small">Monitoring, Registrasi Kartu, dan Integrasi Perangkat IoT Tap Iuran Otomatis</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('warga.index') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="fas fa-users me-1"></i> Data Warga
            </a>
            <a href="#arduino-code" class="btn btn-primary rounded-pill px-3">
                <i class="fas fa-code me-1"></i> Source Code ESP32
            </a>
        </div>
    </div>

    @include('layouts.alerts')

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="fas fa-microchip fa-2x"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Status API IoT</small>
                        <h6 class="fw-bold mb-0 text-success">
                            <i class="fas fa-circle me-1" style="font-size: .6rem;"></i> Online (Active)
                        </h6>
                        <small class="text-muted font-monospace" style="font-size:.7rem;">/api/rfid/tap</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="fas fa-id-card fa-2x"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Warga Punya RFID</small>
                        <h4 class="fw-bold mb-0 text-success">{{ $wargaWithRfid }}</h4>
                        <small class="text-muted">dari {{ $totalWarga }} warga</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="fas fa-credit-card fa-2x"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Belum Pasang RFID</small>
                        <h4 class="fw-bold mb-0 text-warning">{{ $wargaWithoutRfid }}</h4>
                        <small class="text-muted">butuh kartu</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                        <i class="fas fa-wifi fa-2x"></i>
                    </div>
                    <div class="overflow-hidden">
                        <small class="text-muted d-block">Kartu Terakhir Ditap</small>
                        <h6 class="fw-bold mb-0 font-monospace text-truncate" id="stat-last-uid">
                            {{ $lastScan['uid'] ?? 'Belum ada' }}
                        </h6>
                        <small class="text-muted" id="stat-last-time">{{ $lastScan['time'] ?? '-' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Simulator Tap RFID --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1"><i class="fas fa-laptop-code text-primary me-2"></i>Simulator Tap Kartu RFID</h5>
                        <p class="text-muted small mb-0">Uji coba simulasi tap kartu langsung dari web browser</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">Testing Tool</span>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Warga (Otomatis isi UID terdaftar)</label>
                        <select id="sim_warga_select" class="form-select rounded-3" onchange="applySimWarga(this)">
                            <option value="">-- Pilih Warga atau Ketik UID Manual --</option>
                            @foreach($wargas as $w)
                                <option value="{{ $w->rfid_uid }}" data-nama="{{ $w->nama_lengkap }}" data-has="{{ $w->rfid_uid ? '1' : '0' }}">
                                    {{ $w->nama_lengkap }} ({{ $w->rfid_uid ? 'UID: ' . $w->rfid_uid : 'Belum ada RFID' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">RFID UID (Hex)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light font-monospace"><i class="fas fa-id-badge text-muted"></i></span>
                            <input type="text" id="sim_uid_input" class="form-control font-monospace" placeholder="Contoh: A1B2C3D4">
                            <button type="button" class="btn btn-outline-secondary" onclick="generateRandomUid()">
                                <i class="fas fa-dice me-1"></i> Random UID
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Jenis Iuran</label>
                        <select id="sim_iuran_select" class="form-select rounded-3">
                            @foreach($iurans as $i)
                                <option value="{{ $i->id }}">
                                    {{ $i->nama_iuran }} - Rp {{ number_format($i->nominal, 0, ',', '.') }} ({{ ucfirst($i->periode) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="button" class="btn btn-primary w-100 py-2 rounded-pill fw-bold" id="btn-simulate-tap" onclick="simulateTap()">
                        <i class="fas fa-fingerprint me-2"></i> Simulasikan Tap Kartu RFID (Kirim ke API)
                    </button>

                    {{-- Result Area --}}
                    <div id="sim_result_box" class="mt-4 d-none">
                        <div class="p-3 rounded-4" id="sim_alert_box">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i id="sim_icon" class="fas fa-info-circle fa-lg"></i>
                                <strong id="sim_title">Hasil Tap:</strong>
                            </div>
                            <div id="sim_message" class="small mb-2"></div>
                            <pre class="bg-dark text-light p-3 rounded-3 mb-0 small" style="max-height: 160px; overflow-y: auto;" id="sim_json"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Skema Rangkaian ESP32 & RC522 --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-1"><i class="fas fa-diagram-project text-primary me-2"></i>Skema Pinout Modul RC522 & ESP32</h5>
                    <p class="text-muted small mb-0">Hubungkan pin RFID RC522 ke board ESP32 sesuai tabel SPI berikut:</p>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle text-center mb-3">
                            <thead class="table-primary">
                                <tr>
                                    <th>Pin RC522</th>
                                    <th>Pin ESP32</th>
                                    <th>Keterangan / Fungsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b class="text-danger">VCC (3.3V)</b></td>
                                    <td><b class="text-danger">3V3</b></td>
                                    <td><span class="badge bg-danger">Wajib 3.3V (Bukan 5V!)</span></td>
                                </tr>
                                <tr>
                                    <td><b>RST</b></td>
                                    <td><code class="text-primary">GPIO 22</code></td>
                                    <td>Reset Pin</td>
                                </tr>
                                <tr>
                                    <td><b>GND</b></td>
                                    <td><b>GND</b></td>
                                    <td>Ground bersama</td>
                                </tr>
                                <tr>
                                    <td><b>MISO</b></td>
                                    <td><code class="text-primary">GPIO 19</code></td>
                                    <td>SPI MISO (Master In Slave Out)</td>
                                </tr>
                                <tr>
                                    <td><b>MOSI</b></td>
                                    <td><code class="text-primary">GPIO 23</code></td>
                                    <td>SPI MOSI (Master Out Slave In)</td>
                                </tr>
                                <tr>
                                    <td><b>SCK</b></td>
                                    <td><code class="text-primary">GPIO 18</code></td>
                                    <td>SPI SCK (Serial Clock)</td>
                                </tr>
                                <tr>
                                    <td><b>SDA / SS</b></td>
                                    <td><code class="text-primary">GPIO 5</code></td>
                                    <td>SPI Slave Select / Chip Select</td>
                                </tr>
                                <tr class="table-light">
                                    <td><i>Buzzer (+)</i></td>
                                    <td><code class="text-success">GPIO 4</code></td>
                                    <td>Opsional: Indikator Suara Beep</td>
                                </tr>
                                <tr class="table-light">
                                    <td><i>LED Hijau (+)</i></td>
                                    <td><code class="text-success">GPIO 2</code></td>
                                    <td>Opsional: Indikator Berhasil</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        <b>Tips:</b> Modul RFID RC522 bekerja pada level tegangan <b>3.3V</b>. Jangan hubungkan VCC ke pin 5V (Vin) karena dapat merusak modul pembaca kartu.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Pembayaran RFID --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1"><i class="fas fa-clock-rotate-left text-primary me-2"></i>Riwayat Pembayaran via RFID Tap</h5>
                <p class="text-muted small mb-0">Transaksi iuran terbaru yang dilakukan melalui tapping kartu RFID</p>
            </div>
            <a href="{{ route('pembayaran.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                Lihat Semua Pembayaran
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Waktu & Tanggal</th>
                            <th>Nama Warga</th>
                            <th>Jenis Iuran</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th>Keterangan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rfidPembayarans as $idx => $p)
                        <tr>
                            <td class="ps-4 text-muted">{{ $idx + 1 }}</td>
                            <td class="text-muted small">{{ date('d M Y H:i', strtotime($p->created_at)) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                         style="width:30px;height:30px;font-size:.7rem;background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                                        {{ strtoupper(substr($p->warga->nama_lengkap ?? 'W', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold">{{ $p->warga->nama_lengkap ?? '-' }}</span>
                                </div>
                            </td>
                            <td>{{ $p->iuran->nama_iuran ?? '-' }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($p->jumlah_dibayar, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> Verified
                                </span>
                            </td>
                            <td><code class="text-muted small">{{ $p->keterangan }}</code></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-receipt fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada pembayaran melalui RFID Tap. Coba gunakan simulator di atas!
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Source Code ESP32 Arduino --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" id="arduino-code">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1"><i class="fas fa-microchip text-primary me-2"></i>Source Code ESP32 (Arduino IDE / PlatformIO)</h5>
                <p class="text-muted small mb-0">Kode C++ siap flash ke modul ESP32 untuk membaca kartu RC522 dan mengirimkan request ke server</p>
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="copyArduinoCode()">
                <i class="fas fa-copy me-1"></i> Salin Kode
            </button>
        </div>
        <div class="card-body p-4">
            <pre class="bg-dark text-light p-4 rounded-4 small mb-0" style="max-height: 480px; overflow-y: auto;" id="arduino-code-block"><code>/*
 * SISTEM IURAN RT OTOMATIS - ESP32 + MFRC522 RFID READER
 * ----------------------------------------------------
 * Library yang dibutuhkan (Install via Library Manager Arduino IDE):
 * 1. MFRC522 by GithubCommunity
 * 2. ArduinoJson by Benoit Blanchon (Versi 6.x atau 7.x)
 * ----------------------------------------------------
 */

#include &lt;WiFi.h&gt;
#include &lt;HTTPClient.h&gt;
#include &lt;SPI.h&gt;
#include &lt;MFRC522.h&gt;
#include &lt;ArduinoJson.h&gt;

// --- KONFIGURASI WIFI & SERVER LARAVEL ---
const char* ssid = "NAMA_WIFI_ANDA";
const char* password = "PASSWORD_WIFI_ANDA";

// Ganti dengan IP Komputer / Laptop Laragon Anda di jaringan WiFi (misal: http://192.168.1.10:8000/api/rfid/tap)
const char* serverApiUrl = "http://192.168.1.100:8000/api/rfid/tap";

// --- PINOUT RC522 & OUTPUT ---
#define RST_PIN   22
#define SS_PIN    5
#define BUZZER_PIN 4
#define LED_PIN    2

MFRC522 rfid(SS_PIN, RST_PIN);

void beep(int ms, int times = 1) {
  for (int i = 0; i &lt; times; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    digitalWrite(LED_PIN, HIGH);
    delay(ms);
    digitalWrite(BUZZER_PIN, LOW);
    digitalWrite(LED_PIN, LOW);
    if (times &gt; 1) delay(100);
  }
}

void setup() {
  Serial.begin(115200);
  SPI.begin();
  rfid.PCD_Init();

  pinMode(BUZZER_PIN, OUTPUT);
  pinMode(LED_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_PIN, LOW);

  Serial.println("\n[Sistem Iuran RT] Memulai ESP32...");
  Serial.print("Menghubungkan ke WiFi: ");
  Serial.println(ssid);

  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\n[OK] WiFi Terhubung!");
  Serial.print("IP Address ESP32: ");
  Serial.println(WiFi.localIP());

  beep(200, 2); // Beep 2x saat ESP32 siap
  Serial.println("Tempelkan Kartu RFID RT pada sensor RC522...");
}

void loop() {
  // Cek apakah ada kartu baru terdeteksi
  if (!rfid.PICC_IsNewCardPresent()) return;
  if (!rfid.PICC_ReadCardSerial()) return;

  // Baca UID kartu dan format menjadi string Hex uppercase (misal: "A1B2C3D4")
  String cardUid = "";
  for (byte i = 0; i &lt; rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] &lt; 0x10) cardUid += "0";
    cardUid += String(rfid.uid.uidByte[i], HEX);
  }
  cardUid.toUpperCase();

  Serial.println("\n==================================");
  Serial.println("Kartu Terdeteksi! UID: " + cardUid);
  beep(80, 1); // Beep pendek saat tap terdeteksi

  // Kirim data ke API Laravel
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverApiUrl);
    http.addHeader("Content-Type", "application/json");

    // Siapkan JSON Payload
    StaticJsonDocument&lt;200&gt; reqDoc;
    reqDoc["rfid_uid"] = cardUid;
    String requestBody;
    serializeJson(reqDoc, requestBody);

    int httpCode = http.POST(requestBody);
    String response = http.getString();

    Serial.print("HTTP Status Code: ");
    Serial.println(httpCode);
    Serial.println("Response: " + response);

    if (httpCode == 200) {
      StaticJsonDocument&lt;512&gt; resDoc;
      deserializeJson(resDoc, response);

      const char* code = resDoc["code"];
      const char* nama = resDoc["warga"];
      const char* pesan = resDoc["message"];

      if (String(code) == "PAYMENT_SUCCESS") {
        Serial.println("[SUKSES] " + String(pesan));
        Serial.println("Warga: " + String(nama));
        beep(300, 1); // Beep panjang tanda sukses pembayaran
      } else if (String(code) == "ALREADY_PAID") {
        Serial.println("[INFO] " + String(pesan));
        beep(100, 2); // Beep 2x
      }
    } else if (httpCode == 404) {
      Serial.println("[GAGAL] Kartu RFID belum terdaftar di Sistem RT!");
      beep(100, 4); // Beep 4x cepat (Error)
    } else {
      Serial.println("[ERROR] Server merespon dengan error.");
      beep(100, 3);
    }

    http.end();
  } else {
    Serial.println("[ERROR] Koneksi WiFi terputus!");
    beep(500, 1);
  }

  // Hentikan enkripsi pada kartu & bersihkan pembacaan
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();

  delay(2000); // Debounce agar kartu tidak terbaca berkali-kali dalam 2 detik
}</code></pre>
        </div>
    </div>
</div>

@push('scripts')
<script>
function applySimWarga(select) {
    const val = select.value;
    if (val) {
        document.getElementById('sim_uid_input').value = val;
    }
}

function generateRandomUid() {
    const hex = '0123456789ABCDEF';
    let uid = '';
    for (let i = 0; i < 8; i++) {
        uid += hex.charAt(Math.floor(Math.random() * hex.length));
    }
    document.getElementById('sim_uid_input').value = uid;
}

function simulateTap() {
    const uid = document.getElementById('sim_uid_input').value.trim();
    const iuranId = document.getElementById('sim_iuran_select').value;
    const btn = document.getElementById('btn-simulate-tap');
    const resultBox = document.getElementById('sim_result_box');
    const alertBox = document.getElementById('sim_alert_box');
    const title = document.getElementById('sim_title');
    const msg = document.getElementById('sim_message');
    const jsonPre = document.getElementById('sim_json');
    const icon = document.getElementById('sim_icon');

    if (!uid) {
        alert('Silakan masukkan UID atau pilih Warga terlebih dahulu.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim data ke /api/rfid/tap...';

    fetch('/api/rfid/tap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            rfid_uid: uid,
            iuran_id: iuranId
        })
    })
    .then(async res => {
        const data = await res.json();
        return { status: res.status, data };
    })
    .then(({ status, data }) => {
        resultBox.classList.remove('d-none');
        jsonPre.textContent = JSON.stringify(data, null, 2);

        // Update Stat kartu terakhir
        document.getElementById('stat-last-uid').textContent = uid;
        document.getElementById('stat-last-time').textContent = 'Baru saja';

        if (status === 200 && data.code === 'PAYMENT_SUCCESS') {
            alertBox.className = 'p-3 rounded-4 bg-success-subtle text-success border border-success';
            icon.className = 'fas fa-check-circle fa-lg text-success';
            title.textContent = 'Sukses (200 OK): Pembayaran Terverifikasi Otomatis!';
            msg.innerHTML = `Iuran <b>${data.iuran}</b> sebesar <b>Rp ${Number(data.nominal).toLocaleString('id-ID')}</b> atas nama <b>${data.warga}</b> telah berhasil tercatat & saldo kas bertambah.`;
        } else if (status === 200 && data.code === 'ALREADY_PAID') {
            alertBox.className = 'p-3 rounded-4 bg-warning-subtle text-warning-emphasis border border-warning';
            icon.className = 'fas fa-exclamation-circle fa-lg text-warning';
            title.textContent = 'Info (200): ' + data.message;
            msg.innerHTML = `Warga <b>${data.warga}</b> sudah lunas membayar iuran ini untuk periode berjalan pada tanggal ${data.tanggal_bayar}.`;
        } else if (status === 404) {
            alertBox.className = 'p-3 rounded-4 bg-danger-subtle text-danger border border-danger';
            icon.className = 'fas fa-times-circle fa-lg text-danger';
            title.textContent = 'Gagal (404 Not Found): Kartu Belum Terdaftar!';
            msg.innerHTML = `UID <b>${data.rfid_uid}</b> belum diasosiasikan dengan warga manapun. Silakan buka menu <b>Data Warga</b> untuk mendaftarkannya.`;
        } else {
            alertBox.className = 'p-3 rounded-4 bg-danger-subtle text-danger border border-danger';
            icon.className = 'fas fa-exclamation-triangle fa-lg text-danger';
            title.textContent = `Status: ${status} - ${data.message || 'Error'}`;
            msg.innerHTML = data.message || 'Terjadi kesalahan.';
        }
    })
    .catch(err => {
        resultBox.classList.remove('d-none');
        alertBox.className = 'p-3 rounded-4 bg-danger-subtle text-danger border border-danger';
        title.textContent = 'Network Error';
        msg.textContent = 'Tidak dapat menghubungi server API: ' + err.message;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-fingerprint me-2"></i> Simulasikan Tap Kartu RFID (Kirim ke API)';
    });
}

function copyArduinoCode() {
    const code = document.getElementById('arduino-code-block').innerText;
    navigator.clipboard.writeText(code).then(() => {
        alert('Source code ESP32 berhasil disalin ke clipboard!');
    }).catch(err => {
        alert('Gagal menyalin kode: ' + err);
    });
}
</script>
@endpush
@endsection
