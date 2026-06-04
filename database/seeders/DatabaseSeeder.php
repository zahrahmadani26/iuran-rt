<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Warga;
use App\Models\Iuran;
use App\Models\Keuangan;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Buat Admin
        $admin = User::create([
            'name' => 'Admin RT',
            'email' => 'admin@rt.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
        
        // Buat Bendahara
        $bendahara = User::create([
            'name' => 'Bendahara RT',
            'email' => 'bendahara@rt.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara'
        ]);
        
        // Buat Warga
        $wargaUser = User::create([
            'name' => 'Warga 1',
            'email' => 'warga@rt.com',
            'password' => Hash::make('password'),
            'role' => 'warga'
        ]);
        
        Warga::create([
            'user_id' => $wargaUser->id,
            'nik' => '1234567890123456',
            'no_kk' => '1234567890123456',
            'nama_lengkap' => 'Warga 1',
            'alamat' => 'Jl. Kenangan No. 123',
            'no_telp' => '081234567890'
        ]);
        
        // Buat data iuran contoh
        Iuran::create([
            'nama_iuran' => 'Iuran Bulanan',
            'nominal' => 50000,
            'periode' => 'bulanan',
            'tanggal_jatuh_tempo' => '2024-12-10'
        ]);
        
        // Buat data keuangan contoh
        Keuangan::create([
            'jenis' => 'pemasukan',
            'kategori' => 'Iuran Warga',
            'deskripsi' => 'Pembayaran iuran bulan November',
            'nominal' => 500000,
            'tanggal' => '2024-11-01'
        ]);
        
        Keuangan::create([
            'jenis' => 'pengeluaran',
            'kategori' => 'Kebersihan',
            'deskripsi' => 'Bayar kebersihan lingkungan',
            'nominal' => 200000,
            'tanggal' => '2024-11-05'
        ]);
    }
}