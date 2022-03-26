<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colección extends Model
{
    use HasFactory;
    public function cartas()
    {
        return $this->belongsToMany(Carta::class);
    }
}
