<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_hora',
        'numero_comprobante',
        'estado',
        'servicio_id',
        'user_id',
        'comprobante_id',

    ];


    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comprobante()
    {
        return $this->belongsTo(Comprobante::class);
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

    public function insumos()
    {
        return $this->belongsToMany(Insumo::class)
            ->withPivot('cantidad', 'invima', 'lote', 'vencimiento', 'id_marca', 'id_presentacion')
            ->withTimestamps()
            ->with(['marca', 'presentacion']); // Cargar las relaciones
    }


    public static function generarNumeroComprobante()
    {
        // Obtén el máximo ID actual de la tabla
        $ultimoId = self::max('id');

        // Si no hay registros, comienza con el ID 1
        $numero = $ultimoId ? $ultimoId + 1 : 1;

        return $numero;
    }
    public function insumoEntregas()
    {
        return $this->hasMany(EntregaInsumo::class); // Cambia esto al nombre correcto de tu modelo
    }

    public function marca()
{
    return $this->belongsTo(Marca::class, 'id_marca');
}

public function presentacion()
{
    return $this->belongsTo(Presentacione::class, 'id_presentacion');
}
    
}
