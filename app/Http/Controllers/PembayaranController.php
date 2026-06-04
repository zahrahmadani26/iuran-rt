<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Warga;
use App\Models\Iuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pembayarans = collect(); // default kosong
        
        try {
            if ($user->role == 'warga') {
                // Untuk warga: ambil data warga berdasarkan user_id
                $warga = Warga::where('user_id', $user->id)->first();
                
                if ($warga) {
                    $pembayarans = Pembayaran::where('warga_id', $warga->id)
                        ->with('iuran')
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            } else {
                // Untuk admin/bendahara: ambil semua pembayaran
                $pembayarans = Pembayaran::with(['warga', 'iuran'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            // Jika error, biarkan kosong
        }
        
        return view('pembayaran.index', compact('pembayarans'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $wargas = collect();
        $iurans = Iuran::all();
        
        if ($user->role == 'warga') {
            // Untuk warga, ambil data warga nya
            $warga = Warga::where('user_id', $user->id)->first();
            if ($warga) {
                $wargas = collect([$warga]);
            }
        } else {
            // Untuk admin/bendahara, ambil semua warga
            $wargas = Warga::all();
        }
        
        return view('pembayaran.create', compact('wargas', 'iurans'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'iuran_id' => 'required|exists:iurans,id',
            'jumlah_dibayar' => 'required|numeric|min:0',
            'tanggal_bayar' => 'required|date',
            'bukti_transfer' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);
        
        $data = $request->except('bukti_transfer');
        $data['status'] = 'pending';
        
        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('bukti_transfer', $filename, 'public');
            $data['bukti_transfer'] = $path;
        }
        
        Pembayaran::create($data);
        
        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil ditambahkan!');
    }
    
    public function verify($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update(['status' => 'verified']);
        
        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi');
    }
    
    public function reject($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->update(['status' => 'ditolak']);
        
        return redirect()->back()->with('success', 'Pembayaran ditolak');
    }
}