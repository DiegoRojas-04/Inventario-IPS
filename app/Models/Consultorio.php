<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultorio extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    public function setDescripcionAttribute($value)
    {
        $this->attributes['descripcion'] = strtoupper($value);
    }

    public function elementos()
    {
        return $this->belongsToMany(Elemento::class, 'consultorio_elemento')
                    ->withPivot('cantidad','observacion');
    }
    
    
}