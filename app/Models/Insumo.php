<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'stock',
        'requiere_lote',
        'requiere_invima',
        'riesgo',
        'vida_util',
        'id_categoria',
        'id_marca',
        'id_presentacion',
        'estado'
    ];

    public function ingresosDelMes($mes, $anno)
    {
        return $this->kardex()
                    ->where('mes', $mes)
                    ->where('anno', $anno)
                    ->sum('ingresos');
    }

    public function egresosDelMes($mes, $anno)
    {
        return $this->kardex()
                    ->where('mes', $mes)
                    ->where('anno', $anno)
                    ->sum('egresos');
    }

    public function kardex()
    {
        return $this->hasMany(Kardex::class);
    }

    
    public function compras()
    {
        return $this->belongsToMany(Compra::class)->withTimestamps()->withPivot('cantidad');
    }

    public function entregas()
    {
        return $this->belongsToMany(Entrega::class)
                    ->withPivot('cantidad', 'invima', 'lote', 'vencimiento')
                    ->withTimestamps();
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function presentacione()
    {
        return $this->belongsTo(Presentacione::class, 'id_presentacion');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
     
    public function caracteristicas()
    {
        return $this->hasMany(InsumoCaracteristica::class, 'insumo_id');
    }
    
    // Definición de la relación entregaInsumo
    public function entregaInsumo()
    {
        return $this->hasMany(EntregaInsumo::class, 'insumo_id');
    }

    public function detallesTransaccion()
    {
        return $this->hasMany(DetalleTransaccion::class, 'insumo_id');
    }
}
