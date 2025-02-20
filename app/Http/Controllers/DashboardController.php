<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Compra;
use App\Models\Entrega;
use App\Models\Insumo;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Proveedore;
use App\Models\Servicio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Conteos de los diferentes modelos
        $usuarioCount = User::count();
        $preveedoresCount = Proveedore::count();
        $serviciosCount = Servicio::count();
        $categoriaCount = Categoria::count();
        $marcaCount = Marca::count();
        $presentacionCount = Presentacione::count();
        $insumoCount = Insumo::count();

        // Obtén insumos con características que están próximos a vencer o vencidos
        $insumos = Insumo::with('caracteristicas')
            ->whereHas('caracteristicas', function ($query) {
                $query->where('cantidad', '>', 0)
                    ->where(function ($query) {
                        $query->where('vencimiento', '<=', now()->addMonth()) // Menos de un mes
                            ->where('vencimiento', '!=', '0001-01-01'); // Excluir fecha 01-01-0001
                    });
            })
            ->get();

        // Obtener las categorías
        $categorias = Categoria::all();

        $top7ValorConsumo = DB::table('entrega_insumo')
            ->join('insumos', 'entrega_insumo.insumo_id', '=', 'insumos.id')
            ->select(
                'insumos.nombre',
                DB::raw('SUM(entrega_insumo.cantidad * entrega_insumo.valor_unitario) as total_valor')
            )
            ->whereMonth('entrega_insumo.created_at', now()->month)
            ->whereYear('entrega_insumo.created_at', now()->year)
            ->groupBy('insumos.nombre')
            ->orderByDesc('total_valor')
            ->limit(7)
            ->get();

            $topInsumosMes = DB::table('entrega_insumo')
            ->select('insumos.nombre', DB::raw('SUM(entrega_insumo.cantidad) as total_entregado'))
            ->join('insumos', 'entrega_insumo.insumo_id', '=', 'insumos.id')
            ->join('entregas', 'entrega_insumo.entrega_id', '=', 'entregas.id') // Relación con la tabla entregas
            ->where('entregas.servicio_id', '!=', 13) // Excluir el servicio "AJUSTE DE INVENTARIO"
            ->whereMonth('entrega_insumo.created_at', now()->month)
            ->whereYear('entrega_insumo.created_at', now()->year)
            ->groupBy('insumos.nombre')
            ->orderByDesc('total_entregado')
            ->limit(7)
            ->get();
        
        // Pasar los nombres y cantidades a la vista
        $topNombres = $topInsumosMes->pluck('nombre')->toArray();
        $topCantidades = $topInsumosMes->pluck('total_entregado')->toArray();
        

        // Calcular el valor total de inventario por categoría
        $categoriasValores = [];
        foreach ($categorias as $categoria) {
            $valorTotalCategoria = Insumo::where('id_categoria', $categoria->id)
                ->join('insumo_caracteristicas', 'insumos.id', '=', 'insumo_caracteristicas.insumo_id')
                ->sum(DB::raw('insumo_caracteristicas.valor_unitario * insumo_caracteristicas.cantidad'));

            $categoriasValores[] = [
                'nombre' => $categoria->nombre,
                'valor_total' => $valorTotalCategoria,
            ];
        }

        // Obtener las compras y entregas mensuales (últimos 6 meses)
        $comprasYEntregasMensuales = [
            'compras' => [],
            'entregas' => [],
        ];

        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);

            $comprasTotales = Compra::whereMonth('created_at', $mes->month)
                ->whereYear('created_at', $mes->year)
                ->sum('valor_total');

            $entregasTotales = Entrega::whereMonth('created_at', $mes->month)
                ->whereYear('created_at', $mes->year)
                ->whereNotIn('servicio_id', [13]) // Excluir entregas al servicio con ID 13
                ->sum('valor_total');

            $comprasYEntregasMensuales['compras'][] = $comprasTotales;
            $comprasYEntregasMensuales['entregas'][] = $entregasTotales;
        }


        // Obtener evolución del inventario con cierre al último día de cada mes
        $evolucionInventario = $this->obtenerEvolucionInventarioUltimosMeses();

        // Pasar todos los datos a la vista
        return view('dash.index', compact(
            'usuarioCount',
            'preveedoresCount',
            'serviciosCount',
            'categoriaCount',
            'marcaCount',
            'presentacionCount',
            'insumoCount',
            'insumos',
            'categoriasValores',
            'comprasYEntregasMensuales',
            'topNombres',
            'topCantidades',
            'evolucionInventario',
            'top7ValorConsumo'
        ));
    }

    private function obtenerEvolucionInventarioUltimosMeses()
    {
        $mesActual = now()->startOfMonth();
        $datos = [];
        $valorAcumulado = 0;

        for ($i = 5; $i >= 0; $i--) {
            $fechaInicio = $mesActual->copy()->subMonths($i)->startOfMonth();
            $fechaFin = $mesActual->copy()->subMonths($i)->endOfMonth();

            // Calcular las compras del mes
            $comprasMes = Compra::whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->sum('valor_total');

            // Calcular las entregas del mes
            $entregasMes = Entrega::whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->sum('valor_total');

            // Actualizar el valor acumulado del inventario
            $valorAcumulado += $comprasMes; // Sumar compras al inventario
            $valorAcumulado -= $entregasMes; // Restar entregas del inventario

            $datos[] = [
                'mes' => $fechaFin->format('F'), // Ejemplo: "Septiembre"
                'valor' => $valorAcumulado > 0 ? $valorAcumulado : 0, // Asegurarse de no registrar valores negativos
            ];
        }

        return $datos;
    }
}
