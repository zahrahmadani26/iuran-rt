<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPengantar extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'warga_id', 'no_surat', 'jenis_surat', 'keperluan',
        'keterangan', 'status', 'catatan_admin', 'diproses_oleh', 'tanggal_disetujui',
    ];

    protected $casts = [
        'tanggal_disetujui' => 'datetime',
    ];

    public const JENIS_LABELS = [
        'domisili'               => 'Surat Keterangan Domisili',
        'ktp'                    => 'Surat Pengantar Pembuatan KTP',
        'skck'                   => 'Surat Pengantar SKCK',
        'keterangan_usaha'       => 'Surat Keterangan Usaha',
        'keterangan_tidak_mampu' => 'Surat Keterangan Tidak Mampu (SKTM)',
        'keterangan_menikah'     => 'Surat Pengantar Nikah',
        'lainnya'                => 'Surat Keterangan Lainnya',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public function getJenisLabelAttribute()
    {
        return self::JENIS_LABELS[$this->jenis_surat] ?? ucfirst(str_replace('_', ' ', $this->jenis_surat));
    }
}
