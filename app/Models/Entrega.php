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

   
    public function servicio(){ 
        return $this->belongsTo(Servicio::class,'servicio_id');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function comprobante(){
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
                    ->withPivot('cantidad', 'invima', 'lote', 'vencimiento')
                    ->withTimestamps();
    }

    public static function generarNumeroComprobante()
    {
        // Obtén el máximo ID actual de la tabla
        $ultimoId = self::max('id');
        
        // Si no hay registros, comienza con el ID 1
        $numero = $ultimoId ? $ultimoId + 1 : 1;
        
        return $numero;
    }
    
}