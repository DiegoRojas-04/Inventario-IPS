<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElementoRequest;
use App\Models\Elemento;
use App\Models\Consultorio;
use Illuminate\Http\Request;

class ElementoController extends Controller
{

    public function elementosPorConsultorio($consultorioId)
    {
        $consultorio = Consultorio::findOrFail($consultorioId);
        $elementos = $consultorio->elementos; // Obtener los elementos relacionados con el consultorio

        return view('crud.elemento.index', compact('consultorio', 'elementos'));
    }
    // Muestra la lista de elementos

    public function index(Request $request)
    {

        $consultorios = Consultorio::all(); // Obtener todos los consultorios
        $elementos = []; // Inicializar como vacío si no se ha seleccionado
        $todosLosElementos = Elemento::orderBy('nombre', 'asc')->get(); // Obtener todos los elementos ordenados alfabéticamente

        // Si no hay un consultorio seleccionado, mostrar la suma total de todos los elementos
        if (!$request->has('consultorio_id') || $request->consultorio_id === '') {
            $elementos = Elemento::with('consultorios')
                ->orderBy('nombre', 'asc') // Ordenar elementos por nombre
                ->get()
                ->map(function ($elemento) {
                    // Sumar la cantidad de cada elemento en todos los consultorios
                    $cantidadTotal = $elemento->consultorios->sum('pivot.cantidad'); // Asegúrate de que 'cantidad' sea la columna que quieres

                    return [
                        'nombre' => $elemento->nombre,
                        'cantidad_total' => $cantidadTotal,
                    ];
                });
        } elseif ($request->has('consultorio_id')) {
            $consultorio = Consultorio::findOrFail($request->consultorio_id); // Obtener el consultorio
            $elementos = $consultorio->elementos()
                ->orderBy('nombre', 'asc') // Ordenar elementos del consultorio seleccionado por nombre
                ->get(); // Obtener los elementos del consultorio seleccionado
        }

        return view('crud.elemento.index', compact('consultorios', 'elementos', 'todosLosElementos'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:elementos,nombre',
            'categoria' => 'required|string|in:Equipos,Insumos,Papeleria',
            'descripcion' => 'nullable|string|max:500',
            'cantidad_necesaria' => 'required|integer',
        ]);

        $elemento = Elemento::create($request->all());
        $consultorios = Consultorio::all();

        // Asociar el elemento a los consultorios con una observación predeterminada de 1
        foreach ($consultorios as $consultorio) {
            $consultorio->elementos()->attach($elemento->id, [
                'cantidad' => 0,
                'observacion' => 1 // Valor predeterminado para la observación
            ]);
        }

        return redirect()->route('elementos.create')->with('Mensaje', 'Elemento Agregado');
    }


    public function create(Request $request)
    {
        // Obtener la categoría seleccionada si existe
        $categoriaSeleccionada = $request->input('categoria');

        // Obtener todos los elementos, ordenados por estado, categoría y nombre
        $query = Elemento::orderBy('estado', 'desc') // Primero estado 1, luego estado 0
            ->orderByRaw("CASE 
                WHEN categoria = 'Equipos' THEN 1 
                WHEN categoria = 'Insumos' THEN 2 
                WHEN categoria = 'Papeleria' THEN 3 
                ELSE 4 
            END")
            ->orderBy('nombre');

        // Filtrar por categoría si se selecciona una
        if ($categoriaSeleccionada) {
            $query->where('categoria', $categoriaSeleccionada);
        }

        $elementos = $query->get();

        return view('crud.elemento.create', compact('elementos', 'categoriaSeleccionada'));
    }


    // Almacena un nuevo elemento
    // Muestra los detalles de un elemento específico
    public function show($id)
    {
        $elemento = Elemento::findOrFail($id);
        return view('crud.elemento.show', compact('elemento'));
    }

    public function updateCantidad(Request $request, $id)
    {
        $elemento = Elemento::findOrFail($id);

        // Validar los campos
        $request->validate([
            'consultorio_id' => 'required|exists:consultorios,id',
            'cantidad' => 'required|integer|min:0',
            'observacion' => 'nullable|string|max:255',
        ]);

        // Obtener el consultorio_id si está presente
        $consultorioId = $request->input('consultorio_id');

        // Actualizar la cantidad y la observación del elemento en el consultorio correspondiente
        $elemento->consultorios()->updateExistingPivot($consultorioId, [
            'cantidad' => $request->input('cantidad'),
            'observacion' => $request->input('observacion'), // Actualizar también la observación
        ]);

        // Condicional para redirigir según si hay o no un consultorio seleccionado
        if ($consultorioId) {
            return redirect()->route('elementos.index', ['consultorio_id' => $consultorioId])
                ->with('Success', 'Actualizado Correctamente');
        } else {
            return redirect()->route('elementos.index')
                ->with('Success', 'Actualizado Correctamente');
        }
    }

    public function actualizarCantidades(Request $request, $consultorioId)
    {
        $data = $request->validate([
            'cantidades.*' => 'required|integer|min:0',
            'observaciones.*' => 'required|integer|in:1,2,3,4', // Ajusta según tus opciones válidas
        ]);

        foreach ($data['cantidades'] as $elementoId => $cantidad) {
            // Suponiendo que hay una relación many-to-many entre Consultorio y Elemento
            $consultorioElemento = Consultorio::find($consultorioId)
                ->elementos()
                ->where('elemento_id', $elementoId)
                ->first();

            if ($consultorioElemento) {
                $consultorioElemento->pivot->cantidad = $cantidad;
                $consultorioElemento->pivot->observacion = $data['observaciones'][$elementoId];
                $consultorioElemento->pivot->save();
            }
        }

        return redirect()->back()->with('Success', 'Actualizado Correctamente');

    }
    // Muestra el formulario para editar un elemento existente
    public function edit($id)
    {
        $elemento = Elemento::findOrFail($id);
        $consultorios = Consultorio::all();
        return view('crud.elemento.edit', compact('elemento', 'consultorios'));
    }

    // Actualiza un elemento existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'categoria' => 'required|string|in:Equipos,Insumos,Pepeleria',
            'cantidad_necesaria' => 'required|integer',
        ]);

        $elemento = Elemento::findOrFail($id);
        $elemento->update($request->all());

        return redirect()->route('elementos.create')->with('Success', 'Elemento actualizado');
    }

    // Elimina un elemento
    public function destroy($id)
    {
        $elemento = Elemento::find($id);
        if ($elemento) {
            if ($elemento->estado == 1) {
                $elemento->update([
                    'estado' => 0
                ]);
                return redirect('elementos/create')->with('Mensaje', 'Elemento Eliminado');
            } else {
                $elemento->update([
                    'estado' => 1
                ]);
                return redirect('elementos/create')->with('Mensaje3', 'Elemento Restaurado');
            }
        } else {
            return redirect('elementos/create')->with('Mensaje', 'Elemento no encontrado');
        }
    }
}
