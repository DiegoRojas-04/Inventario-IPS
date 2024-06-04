<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Models\Proveedore;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proveedore::query();
    
        // Filtrar y ordenar por estado (primero estado 1, luego estado 0)
        $proveedores = $query->orderBy('estado', 'desc')->orderBy('nombre', 'asc')->paginate($request->input('page_size', 10));
    
        return view('crud.proveedor.index', compact('proveedores'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crud.proveedor.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProveedorRequest $request)
    {
        $datosProveedor = request()->except('_token');
        Proveedore::insert($datosProveedor);
        return redirect('proveedor/create')->with('Mensaje', 'Proveedor Agregado Correctamente');
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
        $proveedor = Proveedore::findOrFail($id);
        return view('crud.proveedor.edit', compact('proveedor'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:60|unique:proveedores,nombre,' . $id,
            'descripcion' => 'nullable|max:255',
        ]);
        $datosProveedor = request()->except(['_token', '_method']);
        Proveedore::where('id', $id)->update($datosProveedor);
        return redirect('proveedor')->with('Mensaje2', 'Proveedor');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $proveedor = Proveedore::find($id);
        if ($proveedor) {
            // Verificar si el proveedor está activo o eliminado
            if ($proveedor->estado == 1) {
                // Cambiar el estado a eliminado
                $proveedor->estado = 0;
                $proveedor->save();
                return redirect('proveedor')->with('Mensaje', 'Proveedor eliminado');
            } else {
                // Cambiar el estado a activo
                $proveedor->estado = 1;
                $proveedor->save();
                return redirect('proveedor')->with('Mensaje3', 'Proveedor restaurado');
            }
        } else {
            return redirect('proveedor')->with('Mensaje', 'Proveedor no encontrado');
        }
    }
}
