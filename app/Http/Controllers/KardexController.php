<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Insumo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    // Obtener el mes y el año seleccionados en el formulario
    $selectedMonth = $request->input('mes', date('n'));
    $selectedYear = $request->input('anno', date('Y'));

    // Obtener todas las categorías
    $categorias = Categoria::all();

    // Obtener los insumos
    $insumos = Insumo::with('detallesTransaccion', 'compras', 'entregas')->orderBy('nombre', 'asc')->get();

    // Filtrar por categoría si se selecciona una
    if ($request->has('id_categoria')) {
        $idCategoria = $request->input('id_categoria');
        // Filtrar solo si se selecciona una categoría específica
        if ($idCategoria != "") {
            $insumos = $insumos->where('id_categoria', $idCategoria);
        }
    }

    // Obtener los insumos y calcular sus datos del Kardex
    $insumos = $insumos->map(function ($insumo) use ($selectedMonth, $selectedYear) {
        $insumo->cantidad_inicial_mes = $this->calcularCantidadInicialMes($insumo, $selectedMonth, $selectedYear);
        $insumo->ingresos_mes = $insumo->ingresosDelMes($selectedMonth, $selectedYear);
        $insumo->egresos_mes = $insumo->egresosDelMes($selectedMonth, $selectedYear);
        $insumo->saldo_final_mes = $insumo->cantidad_inicial_mes + $insumo->ingresos_mes - $insumo->egresos_mes;
        return $insumo;
    });

    // Pasar los datos a la vista
    return view('crud.kardex', compact('insumos', 'selectedMonth', 'selectedYear', 'categorias'));
}


    private function calcularCantidadInicialMes($insumo, $mes, $anno)
    {
        // Obtener el mes y año anterior
        $fecha = Carbon::createFromDate($anno, $mes, 1);
        $fechaAnterior = $fecha->subMonth();
        $mesAnterior = $fechaAnterior->month;
        $annoAnterior = $fechaAnterior->year;

        // Calcular el saldo final del mes anterior como la cantidad inicial del mes actual
        $kardexAnterior = $insumo->kardex()->where('mes', $mesAnterior)->where('anno', $annoAnterior)->first();
        return $kardexAnterior ? $kardexAnterior->saldo : 0;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
