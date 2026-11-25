<?php

namespace App\Http\Controllers;

use App\Models\KartuKeluarga;
use App\Models\Warga;
use Illuminate\Http\Request;

class KartuKeluargaController extends Controller
{
    private function isPengurus(): bool
    {
        return in_array(auth()->user()->role, ['super_admin', 'admin', 'bendahara']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isPengurus = $this->isPengurus();

        if ($isPengurus) {
            $query = Warga::with('kartuKeluargas')->orderBy('nama_lengkap');
            if ($request->filled('q')) {
                $q = $request->q;
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama_lengkap', 'like', "%{$q}%")
                        ->orWhere('no_kk', 'like', "%{$q}%")
                        ->orWhere('nik', 'like', "%{$q}%");
                });
            }
            $wargas = $query->get();
            $totalAnggota = KartuKeluarga::count();
        } else {
            $wargas = Warga::with('kartuKeluargas')->where('user_id', $user->id)->get();
            $wargaIds = $wargas->pluck('id');
            $totalAnggota = KartuKeluarga::whereIn('warga_id', $wargaIds)->count();
        }

        return view('kartu_keluarga.index', compact('wargas', 'isPengurus', 'totalAnggota'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warga_id'      => 'required|exists:wargas,id',
            'nama_lengkap'  => 'required|string|max:120',
            'nik'           => 'nullable|string|max:20',
            'hubungan'      => 'required|in:kepala_keluarga,istri,anak,orang_tua,mertua,cucu,lainnya',
            'jenis_kelamin' => 'required|in:laki_laki,perempuan',
            'tempat_lahir'  => 'nullable|string|max:80',
            'tanggal_lahir' => 'nullable|date',
            'status_kawin'  => 'required|in:belum_kawin,kawin,cerai_hidup,cerai_mati',
            'pekerjaan'     => 'nullable|string|max:100',
        ]);

        $warga = Warga::findOrFail($request->warga_id);

        if (!$this->isPengurus() && $warga->user_id !== auth()->id()) {
            abort(403, 'Anda hanya dapat mengelola Kartu Keluarga Anda sendiri.');
        }

        KartuKeluarga::create([
            'warga_id'      => $warga->id,
            'no_kk'         => $warga->no_kk,
            'nama_lengkap'  => $request->nama_lengkap,
            'nik'           => $request->nik,
            'hubungan'      => $request->hubungan,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'status_kawin'  => $request->status_kawin,
            'pekerjaan'     => $request->pekerjaan,
        ]);

        return redirect()->route('kartu_keluarga.index')->with('success', "Anggota keluarga \"{$request->nama_lengkap}\" berhasil ditambahkan!");
    }

    public function update(Request $request, KartuKeluarga $kartuKeluarga)
    {
        if (!$this->isPengurus() && $kartuKeluarga->warga->user_id !== auth()->id()) {
            abort(403, 'Tidak diizinkan mengubah data ini.');
        }

        $request->validate([
            'nama_lengkap'  => 'required|string|max:120',
            'nik'           => 'nullable|string|max:20',
            'hubungan'      => 'required|in:kepala_keluarga,istri,anak,orang_tua,mertua,cucu,lainnya',
            'jenis_kelamin' => 'required|in:laki_laki,perempuan',
            'tempat_lahir'  => 'nullable|string|max:80',
            'tanggal_lahir' => 'nullable|date',
            'status_kawin'  => 'required|in:belum_kawin,kawin,cerai_hidup,cerai_mati',
            'pekerjaan'     => 'nullable|string|max:100',
        ]);

        $kartuKeluarga->update($request->only([
            'nama_lengkap', 'nik', 'hubungan', 'jenis_kelamin',
            'tempat_lahir', 'tanggal_lahir', 'status_kawin', 'pekerjaan',
        ]));

        return redirect()->route('kartu_keluarga.index')->with('success', 'Data anggota keluarga berhasil diperbarui.');
    }

    public function destroy(KartuKeluarga $kartuKeluarga)
    {
        if (!$this->isPengurus() && $kartuKeluarga->warga->user_id !== auth()->id()) {
            abort(403, 'Tidak diizinkan menghapus data ini.');
        }

        $kartuKeluarga->delete();
        return redirect()->route('kartu_keluarga.index')->with('success', 'Anggota keluarga berhasil dihapus.');
    }
}
