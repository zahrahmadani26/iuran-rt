<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Warga::with('user')->orderBy('id', 'desc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('nik', 'like', "%{$q}%")
                    ->orWhere('no_kk', 'like', "%{$q}%")
                    ->orWhere('alamat', 'like', "%{$q}%")
                    ->orWhere('no_telp', 'like', "%{$q}%")
                    ->orWhere('rfid_uid', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status') && in_array($request->status, ['aktif', 'nonaktif'])) {
            $query->where('status', $request->status);
        }

        $wargas = $query->paginate(25)->withQueryString();
        $totalSemuaWarga = Warga::count();

        return view('warga.index', compact('wargas', 'totalSemuaWarga'));
    }
    
    public function create()
    {
        return view('warga.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'nik' => 'required|unique:wargas',
            'no_kk' => 'required|unique:wargas',
            'alamat' => 'required',
            'no_telp' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'rfid_uid' => 'nullable|string|unique:wargas,rfid_uid'
        ]);
        
        $user = User::create([
            'name' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga'
        ]);
        
        Warga::create([
            'user_id' => $user->id,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'nama_lengkap' => $request->nama_lengkap,
            'alamat' => $request->alamat,
            'no_telp' => $request->no_telp,
            'status' => $request->status ?? 'aktif',
            'rfid_uid' => $request->rfid_uid ? strtoupper(trim($request->rfid_uid)) : null,
        ]);
        
        return redirect()->route('warga.index')->with('success', 'Warga berhasil ditambahkan');
    }
    
    public function edit(Warga $warga)
    {
        return view('warga.edit', compact('warga'));
    }
    
    public function update(Request $request, Warga $warga)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'alamat' => 'required',
            'no_telp' => 'required',
            'rfid_uid' => 'nullable|string|unique:wargas,rfid_uid,' . $warga->id
        ]);
        
        $data = $request->only(['nama_lengkap', 'alamat', 'no_telp', 'status']);
        $data['rfid_uid'] = $request->rfid_uid ? strtoupper(trim($request->rfid_uid)) : null;
        
        $warga->update($data);
        $warga->user->update(['name' => $request->nama_lengkap]);
        
        return redirect()->route('warga.index')->with('success', 'Warga berhasil diupdate');
    }
    
    public function destroy(Warga $warga)
    {
        $warga->user->delete();
        $warga->delete();
        return redirect()->route('warga.index')->with('success', 'Warga berhasil dihapus');
    }
}