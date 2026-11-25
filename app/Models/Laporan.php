<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'warga_id', 'judul', 'deskripsi', 'kategori',
        'lokasi', 'foto', 'status', 'tanggapan_admin',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
