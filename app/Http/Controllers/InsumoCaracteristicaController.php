<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\InsumoCaracteristica;
use App\Models\Marca;
use App\Models\Presentacione;
use Illuminate\Http\Request;

class InsumoCaracteristicaController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit($insumoId, $caracteristicaId)
    {
        $insumo = Insumo::findOrFail($insumoId);
        $caracteristica = InsumoCaracteristica::findOrFail($caracteristicaId);
        $marcas = Marca::all();
        $presentaciones = Presentacione::all();

        return view('crud.caracteristica.edit', compact('insumo', 'caracteristica', 'marcas', 'presentaciones'));
    }

    public function update(Request $request, $insumoId, $caracteristicaId)
    {

        $request->validate([
            'cantidad' => 'required|integer|min:1 ',
            'valor_unitario' => 'nullable',
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

    public function destroy(string $id)
    {
        //
    }
}
