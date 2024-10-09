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
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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
    $query = Insumo::with(['caracteristicas', 'marca', 'presentacion'])
      ->orderBy('nombre', 'asc') // Ordenar alfabéticamente por nombre de A a Z
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
    $pageSize = (int) $request->input('page_size', 50);
    if ($pageSize <= 0) {
      $pageSize = 50; // Asegúrate de que pageSize nunca sea 0
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
      'categorias' => $categorias,
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
    do {
      $codigo = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    } while (Insumo::where('codigo', $codigo)->exists()); // Verifica si el código ya existe

    // Obtener todos los datos excepto el token y añadir el código generado
    $datosInsumo = $request->except('_token');
    $datosInsumo['codigo'] = $codigo; // Añadir el código generado

    // Insertar el nuevo insumo en la base de datos
    $nuevoInsumo = Insumo::create($datosInsumo);

    // Obtener el ID del nuevo insumo creado
    $nuevoInsumoId = $nuevoInsumo->id;

    // Crear registros de Kardex para cada mes
    $annoActual = Carbon::now()->year;

    // Loop a través de los meses del año
    for ($mes = 1; $mes <= 12; $mes++) {
      Kardex::create([
        'insumo_id' => $nuevoInsumoId,
        'mes' => $mes,
        'anno' => $annoActual,
        // Otros campos del Kardex, si los tienes
      ]);
    }

    return redirect('insumo/create')->with('Mensaje', 'Insumo creado con éxito');
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
      // 'id_marca' => $request->input('id_marca'),
      // 'id_presentacion' => $request->input('id_presentacion'),
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

  public function exportToPdf(Request $request)
  {
    // Obtener el id de la categoría seleccionada
    $idCategoria = $request->input('id_categoria');
    $categoriaNombre = $idCategoria ? Categoria::find($idCategoria)->nombre : 'Todas las categorías';

    // Obtener insumos y sus características filtrando por categoría
    $insumos = Insumo::with(['caracteristicas.marca', 'caracteristicas.presentacion'])
      ->when($idCategoria, function ($query, $id) {
        return $query->where('id_categoria', $id); // Asegúrate de que 'categoria_id' es la columna correcta
      })
      ->get();

    // Crear el HTML para el PDF
    $html = '<style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid black; text-align: left; padding: 8px; }
                    th { background-color: #f2f2f2; }
                </style>';

    // Agregar el título con el nombre de la categoría
    $html .= '<h6 style="text-align: center;">Categoría: ' . $categoriaNombre . '</h6>';
    $html .= '<table>';
    $html .= '<tr><th>Nombre del Insumo</th><th>INVIMA</th><th>Lote</th><th>Fecha</th><th>Marca</th><th>Presentación</th><th>Cantidad</th></tr>';

    // Iterar sobre cada insumo y sus características
    foreach ($insumos as $insumo) {
      foreach ($insumo->caracteristicas as $caracteristica) {
        $html .= '<tr>';
        $html .= '<td>' . $insumo->nombre . '</td>';
        $html .= '<td>' . $caracteristica->invima . '</td>';
        $html .= '<td>' . $caracteristica->lote . '</td>';
        $html .= '<td>' . $caracteristica->vencimiento . '</td>';
        $html .= '<td>' . ($caracteristica->marca ? $caracteristica->marca->nombre : 'Sin Marca') . '</td>'; // Cambiado aquí
        $html .= '<td>' . ($caracteristica->presentacion ? $caracteristica->presentacion->nombre : 'Sin Presentación') . '</td>'; // Cambiado aquí
        $html .= '<td>' . $caracteristica->cantidad . '</td>';
        $html .= '</tr>';
      }
    }
    $html .= '</table>';

    // Configurar Dompdf para renderizar HTML
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // Renderizar PDF
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Descargar PDF
    return $dompdf->stream('Insumos_y_Caracteristicas.pdf');
  }

  public function generarCodigoBarrasPDF($id)
  {
    // Encontrar el insumo por ID
    $insumo = Insumo::findOrFail($id);
    $codigo = $insumo->codigo; // Suponiendo que tienes un campo 'codigo' en la tabla 'insumos'
    $nombreInsumo = $insumo->nombre; // Obtener el nombre del insumo
    $nombreArchivo = 'codigo_barras_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $insumo->nombre) . '.pdf';
    // Genera el código de barras
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

    // Crea una nueva instancia de Dompdf
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true); // Permitir imágenes remotas
    $options->set('isHtml5ParserEnabled', true); // Habilitar el analizador HTML5

    // Configurar márgenes a 0
    $options->set('marginTop', 0);
    $options->set('marginBottom', 0);
    $options->set('marginLeft', 0);
    $options->set('marginRight', 0);

    $dompdf = new \Dompdf\Dompdf($options);
    // HTML del PDF
    $html = '
  <style>
      .sticker {
          width: 130mm;
          height: 150mm;
          display: flex;
          justify-content: center;
          text-align: center;
          margin: 0;
          padding: 0;
      }
      .logo {
          text-align: center;
          width: 100%;
          font-size: 24px;
          margin: 0;
          padding: 0;
          margin-bottom: 20px;
          font-weight: bold; 
      }
      .barcode {
          width: 100%;
          height: 30%;
          margin: 0;
          padding: 0;
      }
      .codigo {
          font-size: 32px;
          font-weight: bold;
          letter-spacing: 35px;
          margin: 5px 0;
          font-weight: bold; 
      }
  </style>

  <div class="sticker">
      <p class="logo">' . $nombreInsumo . '</p>
      <img class="barcode" src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Código de Barras">
      <p class="codigo">' . $codigo . '</p>
  </div>
  ';

    // Cargar el HTML
    $dompdf->loadHtml($html);

    // Configura el tamaño del papel y la orientación
    $dompdf->setPaper('A6', 'landscape');

    // Renderiza el PDF
    $dompdf->render();

    // Envía el PDF al navegador para descargar
    return $dompdf->stream($nombreArchivo, [
      'Attachment' => true
    ]);
  }
}
