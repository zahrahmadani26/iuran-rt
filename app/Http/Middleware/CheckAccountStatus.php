<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Super admin selalu lolos
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        // Cek status akun
        if ($user->status_akun === 'pending') {
            auth()->logout();
            return redirect('/login')->withErrors([
                'email' => '⏳ Akun kamu masih menunggu verifikasi dari Ketua RT. Silakan tunggu konfirmasi.'
            ]);
        }

        if ($user->status_akun === 'ditolak') {
            auth()->logout();
            return redirect('/login')->withErrors([
                'email' => '❌ Akun kamu telah ditolak. Hubungi Ketua RT untuk informasi lebih lanjut.'
            ]);
        }

        return $next($request);
    }
}
