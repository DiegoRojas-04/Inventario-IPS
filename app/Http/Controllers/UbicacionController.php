<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;

class UbicacionController extends Controller
{
    // Mostrar una lista de ubicaciones
    public function index()
    {
        $ubicaciones = Ubicacion::all();
        return view('crud.ubicacion.index', compact('ubicaciones'));
    }

    // Mostrar el formulario para crear una nueva ubicación
    public function create()
    {
        return view('ubicaciones.create');
    }

    // Almacenar una nueva ubicación
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|max:255',
            'descripcion' => 'nullable|max:255',
        ]);

        Ubicacion::create($request->all());
        return redirect()->route('ubicaciones.index')->with('Mensaje', 'Ubicaion Agregada');
    }

    // Mostrar una ubicación específica
    public function show(Ubicacion $ubicacion)
    {
        return view('ubicaciones.show', compact('ubicacion'));
    }

    // Mostrar el formulario para editar una ubicación
    public function edit($id)
    {
        $ubicacion = Ubicacion::findOrFail($id);
        return view('crud.ubicacion.edit', compact('ubicacion'));
    }

    // Actualizar una ubicación específica
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        $ubicacion = Ubicacion::findOrFail($id);
        $ubicacion->update($request->all());
        return redirect()->route('ubicaciones.index')->with('Success', 'Consultorio actualizado');
    }

    // Eliminar una ubicación
    public function destroy(Ubicacion $ubicacion)
    {
        $ubicacion->delete();
        return redirect()->route('ubicaciones.index')->with('success', 'Ubicación eliminada con éxito.');
    }
}
