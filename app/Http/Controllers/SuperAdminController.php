<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    /**
     * Dashboard Super Admin — daftar warga pending verifikasi
     */
    public function index()
    {
        $pendingUsers = User::where('role', 'warga')
            ->where('status_akun', 'pending')
            ->with('warga')
            ->latest()
            ->get();

        $allWargaUsers = User::where('role', 'warga')
            ->where('status_akun', '!=', 'pending')
            ->with('warga')
            ->latest()
            ->get();

        $allAdmins = User::whereIn('role', ['admin', 'bendahara', 'super_admin'])
            ->latest()
            ->get();

        $stats = [
            'pending'  => User::where('status_akun', 'pending')->count(),
            'aktif'    => User::where('status_akun', 'aktif')->where('role', 'warga')->count(),
            'ditolak'  => User::where('status_akun', 'ditolak')->count(),
            'admin'    => User::whereIn('role', ['admin', 'bendahara', 'super_admin'])->count(),
        ];

        return view('super_admin.index', compact('pendingUsers', 'allWargaUsers', 'allAdmins', 'stats'));
    }

    /**
     * Verifikasi (setujui) akun warga
     */
    public function verifikasi($id)
    {
        $user = User::findOrFail($id);

        $user->update(['status_akun' => 'aktif']);

        // Aktifkan juga data warganya
        if ($user->warga) {
            $user->warga->update(['status' => 'aktif']);
        }

        return redirect()->route('super_admin.index')
            ->with('success', "✅ Akun warga \"{$user->name}\" berhasil diverifikasi dan diaktifkan.");
    }

    /**
     * Tolak akun warga
     */
    public function tolak($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status_akun' => 'ditolak']);

        return redirect()->route('super_admin.index')
            ->with('error', "❌ Akun warga \"{$user->name}\" telah ditolak.");
    }

    /**
     * Ubah role user (misalnya jadikan admin / bendahara)
     */
    public function ubahRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:super_admin,admin,bendahara,warga',
        ]);

        $user = User::findOrFail($id);

        // Jangan izinkan super admin mengubah dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak bisa mengubah role diri sendiri.');
        }

        $user->update([
            'role'       => $request->role,
            'status_akun' => 'aktif', // admin selalu aktif
        ]);

        return redirect()->route('super_admin.index')
            ->with('success', "✅ Role \"{$user->name}\" berhasil diubah menjadi " . ucfirst($request->role) . ".");
    }

    /**
     * Hapus user (soft delete / permanen)
     */
    public function hapus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('super_admin.index')
            ->with('success', "🗑️ Akun \"{$nama}\" berhasil dihapus.");
    }
}
