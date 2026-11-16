<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Warga;
use App\Models\Iuran;
use App\Models\Pembayaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RfidTest extends TestCase
{
    use RefreshDatabase;

    public function test_rfid_tap_with_unregistered_card_returns_404()
    {
        $response = $this->postJson('/api/rfid/tap', [
            'rfid_uid' => 'UNKNOWN123'
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'code' => 'NOT_REGISTERED'
                 ]);
    }

    public function test_rfid_tap_with_registered_card_creates_verified_payment()
    {
        $user = User::factory()->create(['role' => 'warga']);
        $warga = Warga::create([
            'user_id' => $user->id,
            'nik' => '1234567890123456',
            'no_kk' => '1234567890123456',
            'nama_lengkap' => 'Budi Santoso',
            'alamat' => 'Jl. Mawar No. 1',
            'no_telp' => '08123456789',
            'status' => 'aktif',
            'rfid_uid' => 'TESTUID01'
        ]);

        $iuran = Iuran::create([
            'nama_iuran' => 'Iuran Sampah',
            'nominal' => 25000,
            'periode' => 'bulanan',
            'tanggal_jatuh_tempo' => now()->addDays(10)->toDateString()
        ]);

        // Tap pertama -> Pembayaran sukses
        $response = $this->postJson('/api/rfid/tap', [
            'rfid_uid' => 'TESTUID01'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'code' => 'PAYMENT_SUCCESS',
                     'nominal' => 25000,
                     'warga' => 'Budi Santoso'
                 ]);

        $this->assertDatabaseHas('pembayarans', [
            'warga_id' => $warga->id,
            'iuran_id' => $iuran->id,
            'status' => 'verified'
        ]);

        // Tap kedua di bulan yang sama -> Proteksi double tap
        $secondTap = $this->postJson('/api/rfid/tap', [
            'rfid_uid' => 'TESTUID01'
        ]);

        $secondTap->assertStatus(200)
                  ->assertJson([
                      'success' => false,
                      'code' => 'ALREADY_PAID'
                  ]);
    }

    public function test_last_scanned_rfid_endpoint()
    {
        $this->postJson('/api/rfid/scan', [
            'rfid_uid' => 'SCANUID99'
        ]);

        $response = $this->getJson('/api/rfid/last-scan');
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'rfid_uid' => 'SCANUID99'
                 ]);
    }
}
