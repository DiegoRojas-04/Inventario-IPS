<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInsumoRequest;
use App\Models\Caracteristica;
use App\Models\Categoria;
use App\Models\Insumo;
use App\Models\Kardex;
use App\Models\Marca;
use App\Models\Presentacione;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class InsumoController extends Controller
// implements HasMiddleware
{
  /**
   * Display a listing of the resource.
   */

   
   public function index(Request $request)
{
    // Crear la consulta base con relaciones
    $query = Insumo::with(['caracteristicas', 'marca', 'presentacione'])
        ->orderBy('estado', 'desc'); // Ordenar por estado

    $categorias = Categoria::all();

    // Filtrar por categoría si se proporciona
    if ($request->has('id_categoria') && !empty($request->id_categoria)) {
        $query->where('id_categoria', $request->id_categoria);
    }

    // Filtrar por término de búsqueda si se proporciona
    if ($request->has('search') && !empty($request->search)) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nombre', 'LIKE', "%$search%")
              ->orWhere('descripcion', 'LIKE', "%$search%");
        });
    }

    // Obtener los insumos
    $insumos = $query->get();

    // Definir el tamaño de la página y la página actual
    $pageSize = (int) $request->input('page_size', 20);
    if ($pageSize <= 0) {
        $pageSize = 20; // Asegúrate de que pageSize nunca sea 0
    }
    
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $items = collect($insumos);

    if ($items->isEmpty()) {
        $paginatedItems = new LengthAwarePaginator(
            collect([]),
            0,
            $pageSize,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    } else {
        // Separar y clasificar los insumos con características próximas a vencer
        $insumosVencidos = [];
        $otrosInsumos = [];
        $insumosEliminados = [];

        foreach ($items as $insumo) {
            if ($insumo->estado == 0) {
                $insumosEliminados[] = $insumo;
            } elseif ($insumo->alertClass === 'table-danger') {
                $insumosVencidos[] = $insumo;
            } else {
                $otrosInsumos[] = $insumo;
            }
        }

        // Combinar las listas para tener primero los insumos próximos a vencer
        // y luego los insumos eliminados
        $items = collect(array_merge($insumosVencidos, $otrosInsumos, $insumosEliminados));

        // Paginar los resultados combinados
        $paginatedItems = new LengthAwarePaginator(
            $items->forPage($currentPage, $pageSize),
            $items->count(),
            $pageSize,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    return view('crud.insumo.index', [
        'insumos' => $paginatedItems,
        'categorias' => $categorias
    ]);
}
          

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $categorias = Categoria::where('estado', 1)->get();
    $marcas = Marca::where('estado', 1)->get();
    $presentaciones = Presentacione::where('estado', 1)->get();

    // Obtener todas las características disponibles de los insumos
    $variantes = Caracteristica::all();

    return view('crud.insumo.create', compact('categorias', 'presentaciones', 'marcas', 'variantes'));
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(StoreInsumoRequest $request)
  {
    // Lógica para crear un nuevo insumo
    $datosInsumo = request()->except('_token');
    Insumo::insert($datosInsumo);

    // Obtener el ID del nuevo insumo creado
    $nuevoInsumoId = Insumo::latest()->first()->id;

    // Crear registros de Kardex para cada mes
    $mesActual = Carbon::now()->month;
    $annoActual = Carbon::now()->year;

    // Loop a través de los meses que deseas seguir
    for ($mes = 1; $mes <= 12; $mes++) {
      Kardex::create([
        'insumo_id' => $nuevoInsumoId,
        'mes' => $mes,
        'anno' => $annoActual,
        // Otros campos del Kardex
      ]);
    }

    return redirect('insumo/create')->with('Mensaje', 'Insumo');
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
    $insumo = Insumo::findOrFail($id);
    $categorias = Categoria::all();
    $marcas = Marca::all();
    $presentaciones = Presentacione::all();
    $caracteristicas = $insumo->caracteristicas;
    return view('crud.insumo.edit', compact('insumo', 'categorias', 'marcas', 'presentaciones', 'caracteristicas'));
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, $id)
  {
    $request->validate([
      'nombre' => 'required|max:60|unique:insumos,nombre,' . $id,
      'descripcion' => 'nullable|max:255',
      'codigo' => 'nullable|numeric',
    ]);
    $insumo = Insumo::findOrFail($id);
    $insumo->fill([
      'nombre' => $request->input('nombre'),
      'descripcion' => $request->input('descripcion'),
      'requiere_invima' => $request->filled('requiere_invima')  ? 1 : 0,
      'requiere_lote' => $request->filled('requiere_lote') ? 1 : 0,
      'id_categoria' => $request->input('id_categoria'),
      'id_marca' => $request->input('id_marca'),
      'id_presentacion' => $request->input('id_presentacion'),
      'riesgo' => $request->input('riesgo'),
      'vida_util' => $request->input('vida_util'),
      'codigo' => $request->input('codigo'),
      // 'stock' => $request->input('stock'),
    ]);
    $insumo->save();
    return redirect('insumo')->with('Mensaje2', 'Insumo Actualizada Correctamente');
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    $insumo = Insumo::find($id);
    if ($insumo) {
      if ($insumo->estado == 1) {
        $insumo->update([
          'estado' => 0
        ]);
        return redirect('insumo')->with('Mensaje', 'insumo eliminada');
      } else {
        $insumo->update([
          'estado' => 1
        ]);
        return redirect('insumo')->with('Mensaje3', 'insumo restaurada');
      }
    } else {
      return redirect('insumo')->with('Mensaje', 'insumo no encontrada');
    }
  }
}
