<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTransaccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaccion_id',
        'insumo_id',
        'cantidad',
        'tipo', // Por ejemplo: 'entrada' o 'salida'
        'fecha',
        // Otros campos que puedan ser relevantes
    ];

    // Relación con el insumo
    public function insumo()
    {
        return $this->belongsTo(Insumo::class);
    }
}
