<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elemento extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'cantidad_necesaria', 'descripcion', 'categoria', 'estado']; 

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    // Mutator para convertir la descripción a mayúsculas
    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }
    
    public function consultorios()
    {
        return $this->belongsToMany(Consultorio::class, 'consultorio_elemento')
            ->withPivot('cantidad', 'observacion');
    }

    
}
