<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultorioRequest;
use App\Models\Consultorio;
use App\Models\Elemento;
use Illuminate\Http\Request;

class ConsultorioController extends Controller
{
    // Muestra la lista de consultorios
    public function index(Request $request)
    {
        // Obtener los consultorios desde la base de datos, ordenados por estado y nombre
        $consultorios = Consultorio::orderBy('estado', 'desc')
            ->paginate($request->input('page_size', 15)); // Paginación

        // Pasar los consultorios a la vista
        return view('crud.consultorio.index', compact('consultorios'));
    }


    public function store(StoreConsultorioRequest $request)
    {
        $consultorio = Consultorio::create($request->validated());
        $elementos = Elemento::all();

        // Asociar todos los elementos al nuevo consultorio con una observación predeterminada de 1
        foreach ($elementos as $elemento) {
            $consultorio->elementos()->attach($elemento->id, [
                'cantidad' => 0,
                'observacion' => 1 // Valor predeterminado para la observación
            ]);
        }

        return redirect()->route('consultorios.index')->with('Mensaje', 'Consultorio Agregado');
    }




    // Muestra los detalles de un consultorio específico
    public function show($id)
    {
        $consultorio = Consultorio::findOrFail($id);
        return view('crud.consultorio.show', compact('consultorio'));
    }

    // Muestra el formulario para editar un consultorio existente
    public function edit($id)
    {
        $consultorio = Consultorio::findOrFail($id);
        return view('crud.consultorio.edit', compact('consultorio'));
    }

    // Actualiza un consultorio existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $consultorio = Consultorio::findOrFail($id);
        $consultorio->update($request->all());
        return redirect()->route('consultorios.index')->with('Success', 'Consultorio actualizado');
    }

    // Elimina un consultorio
    public function destroy($id)
    {
        $consultorio = Consultorio::find($id);
        if ($consultorio) {
            if ($consultorio->estado == 1) {
                $consultorio->update([
                    'estado' => 0
                ]);
                return redirect('consultorios')->with('Mensaje', 'Consultorio Eliminado');
            } else {
                $consultorio->update([
                    'estado' => 1
                ]);
                return redirect('consultorios')->with('Mensaje3', 'Consultorio Restaurado');
            }
        } else {
            return redirect('consultorios')->with('Mensaje', 'Consultorios no encontrada');
        }
    }
}
