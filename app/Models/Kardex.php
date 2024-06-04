<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kardex extends Model
{
    use HasFactory;

    protected $fillable = [
        'insumo_id',
        'mes',
        'anno',
        'cantidad_inicial',
        'ingresos',
        'egresos',
        'saldo',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
