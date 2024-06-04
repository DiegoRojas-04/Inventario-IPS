<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\InsumoCaracteristica;
use Illuminate\Http\Request;

class InsumoCaracteristicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function edit($insumoId, $caracteristicaId)
    {
        $insumo = Insumo::findOrFail($insumoId);
        $caracteristica = $insumo->caracteristicas()->findOrFail($caracteristicaId);

        return view('crud.caracteristica.edit', compact('insumo', 'caracteristica'));
    }



   
public function update(Request $request, $insumoId, $caracteristicaId)
{

    $request->validate([
        'cantidad' => 'required|integer|min:1 ',
    ]);

    $caracteristica = InsumoCaracteristica::findOrFail($caracteristicaId);
    $insumo = Insumo::findOrFail($insumoId);

    $cantidadAnterior = $caracteristica->cantidad;

    $caracteristica->update($request->all());

    $diferenciaCantidad = $caracteristica->cantidad - $cantidadAnterior;

    $insumo->stock += $diferenciaCantidad;
    $insumo->save();

    return redirect('insumo')->with('Mensaje2', 'Insumo Actualizado Correctamente');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
