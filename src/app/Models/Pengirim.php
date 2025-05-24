<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengirim extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'nomor_telepon',
        'email',
        'jenis_kelamin',
        'jenis_kendaraan',
    ];

    public function pengirimans()
    {
        return $this->hasMany(Pengiriman::class);
    }
}
