<?php

namespace App\Http\Controllers;

use App\Models\KontakDarurat;
use Illuminate\Http\Request;

class KontakDaruratController extends Controller
{
    public function index()
    {
        $kontaks = KontakDarurat::orderBy('urutan')->orderBy('id')->get();
        return view('kontak_darurat.index', compact('kontaks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required|string|max:120',
            'nomor_telepon' => 'required|string|max:30',
            'kategori'      => 'required|string|max:60',
            'keterangan'    => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:60',
            'urutan'        => 'nullable|integer|min:0',
        ]);

        KontakDarurat::create([
            'nama'          => $request->nama,
            'nomor_telepon' => $request->nomor_telepon,
            'kategori'      => $request->kategori,
            'keterangan'    => $request->keterangan,
            'icon'          => $request->icon ?: 'fas fa-phone',
            'urutan'        => $request->urutan ?? 10,
        ]);

        return redirect()->route('kontak_darurat.index')->with('success', 'Kontak darurat berhasil ditambahkan!');
    }

    public function update(Request $request, KontakDarurat $kontakDarurat)
    {
        $request->validate([
            'nama'          => 'required|string|max:120',
            'nomor_telepon' => 'required|string|max:30',
            'kategori'      => 'required|string|max:60',
            'keterangan'    => 'nullable|string|max:255',
            'icon'          => 'nullable|string|max:60',
            'urutan'        => 'nullable|integer|min:0',
        ]);

        $kontakDarurat->update([
            'nama'          => $request->nama,
            'nomor_telepon' => $request->nomor_telepon,
            'kategori'      => $request->kategori,
            'keterangan'    => $request->keterangan,
            'icon'          => $request->icon ?: 'fas fa-phone',
            'urutan'        => $request->urutan ?? $kontakDarurat->urutan,
        ]);

        return redirect()->route('kontak_darurat.index')->with('success', 'Kontak darurat berhasil diperbarui.');
    }

    public function destroy(KontakDarurat $kontakDarurat)
    {
        $kontakDarurat->delete();
        return redirect()->route('kontak_darurat.index')->with('success', 'Kontak darurat berhasil dihapus.');
    }
}
