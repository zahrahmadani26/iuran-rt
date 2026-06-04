<?php

namespace App\Http\Controllers;

use App\Models\Keuangan;
use App\Exports\KeuanganExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $keuangans = Keuangan::orderBy('tanggal', 'desc')->get();
        
        $totalPemasukan = Keuangan::where('jenis', 'pemasukan')->sum('nominal') ?? 0;
        $totalPengeluaran = Keuangan::where('jenis', 'pengeluaran')->sum('nominal') ?? 0;
        $saldoKas = $totalPemasukan - $totalPengeluaran;
        
        return view('keuangan.index', compact('keuangans', 'totalPemasukan', 'totalPengeluaran', 'saldoKas'));
    }
    
    public function create()
    {
        $totalPemasukan = Keuangan::where('jenis', 'pemasukan')->sum('nominal') ?? 0;
        $totalPengeluaran = Keuangan::where('jenis', 'pengeluaran')->sum('nominal') ?? 0;
        $saldoKas = $totalPemasukan - $totalPengeluaran;
        
        return view('keuangan.create', compact('saldoKas'));
    }
    
    public function createPengeluaran()
    {
        $totalPemasukan = Keuangan::where('jenis', 'pemasukan')->sum('nominal') ?? 0;
        $totalPengeluaran = Keuangan::where('jenis', 'pengeluaran')->sum('nominal') ?? 0;
        $saldoKas = $totalPemasukan - $totalPengeluaran;
        
        return view('keuangan.create', [
            'saldoKas' => $saldoKas,
            'jenis' => 'pengeluaran'
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string',
            'deskripsi' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);
        
        $data = $request->all();
        
        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('bukti_keuangan', 'public');
        }
        
        Keuangan::create($data);
        
        $message = $request->jenis == 'pemasukan' ? 'Pemasukan berhasil ditambahkan' : 'Pengeluaran berhasil ditambahkan';
        return redirect()->route('keuangan.index')->with('success', $message);
    }
    
    public function edit(Keuangan $keuangan)
    {
        return view('keuangan.edit', compact('keuangan'));
    }
    
    public function update(Request $request, Keuangan $keuangan)
    {
        $request->validate([
            'jenis' => 'required|in:pemasukan,pengeluaran',
            'kategori' => 'required|string',
            'deskripsi' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'tanggal' => 'required|date'
        ]);
        
        $keuangan->update($request->all());
        
        $message = $request->jenis == 'pemasukan' ? 'Pemasukan berhasil diupdate' : 'Pengeluaran berhasil diupdate';
        return redirect()->route('keuangan.index')->with('success', $message);
    }
    
    public function destroy(Keuangan $keuangan)
    {
        if ($keuangan->bukti && file_exists(storage_path('app/public/' . $keuangan->bukti))) {
            unlink(storage_path('app/public/' . $keuangan->bukti));
        }
        
        $keuangan->delete();
        return redirect()->route('keuangan.index')->with('success', 'Transaksi berhasil dihapus');
    }
    
    public function exportExcel()
    {
        return Excel::download(new KeuanganExport, 'laporan-keuangan-' . date('Y-m-d') . '.xlsx');
    }
}