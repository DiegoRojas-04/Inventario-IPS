<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_hora',
        'user_id'
    ];

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)->withPivot('cantidad')->withTimestamps();
    }
    

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
