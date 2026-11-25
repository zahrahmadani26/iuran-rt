<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'judul', 'deskripsi', 'kategori',
        'tanggal', 'jam_mulai', 'jam_selesai', 'lokasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
