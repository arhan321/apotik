<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PesananItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'obat_id',
        'pesanan_id',
        'qty',
        'total',
    ];

    public function obat()
{
    return $this->belongsTo(Obat::class);
}

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
