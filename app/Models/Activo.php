<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'categoria_id',
        'modelo',
        'serie',
        'marca',
        'cantidad',
        'medida',
        'estado',
        'observacion',
        'condicion',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaActivo::class);
    }
}
