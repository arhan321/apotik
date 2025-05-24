<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Golongan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function obats()
    {
        return $this->hasMany(Obat::class);
    }
}
