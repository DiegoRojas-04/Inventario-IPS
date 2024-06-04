<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caracteristica extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'descripcion',
        'invima',
        'lote',
        'vencimiento',
        'cantidad',
    ];

    public function categoria(){
        return $this->hasOne(Categoria::class);
    }

    public function marca(){
        return $this->hasOne(Marca::class);
    }

    public function presentacione(){
        return $this->hasOne(Presentacione::class);
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)->withPivot('invima', 'lote', 'vencimiento', 'cantidad');
    }
   
}
