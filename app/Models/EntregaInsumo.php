<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaInsumo extends Model
{
    use HasFactory;

    protected $table = 'entrega_insumo';

    protected $fillable = [
        'entrega_id',
        'insumo_id',
        'cantidad',
    ];

    public function entrega()
    {
        return $this->belongsTo(Entrega::class, 'entrega_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'insumo_id');
    }
}
