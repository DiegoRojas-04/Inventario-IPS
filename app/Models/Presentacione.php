<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacione extends Model
{
    use HasFactory;
    protected $table = 'presentaciones'; // Asegúrate de que el nombre aquí coincida con tu tabla
    protected $fillable = ['nombre', 'estado'];
   
    public function insumos()
    {
        return $this->hasMany(Insumo::class, 'id_presentacion');
    }
}
