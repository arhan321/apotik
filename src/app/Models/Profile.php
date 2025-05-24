<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'nomor_telepon',
        'jenis_kelamin',
        'tanggal_lahir',
        'image',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pengajuans()
    {
        return $this->hasMany(Pengajuan::class);
    }
    
    public function pesanans()
{
    return $this->hasMany(Pesanan::class);
}
}
