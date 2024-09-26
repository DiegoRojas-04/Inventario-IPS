<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultorio extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'estado'];
    public function elementos()
    {
        return $this->belongsToMany(Elemento::class, 'consultorio_elemento')
                    ->withPivot('cantidad', 'estado','observacion');
    }
    
    
}