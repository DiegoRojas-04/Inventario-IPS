<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Insumo;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::all();
        return view('crud.pedido.index', compact('pedidos'));
    }

    public function show($id)
    {
        $pedido = Pedido::findOrFail($id);
        return view('crud.pedido.show', compact('pedido'));
    }
    public function store(Request $request)
    {
        dd($request);
        $pedido = new Pedido();
        $pedido->user_id = auth()->user()->id;
        $pedido->fecha_hora = now();
        $pedido->estado = 1;
        $pedido->save();
    
        $insumos = $request->input('insumos');
        $cantidades = $request->input('cantidades');
    
        // Iterar sobre los insumos seleccionados y guardar los detalles del pedido
        foreach ($insumos as $key => $insumoId) {
                $insumo = Insumo::findOrFail($insumoId);
            $cantidad = $cantidades[$key];
    
            // Asociar el insumo al pedido y guardar la cantidad
            $pedido->insumos()->attach($insumoId, ['cantidad' => $cantidad]);
        }
    
        return redirect()->route('pedido.index')->with('success', 'Pedido realizado con éxito.');
    }
    

    public function create()
    {
        $insumos = Insumo::all();
        return view('crud.pedido.create', compact('insumos'));
    }
}
