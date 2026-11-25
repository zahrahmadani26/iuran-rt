<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    private function isPengurus(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();

        $query = Laporan::with(['warga', 'user'])->latest();

        if ($request->filled('scope') && $request->scope === 'saya') {
            $query->where(function ($q) use ($user, $warga) {
                $q->where('user_id', $user->id);
                if ($warga) {
                    $q->orWhere('warga_id', $warga->id);
                }
            });
        }

        if ($request->filled('status') && in_array($request->status, ['baru', 'diproses', 'selesai', 'ditolak'])) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori') && in_array($request->kategori, ['infrastruktur', 'kebersihan', 'keamanan', 'sosial', 'lainnya'])) {
            $query->where('kategori', $request->kategori);
        }

        $laporans = $query->get();

        $stats = [
            'baru'     => Laporan::where('status', 'baru')->count(),
            'diproses' => Laporan::where('status', 'diproses')->count(),
            'selesai'  => Laporan::where('status', 'selesai')->count(),
            'total'    => Laporan::count(),
        ];

        return view('laporan.index', compact('laporans', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:180',
            'deskripsi' => 'required|string',
            'kategori'  => 'required|in:infrastruktur,kebersihan,keamanan,sosial,lainnya',
            'lokasi'    => 'nullable|string|max:180',
            'foto'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\.\-_]/', '', $file->getClientOriginalName());
            $fotoPath = $file->storeAs('laporan_foto', $filename, 'public');
        }

        Laporan::create([
            'user_id'   => $user->id,
            'warga_id'  => $warga?->id,
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kategori'  => $request->kategori,
            'lokasi'    => $request->lokasi,
            'foto'      => $fotoPath,
            'status'    => 'baru',
        ]);

        return redirect()->route('laporan.index')->with('success', 'Laporan / aspirasi Anda berhasil dikirim ke Pengurus RT.');
    }

    public function updateStatus(Request $request, Laporan $laporan)
    {
        $request->validate([
            'status'          => 'required|in:baru,diproses,selesai,ditolak',
            'tanggapan_admin' => 'nullable|string|max:1000',
        ]);

        $laporan->update([
            'status'          => $request->status,
            'tanggapan_admin' => $request->tanggapan_admin,
        ]);

        return redirect()->route('laporan.index')->with('success', 'Status dan tanggapan laporan berhasil diperbarui.');
    }

    public function destroy(Laporan $laporan)
    {
        $user = auth()->user();
        if (!$this->isPengurus() && $laporan->user_id !== $user->id) {
            abort(403, 'Tidak diizinkan menghapus laporan ini.');
        }

        if ($laporan->foto && Storage::disk('public')->exists($laporan->foto)) {
            Storage::disk('public')->delete($laporan->foto);
        }

        $laporan->delete();
        return redirect()->route('laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }
}
