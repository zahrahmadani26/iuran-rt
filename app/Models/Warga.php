<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'nik', 'no_kk', 'nama_lengkap', 'alamat', 'no_telp', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}