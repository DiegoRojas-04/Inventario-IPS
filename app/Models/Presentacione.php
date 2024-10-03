<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacione extends Model
{
    use HasFactory;
    protected $table = 'presentaciones'; // Asegúrate de que el nombre aquí coincida con tu tabla
    protected $fillable = ['nombre', 'descripcion', 'estado'];
   
    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'id_presentacion');
    }

   
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    // Mutador para el campo 'descripcion' a mayúsculas
    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }
}
