<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use Illuminate\Http\Request;

class JadwalKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $query = JadwalKegiatan::with('user')->orderBy('tanggal', 'asc')->orderBy('jam_mulai', 'asc');

        if ($request->filled('kategori') && in_array($request->kategori, ['rapat', 'gotong_royong', 'posyandu', 'arisan', 'olahraga', 'lainnya'])) {
            $query->where('kategori', $request->kategori);
        }

        $semuaJadwal = $query->get();
        $today = now()->toDateString();

        $jadwalMendatang = $semuaJadwal->filter(fn ($j) => $j->tanggal->toDateString() >= $today);
        $jadwalSelesai   = $semuaJadwal->filter(fn ($j) => $j->tanggal->toDateString() < $today)->reverse();

        return view('jadwal.index', compact('jadwalMendatang', 'jadwalSelesai'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'       => 'required|string|max:180',
            'deskripsi'   => 'nullable|string',
            'kategori'    => 'required|in:rapat,gotong_royong,posyandu,arisan,olahraga,lainnya',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'nullable',
            'lokasi'      => 'nullable|string|max:180',
        ]);

        JadwalKegiatan::create([
            'user_id'     => auth()->id(),
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi,
            'kategori'    => $request->kategori,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'lokasi'      => $request->lokasi,
        ]);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal kegiatan berhasil ditambahkan!');
    }

    public function update(Request $request, JadwalKegiatan $jadwal)
    {
        $request->validate([
            'judul'       => 'required|string|max:180',
            'deskripsi'   => 'nullable|string',
            'kategori'    => 'required|in:rapat,gotong_royong,posyandu,arisan,olahraga,lainnya',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'nullable',
            'lokasi'      => 'nullable|string|max:180',
        ]);

        $jadwal->update($request->only([
            'judul', 'deskripsi', 'kategori', 'tanggal', 'jam_mulai', 'jam_selesai', 'lokasi',
        ]));

        return redirect()->route('jadwal.index')->with('success', 'Jadwal kegiatan berhasil diperbarui.');
    }

    public function destroy(JadwalKegiatan $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('jadwal.index')->with('success', 'Jadwal kegiatan berhasil dihapus.');
    }
}
