<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoCaracteristica extends Model
{
    use HasFactory;

    protected $fillable = [
        'insumo_id',
        'compra_id', // Asegúrate de tener esta clave foránea
        'invima',
        'lote',
        'vencimiento',
        'cantidad',
        'cantidad_compra',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }
}
