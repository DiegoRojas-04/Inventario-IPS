<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaActivo extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    // Relación con los activos
    public function activos()
    {
        return $this->hasMany(Activo::class);
    }
}
