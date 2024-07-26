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

    public static function generarNumeroComprobante() {
        $ultimo = self::latest('numero_comprobante')->first();
        $ultimoNumero = $ultimo ? $ultimo->numero_comprobante : 0;
        return $ultimoNumero + 1;
    }
}