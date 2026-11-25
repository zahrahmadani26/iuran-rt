/**
 * ============================================================
 *  SISTEM IURAN RT - ESP32 + RC522 RFID Reader
 * ============================================================
 *  Hardware :  ESP32 DevKit v1  +  MFRC522 RFID Module
 *  Endpoint  :  POST https://iuran-rt-zahrahmadani-app.fly.dev/api/rfid/tap
 *  Library   :  MFRC522 by GithubCommunity
 *               ArduinoJson by Benoit Blanchon
 *               HTTPClient (bawaan Arduino ESP32)
 * ============================================================
 *  WIRING RC522 ke ESP32:
 *    RC522 Pin  ->  ESP32 GPIO
 *    SDA (SS)   ->  GPIO 5   (bisa ganti, sesuaikan SS_PIN)
 *    SCK        ->  GPIO 18
 *    MOSI       ->  GPIO 23
 *    MISO       ->  GPIO 19
 *    IRQ        ->  (tidak dipakai)
 *    GND        ->  GND
 *    RST        ->  GPIO 22  (bisa ganti, sesuaikan RST_PIN)
 *    3.3V       ->  3.3V     !! JANGAN 5V, bisa merusak RC522 !!
 * ============================================================
 */

#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <ArduinoJson.h>

// ============================================================
//  ⚙️  KONFIGURASI — EDIT BAGIAN INI SESUAI KEBUTUHAN
// ============================================================

// --- WiFi ---
const char* WIFI_SSID     = "NamaWiFiKamu";      // Ganti nama WiFi
const char* WIFI_PASSWORD = "PasswordWiFiKamu";   // Ganti password WiFi

// --- Server / API ---
const char* SERVER_URL    = "https://iuran-rt-zahrahmadani-app.fly.dev/api/rfid/tap";
// Jika pakai ngrok lokal, ganti dengan:
// const char* SERVER_URL = "https://storeroom-exhale-pointless.ngrok-free.dev/api/rfid/tap";

// --- Pin RC522 ---
#define SS_PIN   5    // SDA/SS
#define RST_PIN  22   // RST

// --- Cooldown antar tap (ms) — cegah double-tap ---
#define COOLDOWN_MS  3000

// --- LED Indikator (opsional, pakai LED onboard ESP32) ---
#define LED_SUKSES  2    // LED onboard ESP32 (GPIO 2)
// Kalau mau LED tambahan: sambung LED + resistor 220Ω ke GPIO lain

// ============================================================
//  VARIABEL GLOBAL
// ============================================================
MFRC522 rfid(SS_PIN, RST_PIN);
unsigned long lastTapTime = 0;

// ============================================================
//  SETUP
// ============================================================
void setup() {
    Serial.begin(115200);
    delay(500);

    pinMode(LED_SUKSES, OUTPUT);
    digitalWrite(LED_SUKSES, LOW);

    Serial.println("===========================================");
    Serial.println("  SISTEM IURAN RT - RFID Reader ESP32");
    Serial.println("===========================================");

    // Init SPI & MFRC522
    SPI.begin();
    rfid.PCD_Init();
    rfid.PCD_DumpVersionToSerial();
    Serial.println("[RFID] RC522 siap.");

    // Koneksi WiFi
    Serial.print("[WiFi] Menghubungkan ke: ");
    Serial.println(WIFI_SSID);
    WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

    int attempt = 0;
    while (WiFi.status() != WL_CONNECTED && attempt < 30) {
        delay(500);
        Serial.print(".");
        attempt++;
    }

    if (WiFi.status() == WL_CONNECTED) {
        Serial.println("\n[WiFi] ✓ Terhubung!");
        Serial.print("[WiFi] IP Address: ");
        Serial.println(WiFi.localIP());
        blinkLED(3, 150); // Blink 3x = WiFi OK
    } else {
        Serial.println("\n[WiFi] ✗ GAGAL KONEK! Cek SSID/Password.");
        blinkLED(10, 80); // Blink cepat = error
    }

    Serial.println("-------------------------------------------");
    Serial.println("[SIAP] Tempelkan kartu RFID...");
    Serial.println("===========================================");
}

// ============================================================
//  LOOP UTAMA
// ============================================================
void loop() {
    // Reconnect WiFi jika putus
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[WiFi] Koneksi terputus, reconnecting...");
        WiFi.reconnect();
        delay(3000);
        return;
    }

    // Cek apakah ada kartu baru
    if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
        return;
    }

    // Cooldown — cegah tap ganda
    unsigned long now = millis();
    if (now - lastTapTime < COOLDOWN_MS) {
        rfid.PICC_HaltA();
        rfid.PCD_StopCrypto1();
        return;
    }
    lastTapTime = now;

    // Ambil UID
    String uid = bacaUID();
    Serial.println("-------------------------------------------");
    Serial.print("[RFID] Kartu terdeteksi! UID: ");
    Serial.println(uid);

    // Kirim ke server
    kirimKeServer(uid);

    // Hentikan komunikasi kartu
    rfid.PICC_HaltA();
    rfid.PCD_StopCrypto1();

    Serial.println("[SIAP] Tempelkan kartu RFID...");
    Serial.println("===========================================");
}

