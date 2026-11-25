<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PanicButton extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'warga_id', 'jenis', 'keterangan', 'lokasi',
        'status', 'ditangani_at', 'ditangani_oleh',
    ];

    protected $casts = [
        'ditangani_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function penangan()
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }
}
