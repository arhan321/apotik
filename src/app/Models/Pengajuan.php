<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'profile_id',
        'nomor_pengajuan',
        'catatan',
        'tanggal',
        'alamat',
        'jarak',
        'total',
        'status',
        'image',
    ];

    /**
     * Relasi ke Profile (setiap pengajuan dibuat oleh satu profile).
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }



    /**
     * Relasi ke Pesanan (jika ada pesanan yang dibuat dari pengajuan ini).
     */
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pengajuan) {
            if (empty($pengajuan->nomor_pengajuan)) {
                $lastId = self::max('id') ?? 0;
                $nextId = $lastId + 1;
                $pengajuan->nomor_pengajuan = 'PNG-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
