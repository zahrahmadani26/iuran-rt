<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengumuman::with('user')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');

        if ($request->filled('kategori') && in_array($request->kategori, ['umum', 'penting', 'darurat'])) {
            $query->where('kategori', $request->kategori);
        }

        $pengumumans = $query->get();

        return view('pengumuman.index', compact('pengumumans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:180',
            'konten' => 'required|string',
            'kategori' => 'required|in:umum,penting,darurat',
        ]);

        Pengumuman::create([
            'user_id' => auth()->id(),
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kategori' => $request->kategori,
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dipublikasikan!');
    }

    public function update(Request $request, Pengumuman $pengumuman)
    {
        $request->validate([
            'judul' => 'required|string|max:180',
            'konten' => 'required|string',
            'kategori' => 'required|in:umum,penting,darurat',
        ]);

        $pengumuman->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kategori' => $request->kategori,
            'is_pinned' => $request->boolean('is_pinned'),
        ]);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function togglePin(Pengumuman $pengumuman)
    {
        $pengumuman->update(['is_pinned' => !$pengumuman->is_pinned]);
        $msg = $pengumuman->is_pinned ? 'Pengumuman disematkan (pinned).' : 'Sematkan pengumuman dilepas.';
        return redirect()->route('pengumuman.index')->with('success', $msg);
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
