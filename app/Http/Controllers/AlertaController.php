<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertaController extends Controller
{    
    /**
     * Display a listing of the resource.
     */
    
    public function index()
    {    
        $fechaActual = Carbon::now()->format('Y-m-d');

        // Calcular la fecha límite (30 días a partir de la fecha actual)
        $fechaLimite = Carbon::now()->addDays(30)->format('Y-m-d');

        $insumosVencidos = DB::table('insumo_caracteristicas')
            ->where('vencimiento', '!=', '0001-01-01') // Excluir la fecha "01-01-0001"
            ->where('cantidad', '>', 0) // Asegurarse que la cantidad sea mayor a 0
            ->where(function ($query) use ($fechaActual, $fechaLimite) {
                // Incluir insumos que han vencido o que están por vencer
                $query->where('vencimiento', '<=', $fechaLimite) // Por vencer
                    ->where('vencimiento', '>=', $fechaActual) // Vencidos
                    ->orWhere('vencimiento', '<', $fechaActual); // Vencidos
            })
            ->join('insumos', 'insumo_caracteristicas.insumo_id', '=', 'insumos.id') // Unir con la tabla de insumos para obtener el nombre
            ->select('insumos.nombre as insumo', 'insumo_caracteristicas.vencimiento', 'insumo_caracteristicas.cantidad')
            ->get();

        // Filtrar los elementos en mal estado en la tabla consultorio_elemento
        $elementosMalEstado = DB::table('consultorio_elemento')
            ->where('observacion', 2) // Mal estado
            ->join('consultorios', 'consultorio_elemento.consultorio_id', '=', 'consultorios.id')
            ->join('elementos', 'consultorio_elemento.elemento_id', '=', 'elementos.id')
            ->select('consultorios.nombre as consultorio', 'elementos.nombre as elemento', 'consultorio_elemento.cantidad')
            ->get();

        // Filtrar los activos en reparación en la tabla activos
        $activosReparacion = DB::table('activos')
            ->where('activos.estado', 2) // Filtrar activos en reparación
            ->select('activos.codigo', 'activos.nombre', 'activos.cantidad') // Obtener solo el código, nombre y cantidad
            ->get();

        return view('crud.alerta.index', compact('elementosMalEstado', 'activosReparacion', 'insumosVencidos'));
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
