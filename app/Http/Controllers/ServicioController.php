<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreServicioRequest;
use App\Models\Servicio;
use Illuminate\Http\Request;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Servicio::query();
        $servicios = $query->orderBy('estado', 'desc')->orderBy('nombre', 'asc')->paginate($request->input('page_size', 10));
        return view('crud.servicio.index', compact('servicios'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crud.servicio.create');    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServicioRequest $request)
    {
    // $datosCategoria=request()->all();
      $datosServicio=request()->except('_token');
      Servicio::insert($datosServicio);
      return redirect('servicio/create')->with('Mensaje','Servicio Agregado Correctamente');

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
        $servicio=Servicio::findOrFail($id);
        return view('crud.servicio.edit',compact('servicio'));    
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|max:60|unique:servicios,nombre,' . $id,
            'descripcion' => 'nullable|max:255',
        ]); 
        $datosServicio=request()->except(['_token','_method']);
        Servicio::where('id',$id)->update($datosServicio);
        return redirect('servicio')->with('Mensaje2','Servicio');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $servicio = Servicio::find($id);
        if ($servicio) {
            if ($servicio->estado == 1) {

                $servicio->estado = 0;
                $servicio->save();
                return redirect('servicio')->with('Mensaje', 'servicio eliminado');
            } else {
                // Cambiar el estado a activo
                $servicio->estado = 1;
                $servicio->save();
                return redirect('servicio')->with('Mensaje3', 'servicio restaurado');
            }
        } else {
            return redirect('servicio')->with('Mensaje', 'servicio no encontrado');
        }
    }
}
