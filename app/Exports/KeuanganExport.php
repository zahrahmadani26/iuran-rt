<?php

namespace App\Exports;

use App\Models\Keuangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class KeuanganExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Keuangan::all();
    }
    
    public function headings(): array
    {
        return ['ID', 'Jenis', 'Kategori', 'Deskripsi', 'Nominal', 'Tanggal'];
    }
    
    public function map($keuangan): array
    {
        return [
            $keuangan->id,
            $keuangan->jenis,
            $keuangan->kategori,
            $keuangan->deskripsi,
            $keuangan->nominal,
            $keuangan->tanggal,
        ];
    }
}