<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterWargaController extends Controller
{
    /**
     * Tampilkan form registrasi warga
     */
    public function showForm()
    {
        // Kalau sudah login, langsung ke dashboard
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register_warga');
    }

    /**
     * Proses pendaftaran warga baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:8|confirmed',
            'no_telp'               => 'required|string|max:15',
            'nik'                   => 'required|string|size:16|unique:wargas,nik',
            'no_kk'                 => 'required|string|size:16|unique:wargas,no_kk',
            'alamat'                => 'required|string',
        ], [
            'email.unique'          => 'Email sudah digunakan, gunakan email lain.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
            'password.min'          => 'Password minimal 8 karakter.',
            'nik.unique'            => 'NIK sudah terdaftar dalam sistem.',
            'nik.size'              => 'NIK harus tepat 16 digit.',
            'no_kk.unique'          => 'No. KK sudah terdaftar dalam sistem.',
            'no_kk.size'            => 'No. KK harus tepat 16 digit.',
        ]);

        DB::transaction(function () use ($request) {
            // Buat User dengan status_akun = 'pending'
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'password'      => Hash::make($request->password),
                'role'          => 'warga',
                'status_akun'   => 'pending',
            ]);

            // Buat data Warga dengan status nonaktif dulu (aktif setelah diverifikasi)
            Warga::create([
                'user_id'       => $user->id,
                'nik'           => $request->nik,
                'no_kk'         => $request->no_kk,
                'nama_lengkap'  => $request->name,
                'alamat'        => $request->alamat,
                'no_telp'       => $request->no_telp,
                'status'        => 'nonaktif', // Aktif setelah verifikasi
            ]);
        });

        return redirect()->route('register.warga')
            ->with('success', '✅ Pendaftaran berhasil! Akun kamu sedang menunggu verifikasi dari Ketua RT. Silakan cek kembali dalam 1×24 jam.');
    }
}
