<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Jenis extends Model
{
    use HasFactory;

    protected $table = 'jenis'; 

    protected $fillable = [
        'name',
    ];

    /**
     * Relasi ke Obat (satu jenis memiliki banyak obat).
     */
    public function obats()
    {
        return $this->hasMany(Obat::class);
    }
}
