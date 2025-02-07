<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'ubicacion_general',
        'categoria_id',
        'modelo',
        'serie',
        'marca',
        'cantidad',
        'medida',
        'estado',
        'ubicacion_id',
        'observacion',
        'condicion',
    ];


public function categoria()
{
    return $this->belongsTo(CategoriaActivo::class, 'categoria_id'); // 'categoria_id' es la clave foránea
}


    public function ubicacion()
{
    return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
}

}

