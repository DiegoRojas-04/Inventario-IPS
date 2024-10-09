<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompraInsumo extends Model
{
    use HasFactory;

    protected $table = 'compra_insumo'; // Asegúrate de que el nombre de la tabla esté correcto

    protected $fillable = [
        'compra_id',
        'insumo_id',
        'cantidad',
    ];

    // Relación con Compra
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id', 'id');
    }

    // Relación con Insumo
    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id', 'id');
    }
}
