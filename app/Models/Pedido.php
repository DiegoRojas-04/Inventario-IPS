<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_hora',
        'user_id',
        'estado',
        'tipo',
        'observacion', 
    ];

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)->withPivot('cantidad', 'restante');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
