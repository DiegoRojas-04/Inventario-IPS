<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {
        $pedidos = Pedido::with('user')->latest()->get();
        return view('crud.pedido.index', compact('pedidos'));
    }

    public function create()
    {
        $insumos = Insumo::all();
        return view('crud.pedido.create', compact('insumos'));
    }


    public function store(Request $request)
    {
        $pedido = Pedido::create([
            'fecha_hora' => now(),
            'user_id' => auth()->id()
        ]);

        $insumos = $request->input('insumos');
        $cantidades = $request->input('cantidades');

        for ($i = 0; $i < count($insumos); $i++) {
            $pedido->insumos()->attach($insumos[$i], ['cantidad' => $cantidades[$i]]);
        }

        return redirect()->route('pedido.index');
    }

    public function show(string $id)
    {
        $pedido = Pedido::with('insumos')->findOrFail($id);
        return view('crud.pedido.show', compact('pedido'));
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
