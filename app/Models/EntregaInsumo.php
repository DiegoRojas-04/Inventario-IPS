<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaInsumo extends Model
{
    use HasFactory;

    
    public $timestamps = true; 
    protected $table = 'entrega_insumo';

    protected $fillable = [
        'entrega_id',
        'insumo_id',
        'cantidad',
        'id_marca',
        'id_presentacion',
        'invima',
        'lote',
        'vencimiento',
        'created_at',

    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacione::class, 'id_presentacion');
    }

    public function entregas()
    {
        return $this->hasMany(Entrega::class, 'id', 'entrega_id'); // Aquí la clave foránea es 'compra_id' de la tabla insumo_caracteristica
    }
    
    public function compraInsumos()
    {
        return $this->hasMany(CompraInsumo::class, 'entrega_id', 'id');
    }
}
