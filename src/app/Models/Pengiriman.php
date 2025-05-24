<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengiriman extends Model
{
    use HasFactory;

    protected $table = 'pengirimen';

    protected $fillable = [
        'pesanan_id',
        'pengirim_id',
        'alamat',
        'jarak',
        'total',
        'status',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function pengirim()
    {
        return $this->belongsTo(Pengirim::class);
    }
}
