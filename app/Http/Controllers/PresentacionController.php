<?php

namespace App\Http\Controllers;
use App\Http\Requests\StorePresentacioneRequest;
use App\Models\Presentacione;
use Illuminate\Http\Request;

class PresentacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Presentacione::query();
    
        // Filtrar y ordenar por estado (primero estado 1, luego estado 0)
        $presentaciones = $query->orderBy('estado', 'desc')->orderBy('nombre', 'asc')->paginate($request->input('page_size', 10));
    
        return view('crud.presentacion.index', compact('presentaciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crud.presentacion.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePresentacioneRequest $request)
    {
        $datosPresentacion=request()->except('_token');
        Presentacione::insert($datosPresentacion);
        return redirect('presentacion/create')->with('Mensaje','Presentacion');  
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
        $presentacion=Presentacione::findOrFail($id);
        return view('crud.presentacion.edit',compact('presentacion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
  {
      $request->validate([
          'nombre' => 'required|max:60|unique:presentaciones,nombre,' . $id,
          'descripcion' => 'nullable|max:255',
      ]);

        $datosPresentacion=request()->except(['_token','_method']);
        Presentacione::where('id',$id)->update($datosPresentacion);
        return redirect('presentacion')->with('Mensaje','Presentacion');
  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
      $presentacion = Presentacione::find($id);
      if ($presentacion) {
        if ($presentacion->estado == 1) {
          $presentacion->update([
            'estado' => 0
          ]);
          return redirect('presentacion')->with('Mensaje2', 'presentacion eliminada');
        } else {
          $presentacion->update([
            'estado' => 1
          ]);
          return redirect('presentacion')->with('Mensaje3', 'presentacion restaurada');
        }
      } else {
        return redirect('presentacion')->with('Mensaje', 'presentacion no encontrada');
      }
    }
}