// ============================================================
//  FUNGSI: Baca UID dari kartu RFID
// ============================================================
String bacaUID() {
    String uid = "";
    for (byte i = 0; i < rfid.uid.size; i++) {
        if (rfid.uid.uidByte[i] < 0x10) uid += "0";
        uid += String(rfid.uid.uidByte[i], HEX);
    }
    uid.toUpperCase();
    return uid;
}

// ============================================================
//  FUNGSI: Kirim UID ke API Laravel
// ============================================================
void kirimKeServer(String uid) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("[HTTP] Gagal: WiFi tidak terhubung.");
        return;
    }

    HTTPClient http;
    http.begin(SERVER_URL);
    http.addHeader("Content-Type", "application/json");
    http.addHeader("Accept", "application/json");
    // Timeout 10 detik
    http.setTimeout(10000);

    // Buat JSON payload
    StaticJsonDocument<256> doc;
    doc["rfid_uid"] = uid;
    doc["source"]   = "rfid_esp32";

    String payload;
    serializeJson(doc, payload);

    Serial.print("[HTTP] POST ke: ");
    Serial.println(SERVER_URL);
    Serial.print("[HTTP] Payload: ");
    Serial.println(payload);

    int httpCode = http.POST(payload);

    Serial.print("[HTTP] Response Code: ");
    Serial.println(httpCode);

    if (httpCode > 0) {
        String response = http.getString();
        Serial.print("[HTTP] Response: ");
        Serial.println(response);

        // Parse JSON response
        StaticJsonDocument<512> res;
        DeserializationError err = deserializeJson(res, response);

        if (!err) {
            bool success   = res["success"] | false;
            String code    = res["code"]    | "UNKNOWN";
            String message = res["message"] | "Tidak ada pesan.";
            String warga   = res["warga"]   | "";

            Serial.println("---");
            Serial.print("[STATUS] Code   : "); Serial.println(code);
            Serial.print("[STATUS] Pesan  : "); Serial.println(message);
            if (warga != "") {
                Serial.print("[STATUS] Warga  : "); Serial.println(warga);
            }

            if (success && code == "PAYMENT_SUCCESS") {
                // ✅ Pembayaran berhasil
                long nominal  = res["nominal"]   | 0;
                String iuran  = res["iuran"]     | "";
                long saldo    = res["saldo_kas"] | 0;
                Serial.print("[STATUS] Iuran  : "); Serial.println(iuran);
                Serial.print("[STATUS] Nominal: Rp "); Serial.println(nominal);
                Serial.print("[STATUS] Saldo Kas: Rp "); Serial.println(saldo);
                blinkLED(2, 300);  // Blink 2x lambat = sukses

            } else if (code == "ALREADY_PAID") {
                // ⚠️ Sudah bayar bulan ini
                Serial.println("[STATUS] ⚠ Sudah lunas bulan ini!");
                blinkLED(5, 100);  // Blink 5x cepat = sudah bayar

            } else if (code == "NOT_REGISTERED") {
                // ❌ Kartu belum terdaftar
                Serial.println("[STATUS] ❌ Kartu BELUM TERDAFTAR di sistem!");
                Serial.println("[STATUS] Daftarkan kartu ini di Web Admin.");
                blinkLED(1, 1000); // Blink 1x panjang = error

            } else if (code == "WARGA_INACTIVE") {
                // ❌ Warga non-aktif
                Serial.println("[STATUS] ❌ Warga berstatus Non-Aktif!");
                blinkLED(1, 1000);

            } else {
                Serial.println("[STATUS] ❌ Error tidak dikenal.");
                blinkLED(1, 1000);
            }
        } else {
            Serial.println("[HTTP] Gagal parse JSON response.");
        }

    } else {
        Serial.print("[HTTP] ✗ Error koneksi ke server: ");
        Serial.println(http.errorToString(httpCode).c_str());
        blinkLED(1, 2000);
    }

    http.end();
}

// ============================================================
//  FUNGSI: Kedipkan LED
// ============================================================
void blinkLED(int kali, int durasiMs) {
    for (int i = 0; i < kali; i++) {
        digitalWrite(LED_SUKSES, HIGH);
        delay(durasiMs);
        digitalWrite(LED_SUKSES, LOW);
        if (i < kali - 1) delay(durasiMs / 2);
    }
}
