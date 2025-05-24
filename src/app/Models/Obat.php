<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Obat extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_id',
        'golongan_id',
        'kode_obat',
        'nama_obat',
        'komposisi',
        'dosis',
        'aturan_pakai',
        'nomor_izin_edaar',
        'tanggal_kadaluarsa',
        'harga',
        'stok',
        'status_label',
        'status',
        'image',
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    public function golongan()
    {
        return $this->belongsTo(Golongan::class);
    }

    public function pesananItems()
    {
        return $this->hasMany(PesananItem::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($obat) {
            // Hanya isi kode_obat jika belum diset
            if (empty($obat->kode_obat)) {
                $lastId = self::max('id') ?? 0;
                $nextId = $lastId + 1;
                $obat->kode_obat = 'OBT-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
