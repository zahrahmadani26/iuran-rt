<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WargaSeeder extends Seeder
{
    public function run(): void
    {
        $namaDepan = [
            'Ahmad', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Gunawan', 'Hendra', 'Indah', 'Joko',
            'Kartika', 'Lukman', 'Maya', 'Nugroho', 'Putri', 'Rahmat', 'Sari', 'Taufik', 'Umar', 'Wulan',
            'Agus', 'Bayu', 'Dian', 'Fajar', 'Gita', 'Hadi', 'Irfan', 'Lestari', 'Muhammad', 'Nadia',
            'Oki', 'Pandu', 'Ratna', 'Siti', 'Teguh', 'Wahyu', 'Yudi', 'Zainal', 'Arif', 'Bagus',
            'Cahyo', 'Desi', 'Endang', 'Farhan', 'Galih', 'Herman', 'Intan', 'Johan', 'Kurnia', 'Linda',
        ];

        $namaBelakang = [
            'Santoso', 'Wijaya', 'Pratama', 'Lestari', 'Saputra', 'Utami', 'Hidayat', 'Kusuma', 'Nugraha', 'Purnama',
            'Setiawan', 'Rahayu', 'Wibowo', 'Permana', 'Anggraini', 'Hakim', 'Siregar', 'Maulana', 'Hartono', 'Suryani',
            'Kurniawan', 'Sari', 'Susanto', 'Handayani', 'Prasetyo', 'Fitriani', 'Ramadhan', 'Puspita', 'Fauzi', 'Marlina',
        ];

        $bloks = ['Blok A', 'Blok B', 'Blok C', 'Blok D', 'Blok E'];
        $jalans = ['Jl. Melati', 'Jl. Mawar', 'Jl. Kenanga', 'Jl. Anggrek', 'Jl. Flamboyan'];
        $prefixes = ['0812', '0813', '0821', '0852', '0857', '0878'];

        $hashedPassword = Hash::make('password');

        DB::transaction(function () use ($namaDepan, $namaBelakang, $bloks, $jalans, $prefixes, $hashedPassword) {
            for ($i = 1; $i <= 100; $i++) {
                $depan = $namaDepan[($i - 1) % count($namaDepan)];
                $belakang = $namaBelakang[(int)(($i - 1) * 7 / 3 + ($i - 1)) % count($namaBelakang)];
                $namaLengkap = "{$depan} {$belakang}";

                $email = sprintf('warga%03d@rt.com', $i);
                $nik   = '327501' . sprintf('%02d%02d%02d%04d', (($i % 28) + 1), (($i % 12) + 1), (75 + ($i % 25)), $i);
                $noKk  = '327501100120' . sprintf('%04d', $i);

                // Skip jika NIK atau No KK atau email sudah ada
                if (User::where('email', $email)->exists() || Warga::where('nik', $nik)->orWhere('no_kk', $noKk)->exists()) {
                    continue;
                }

                $blokIdx = (int)(($i - 1) / 20) % count($bloks);
                $noRumah = (($i - 1) % 20) + 1;
                $alamat  = "{$jalans[$blokIdx]} {$bloks[$blokIdx]} No. {$noRumah}, RT 01/RW 01";
                $noTelp  = $prefixes[$i % count($prefixes)] . sprintf('%08d', 10000000 + $i * 137);

                $user = User::create([
                    'name'        => $namaLengkap,
                    'email'       => $email,
                    'password'    => $hashedPassword,
                    'role'        => 'warga',
                    'status_akun' => 'aktif',
                ]);

                Warga::create([
                    'user_id'      => $user->id,
                    'nik'          => $nik,
                    'no_kk'        => $noKk,
                    'nama_lengkap' => $namaLengkap,
                    'alamat'       => $alamat,
                    'no_telp'      => $noTelp,
                    'status'       => 'aktif',
                    'rfid_uid'     => null,
                ]);
            }
        });
    }
}
