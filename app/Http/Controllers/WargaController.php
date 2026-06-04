<?php

namespace App\Http\Controllers;

use App\Models\Warga;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WargaController extends Controller
{
    public function index()
    {
        $wargas = Warga::with('user')->get();
        return view('warga.index', compact('wargas'));
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
            'password' => 'required|min:6'
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
            'no_telp' => $request->no_telp
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
            'no_telp' => 'required'
        ]);
        
        $warga->update($request->only(['nama_lengkap', 'alamat', 'no_telp', 'status']));
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