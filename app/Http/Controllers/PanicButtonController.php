<?php

namespace App\Http\Controllers;

use App\Models\PanicButton;
use App\Models\KontakDarurat;
use App\Models\Warga;
use Illuminate\Http\Request;

class PanicButtonController extends Controller
{
    private function isPengurus(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']);
    }

    public function index()
    {
        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();
        $isPengurus = $this->isPengurus();

        $activeAlerts = PanicButton::with(['warga', 'user', 'penangan'])
            ->where('status', 'aktif')
            ->latest()
            ->get();

        $riwayatQuery = PanicButton::with(['warga', 'user', 'penangan'])->latest();
        if (!$isPengurus) {
            $riwayatQuery->where(function ($q) use ($user, $warga) {
                $q->where('user_id', $user->id);
                if ($warga) {
                    $q->orWhere('warga_id', $warga->id);
                }
            });
        }
        $riwayats = $riwayatQuery->limit(25)->get();

        $kontakDarurat = KontakDarurat::orderBy('urutan')->limit(4)->get();

        return view('panic_button.index', compact('activeAlerts', 'riwayats', 'warga', 'isPengurus', 'kontakDarurat'));
    }

    public function trigger(Request $request)
    {
        $request->validate([
            'jenis'      => 'required|in:kebakaran,kemalingan,medis,kecelakaan,lainnya',
            'lokasi'     => 'nullable|string|max:200',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();

        $lokasiDefault = $request->lokasi ?: ($warga?->alamat ?? 'Lingkungan RT');

        $sos = PanicButton::create([
            'user_id'    => $user->id,
            'warga_id'   => $warga?->id,
            'jenis'      => $request->jenis,
            'lokasi'     => $lokasiDefault,
            'keterangan' => $request->keterangan ?: 'Sinyal darurat SOS dipicu oleh ' . $user->name,
            'status'     => 'aktif',
        ]);

        return redirect()->route('panic.index')
            ->with('success', '🚨 SINYAL DARURAT SOS TERKIRIM! Pengurus RT dan keamanan telah menerima peringatan Anda.');
    }

    public function tangani(PanicButton $panic)
    {
        $panic->update([
            'status'         => 'ditangani',
            'ditangani_at'   => now(),
            'ditangani_oleh' => auth()->id(),
        ]);

        return redirect()->route('panic.index')->with('success', 'Status darurat ditandai: Sedang Ditangani.');
    }

    public function selesai(PanicButton $panic)
    {
        $panic->update([
            'status'         => 'selesai',
            'ditangani_at'   => $panic->ditangani_at ?? now(),
            'ditangani_oleh' => $panic->ditangani_oleh ?? auth()->id(),
        ]);

        return redirect()->route('panic.index')->with('success', 'Status darurat ditandai: Selesai / Kondisi Aman.');
    }
}
