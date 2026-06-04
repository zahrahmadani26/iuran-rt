<?php

namespace App\Http\Controllers;

use App\Models\Iuran;
use Illuminate\Http\Request;

class IuranController extends Controller
{
    public function index()
    {
        $iurans = Iuran::all();
        return view('iuran.index', compact('iurans'));
    }
    
    public function create()
    {
        return view('iuran.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_iuran' => 'required',
            'nominal' => 'required|numeric',
            'periode' => 'required',
            'tanggal_jatuh_tempo' => 'required|date'
        ]);
        
        Iuran::create($request->all());
        return redirect()->route('iuran.index')->with('success', 'Iuran berhasil ditambahkan');
    }
    
    public function edit(Iuran $iuran)
    {
        return view('iuran.edit', compact('iuran'));
    }
    
    public function update(Request $request, Iuran $iuran)
    {
        $request->validate([
            'nama_iuran' => 'required',
            'nominal' => 'required|numeric',
            'periode' => 'required',
            'tanggal_jatuh_tempo' => 'required|date'
        ]);
        
        $iuran->update($request->all());
        return redirect()->route('iuran.index')->with('success', 'Iuran berhasil diupdate');
    }
    
    public function destroy(Iuran $iuran)
    {
        $iuran->delete();
        return redirect()->route('iuran.index')->with('success', 'Iuran berhasil dihapus');
    }
}