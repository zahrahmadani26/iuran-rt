<?php

namespace App\Http\Controllers;

use App\Models\SuratPengantar;
use App\Models\Warga;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SuratPengantarController extends Controller
{
    private function isPengurus(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();

        $query = SuratPengantar::with(['warga', 'user', 'processor'])->latest();

        if (!$this->isPengurus()) {
            $query->where(function ($q) use ($user, $warga) {
                $q->where('user_id', $user->id);
                if ($warga) {
                    $q->orWhere('warga_id', $warga->id);
                }
            });
        } elseif ($request->filled('status') && in_array($request->status, ['pending', 'diproses', 'disetujui', 'ditolak'])) {
            $query->where('status', $request->status);
        }

        $surats = $query->get();
        $wargas = $this->isPengurus() ? Warga::orderBy('nama_lengkap')->get() : collect();
        $jenisList = SuratPengantar::JENIS_LABELS;

        return view('surat.index', compact('surats', 'warga', 'wargas', 'jenisList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_surat' => 'required|in:domisili,ktp,skck,keterangan_usaha,keterangan_tidak_mampu,keterangan_menikah,lainnya',
            'keperluan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'warga_id' => 'nullable|exists:wargas,id',
        ]);

        $user = auth()->user();
        $wargaId = null;

        if ($this->isPengurus() && $request->filled('warga_id')) {
            $wargaId = $request->warga_id;
        } else {
            $warga = Warga::where('user_id', $user->id)->first();
            $wargaId = $warga?->id;
        }

        SuratPengantar::create([
            'user_id' => $user->id,
            'warga_id' => $wargaId,
            'jenis_surat' => $request->jenis_surat,
            'keperluan' => $request->keperluan,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        return redirect()->route('surat.index')->with('success', 'Pengajuan surat pengantar berhasil dikirim! Menunggu persetujuan Pengurus RT.');
    }

    public function approve(Request $request, SuratPengantar $surat)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        // Generate nomor surat otomatis
        $tahun = date('Y');
        $bulanRomawi = ['01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI','07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII'][date('m')] ?? 'I';
        $urutan = SuratPengantar::where('status', 'disetujui')
            ->whereRaw("strftime('%Y', created_at) = ?", [$tahun])
            ->count() + 1;

        $noSurat = $surat->no_surat ?: sprintf('%03d/RT-01/%s/%s', $urutan, $bulanRomawi, $tahun);

        $surat->update([
            'no_surat' => $noSurat,
            'status' => 'disetujui',
            'catatan_admin' => $request->catatan_admin,
            'diproses_oleh' => auth()->id(),
            'tanggal_disetujui' => now(),
        ]);

        return redirect()->route('surat.index')->with('success', "Surat pengantar {$noSurat} disetujui dan siap diunduh PDF!");
    }

    public function process(SuratPengantar $surat)
    {
        $surat->update([
            'status' => 'diproses',
            'diproses_oleh' => auth()->id(),
        ]);

        return redirect()->route('surat.index')->with('success', 'Status surat diubah menjadi Diproses.');
    }

    public function reject(Request $request, SuratPengantar $surat)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Harap isi alasan penolakan.',
        ]);

        $surat->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
            'diproses_oleh' => auth()->id(),
        ]);

        return redirect()->route('surat.index')->with('error', 'Pengajuan surat pengantar telah ditolak.');
    }

    public function downloadPdf(SuratPengantar $surat)
    {
        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();

        // Cek hak akses: hanya pemilik atau pengurus RT
        if (!$this->isPengurus() && $surat->user_id !== $user->id && $surat->warga_id !== ($warga?->id)) {
            abort(403, 'Anda tidak berhak mengunduh surat ini.');
        }

        if ($surat->status !== 'disetujui') {
            return redirect()->route('surat.index')->with('error', 'Surat hanya dapat diunduh setelah disetujui oleh Pengurus RT.');
        }

        $surat->load(['warga', 'user', 'processor']);

        $pdf = Pdf::loadView('surat.pdf', compact('surat'))->setPaper('a4', 'portrait');
        $safeNo = preg_replace('/[^A-Za-z0-9\-]/', '_', $surat->no_surat ?? 'surat_' . $surat->id);

        return $pdf->download("Surat_Pengantar_{$safeNo}.pdf");
    }

    public function destroy(SuratPengantar $surat)
    {
        $user = auth()->user();
        if (!$this->isPengurus() && ($surat->user_id !== $user->id || $surat->status !== 'pending')) {
            abort(403, 'Tidak diizinkan menghapus pengajuan ini.');
        }

        $surat->delete();
        return redirect()->route('surat.index')->with('success', 'Pengajuan surat berhasil dihapus.');
    }
}
