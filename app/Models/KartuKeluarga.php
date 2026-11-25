<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuKeluarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'warga_id', 'no_kk', 'nama_lengkap', 'nik', 'hubungan',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'status_kawin', 'pekerjaan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
