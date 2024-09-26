<?php

namespace App\Http\Controllers;

use App\Http\Requests\ActivoRequest;
use App\Http\Requests\StoreActivoRequest;
use App\Models\Activo;
use Illuminate\Http\Request;

class ActivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Activo::query();
    
        // Filtrado por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }
    
        // Búsqueda
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }
    
        // Paginación
        $pageSize = $request->input('pageSize', 15);
        $activos = $query->paginate($pageSize);
    
        return view('crud.activo.index', compact('activos'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crud.activo.create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreActivoRequest $request)
    {
        // Generar un código único de 6 dígitos
        $codigo = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Obtener todos los datos excepto el token y añadir el código generado
        $datosActivos = $request->except('_token');
        $datosActivos['codigo'] = $codigo;

        // Insertar los datos en la base de datos
        Activo::insert($datosActivos);

        return redirect('activo/create')->with('Mensaje', 'Activo creado con éxito');
    }

    public function updateEstado(Request $request, $id)
    {
        $activo = Activo::findOrFail($id);
        $activo->estado = $request->input('estado');
        $activo->save();

        return redirect()->back()->with('Mensaje2', 'Estado actualizado correctamente');
    }

    // En App\Http\Controllers\ActivoController
    public function updateObservacion(Request $request, $id)
    {
        $request->validate([
            'observacion' => 'nullable|string|max:255', // Validar la observación
        ]);

        $activo = Activo::findOrFail($id); // Encuentra el activo por ID
        $activo->observacion = $request->input('observacion'); // Actualiza la observación
        $activo->save(); // Guarda los cambios

        return redirect()->back()->with('Mensaje2', 'Observación actualizada con éxito'); // Redirige con un mensaje
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
    public function edit($id)
    {
        $activo = Activo::findOrFail($id); // Encuentra el activo por ID o lanza un error 404
        return view('crud.activo.edit', compact('activo')); // Pasa el activo a la vista
    }

    public function update(StoreActivoRequest $request, $id)
    {
        $datosActivos = $request->all(); // Obtiene los datos validados
        $activo = Activo::findOrFail($id); // Encuentra el activo por ID
        $activo->update($datosActivos);    // Actualiza el registro en la base de datos
        return redirect('activo')->with('Mensaje2', 'Activo actualizado con éxito');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $activo = Activo::findOrFail($id);

        // Cambia el estado del activo
        if ($activo->condicion == 1) {
            $activo->condicion = 0; // Establece como eliminado
            $mensaje = 'Activo Eliminado';
            $sessionMessage = 'Mensaje'; // Usar el mensaje para eliminar
        } else {
            $activo->condicion = 1; // Restaurar
            $mensaje = 'Activo Restaurado';
            $sessionMessage = 'Mensaje3'; // Usar el mensaje para restaurar
        }

        $activo->save();

        return redirect()->back()->with($sessionMessage, $mensaje);
    }
}
