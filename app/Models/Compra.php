<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\FuncCall;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_hora',
        'numero_comprobante',
        'estado',
        'proveedor_id',
        'user_id',
        'comprobante_id',
        'valor_total',
        'created_at',

    ];
    public function proveedor()
    {
        return $this->belongsTo(Proveedore::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
    }

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)->withTimestamps()->withPivot('cantidad');
    }

    public function insumoCaracteristicas()
    {
        return $this->hasManyThrough(
            InsumoCaracteristica::class,
            Insumo::class,
            'id',
            'insumo_id'
        )->whereIn('insumo_id', $this->insumos->pluck('id'));
    }

    public static function generarNumeroComprobante()
    {
        // Obtén el máximo ID actual de la tabla
        $ultimoId = self::max('id');
        
        // Si no hay registros, comienza con el ID 1
        $numero = $ultimoId ? $ultimoId + 1 : 1;
        
        return $numero;
    }

    public function compraInsumos()
    {
        return $this->hasMany(CompraInsumo::class, 'compra_id', 'id');
    }
    
}
