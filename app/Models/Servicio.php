<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function entregas()
    {
        return $this->hasMany(Entrega::class);
    }

    public function ultimaEntregaDeInsumo($insumoId)
    {
        return $this->entregas()
            ->whereHas('insumos', function ($query) use ($insumoId) {
                $query->where('insumo_id', $insumoId);
            })
            ->latest('fecha_hora')
            ->first();
    }
}

