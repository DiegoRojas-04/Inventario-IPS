<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'codigo',
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
            ->withPivot('id_marca', 'id_presentacion', 'invima', 'lote', 'vencimiento', 'cantidad')
            ->withTimestamps();
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
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

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class)->withPivot('cantidad');
    }

    public function getAlertClassAttribute()
    {
        foreach ($this->caracteristicas as $caracteristica) {
            $fechaVencimiento = Carbon::parse($caracteristica->vencimiento);
            $hoy = Carbon::now();
            $diferenciaDias = $hoy->diffInDays($fechaVencimiento, false);

            if ($fechaVencimiento->format('d-m-Y') !== '01-01-0001') {
                if ($caracteristica->cantidad > 0 && ($diferenciaDias <= 9 || $diferenciaDias < 0)) {
                    return 'table-danger';
                }
            }
        }
        return '';
    }
    // En tu modelo Insumo
    public function marca()
    {
        return $this->belongsTo(Marca::class, 'id_marca');
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacione::class, 'id_presentacion');
    }
    // Define la relación con las características (si es necesario)
    public function caracteristicas()
    {
        return $this->hasMany(InsumoCaracteristica::class, 'insumo_id'); // Asegúrate de que 'insumo_id' sea el nombre correcto de la columna en la tabla 'insumo_caracteristicas'
    }
    

}
