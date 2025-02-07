<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaActivo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }
    
    // Relación con los activos
    public function activos()
    {
        return $this->hasMany(Activo::class, 'categoria_id'); // La clave correcta es 'categoria_id'
    }
}
