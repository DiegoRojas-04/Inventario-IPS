<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsumoCaracteristica extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'insumo_id',
        'compra_id', // Asegúrate de tener esta clave foránea
        'invima',
        'lote',
        'vencimiento',
        'cantidad',
        'cantidad_compra',
        'id_marca',
        'id_presentacion',
        'valor_unitario',
        'created_at',
        'updated_at',
    ];

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacione::class, 'id_presentacion');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id', 'compra_id'); // Aquí la clave foránea es 'compra_id' de la tabla insumo_caracteristica
    }   
}
