<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'nomor_pesanan',
        'tanggal',
        'total',
        'status',
        'pengajuan_id',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function items()
{
    return $this->hasMany(PesananItem::class);
}

public function pengiriman()
{
    return $this->hasOne(Pengiriman::class);
}

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            // Nomor pesanan otomatis
            if (empty($pesanan->nomor_pesanan)) {
                $lastId = self::max('id') ?? 0;
                $nextId = $lastId + 1;
                $pesanan->nomor_pesanan = 'PSN-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    protected $with = ['items'];
}
