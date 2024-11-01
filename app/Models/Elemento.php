<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elemento extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'cantidad_necesaria', 'descripcion', 'categoria', 'estado']; 

    public function consultorios()
    {
        return $this->belongsToMany(Consultorio::class, 'consultorio_elemento')
            ->withPivot('cantidad', 'observacion');
    }

    
}
