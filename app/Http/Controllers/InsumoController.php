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
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
    $user = auth()->user();

    // Crear la consulta base con relaciones
    $query = Insumo::with(['caracteristicas', 'marca', 'presentacion'])
      ->orderBy('nombre', 'asc') 
      ->orderBy('estado', 'desc'); 

    // Obtener todas las categorías
    $categorias = Categoria::all();

    // Filtrar por categoría si se proporciona y si el usuario es Administrador
    if ($user->roles->contains('name', 'Administrador')) {
      // Si el usuario es Administrador, permitir filtrar por categoría
      if ($request->has('id_categoria') && !empty($request->id_categoria)) {
        $query->where('id_categoria', $request->id_categoria);
      }
    } elseif ($user->roles->contains('name', 'Laboratorio')) {
      // Si el usuario es Laboratorio, solo permitir ver insumos de la categoría 6
      $query->where('id_categoria', 12);
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
    $pageSize = (int) $request->input('page_size', 10);
    if ($pageSize <= 0) {
      $pageSize = 10; // Asegúrate de que pageSize nunca sea 0
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

    return view('crud.insumo.create', compact('categorias', 'presentaciones', 'marcas',));
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
      'ubicacion' => 'nullable|numeric',
    ]);
    $insumo = Insumo::findOrFail($id);
    $insumo->fill([
      'nombre' => $request->input('nombre'),
      'descripcion' => $request->input('descripcion'),
      'requiere_invima' => $request->filled('requiere_invima')  ? 1 : 0,
      'requiere_lote' => $request->filled('requiere_lote') ? 1 : 0,
      'iva' => $request->filled('iva') ? 1 : 0,
      'id_categoria' => $request->input('id_categoria'),
      // 'id_marca' => $request->input('id_marca'),
      // 'id_presentacion' => $request->input('id_presentacion'),
      'riesgo' => $request->input('riesgo'),
      'vida_util' => $request->input('vida_util'),
      'ubicacion' => $request->input('ubicacion'),
      'codigo' => $request->input('codigo'),
      // 'stock' => $request->input('stock'),
    ]);
    $insumo->save();
    return redirect('insumo')->with('Mensaje2', 'Insumo Actualizada Correctamente');
  }


  public function analisisPrecios(Request $request)
  {
    $insumos = Insumo::with(['caracteristicas', 'caracteristicas.compra.proveedor'])
      ->get()
      ->map(function ($insumo) {
        $caracteristicas = $insumo->caracteristicas->sortByDesc('created_at');

        $ultimaCompra = $caracteristicas->first();
        $penultimaCompra = $caracteristicas->skip(1)->first();

        $diferenciaPorcentaje = null;
        if ($ultimaCompra && $penultimaCompra && $penultimaCompra->valor_unitario != 0) {
          $diferenciaPorcentaje = ($ultimaCompra->valor_unitario - $penultimaCompra->valor_unitario) / $penultimaCompra->valor_unitario * 100;
        }

        return [
          'nombre' => $insumo->nombre,
          'proveedor_ultima' => $ultimaCompra?->compra->proveedor->nombre,
          'fecha_penultima' => $penultimaCompra ? \Carbon\Carbon::parse($penultimaCompra->compra->fecha_hora)->format('d-m-Y') : null,
          'valor_penultima' => $penultimaCompra?->valor_unitario,
          'proveedor_penultima' => $penultimaCompra?->compra->proveedor->nombre,
          'fecha_ultima' => $ultimaCompra ? \Carbon\Carbon::parse($ultimaCompra->compra->fecha_hora)->format('d-m-Y') : null,
          'valor_ultima' => $ultimaCompra?->valor_unitario,
          'diferencia_porcentaje' => $diferenciaPorcentaje
        ];
      })
      ->filter(function ($insumo) use ($request) {
        // Aplicar el filtro de cambio de precio
        if ($request->price_change === 'up') {
          return $insumo['diferencia_porcentaje'] > 0;
        } elseif ($request->price_change === 'down') {
          return $insumo['diferencia_porcentaje'] < 0;
        } elseif ($request->price_change === 'equal') {
          return $insumo['diferencia_porcentaje'] == 0;
        }
        return true; // No aplicar filtro si no se selecciona opción
      })
      ->sortBy('nombre'); // Ordenar por el nombre del insumo

    return view('crud.insumo.porcentaje', compact('insumos'));
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

    // Obtener insumos y sus características filtrando por categoría y ordenando alfabéticamente
    $insumos = Insumo::with(['caracteristicas.marca', 'caracteristicas.presentacion'])
      ->when($idCategoria, function ($query, $id) {
        return $query->where('id_categoria', $id);
      })
      ->orderBy('nombre', 'asc') // Ordenar alfabéticamente por el nombre del insumo
      ->get();

    // Calcular el valor total de todos los insumos
    $valorTotal = 0;
    foreach ($insumos as $insumo) {
      foreach ($insumo->caracteristicas as $caracteristica) {
        if ($caracteristica->cantidad > 0) {
          $valorTotal += $caracteristica->cantidad * $caracteristica->valor_unitario;
        }
      }
    }

    // Crear el HTML para el PDF con ajustes de estilo
    $html = '<style>
                  body { font-family: Arial, sans-serif; }
                  table { width: 100%; border-collapse: collapse; font-size: 10px; } /* Reducido a 10px */
                  th, td { border: 1px solid black; text-align: left; padding: 4px; } /* Padding reducido */
                  th { background-color: #f2f2f2; font-size: 9px; } /* Tamaño de fuente más pequeño para el encabezado */
                  h6 { font-size: 12px; margin: 0; }
                  p { font-size: 12px; margin: 5px 0; }
              </style>';

    // Agregar el título con el nombre de la categoría y el valor total
    $html .= '<h6 style="text-align: center;">Categoría: ' . $categoriaNombre . '</h6>';
    $html .= '<p style="text-align: center; font-weight: bold;">Valor Total de los Insumos: $' . number_format(floor($valorTotal), 0) . '</p>'; // Sin decimales
    $html .= '<table>';
    $html .= '<tr><th>Nombre del Insumo</th><th>INVIMA</th><th>Lote</th><th>Fecha</th><th>Marca</th><th>Presentación</th><th>Cantidad</th><th>Unitario</th><th>Subtotal</th></tr>';

    // Iterar sobre cada insumo y sus características
    foreach ($insumos as $insumo) {
      foreach ($insumo->caracteristicas as $caracteristica) {
        if ($caracteristica->cantidad > 0) {
          $subtotal = $caracteristica->cantidad * $caracteristica->valor_unitario;
          $html .= '<tr>';
          $html .= '<td>' . $insumo->nombre . '</td>';
          $html .= '<td>' . $caracteristica->invima . '</td>';
          $html .= '<td>' . $caracteristica->lote . '</td>';
          $html .= '<td>' . $caracteristica->vencimiento . '</td>';
          $html .= '<td>' . ($caracteristica->marca ? $caracteristica->marca->nombre : 'Sin Marca') . '</td>';
          $html .= '<td>' . ($caracteristica->presentacion ? $caracteristica->presentacion->nombre : 'Sin Presentación') . '</td>';
          $html .= '<td>' . $caracteristica->cantidad . '</td>';
          $html .= '<td>$' . number_format(floor($caracteristica->valor_unitario), 0) . '</td>'; // Sin decimales
          $html .= '<td>$' . number_format(floor($subtotal), 0) . '</td>'; // Sin decimales
          $html .= '</tr>';
        }
      }
    }
    $html .= '</table>';

    // Configurar Dompdf para renderizar HTML
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);

    // Ajustar el tamaño del papel y la orientación
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Descargar PDF
    return $dompdf->stream('Insumos_y_Caracteristicas.pdf');
  }

  public function exportToExcel(Request $request)
  {
    $idCategoria = $request->input('id_categoria');
    $categoriaNombre = $idCategoria ? Categoria::find($idCategoria)->nombre : 'Todas las categorías';

    $insumos = Insumo::with(['caracteristicas.marca', 'caracteristicas.presentacion'])
      ->when($idCategoria, function ($query, $id) {
        return $query->where('id_categoria', $id);
      })
      ->orderBy('nombre', 'asc')
      ->get();

    $valorTotal = 0;
    foreach ($insumos as $insumo) {
      foreach ($insumo->caracteristicas as $caracteristica) {
        if ($caracteristica->cantidad > 0) {
          $valorTotal += $caracteristica->cantidad * $caracteristica->valor_unitario;
        }
      }
    }

    $ivaGeneral = $valorTotal * 0.19;
    $valorConIvaGeneral = $valorTotal + $ivaGeneral;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Títulos principales
    $sheet->setCellValue('B2', 'Valor Total de Inventario: $' . number_format(floor($valorTotal), 0));
    $sheet->setCellValue('B3', 'IVA (19%): $' . number_format(floor($ivaGeneral), 0));
    $sheet->setCellValue('B4', 'Total con IVA: $' . number_format(floor($valorConIvaGeneral), 0));

    // Estilo para títulos principales
    $titleStyle = [
      'font' => ['bold' => true, 'size' => 14],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    ];
    $sheet->mergeCells('B2:D2');
    $sheet->mergeCells('B3:D3');
    $sheet->mergeCells('B4:D4');
    $sheet->getStyle('B2:D4')->applyFromArray($titleStyle);

    // Encabezados para la tabla de categorías
    $sheet->setCellValue('B6', 'Categoría');
    $sheet->setCellValue('C6', 'Valor');
    $sheet->setCellValue('D6', 'IVA (19%)');
    $sheet->setCellValue('E6', 'Total');

    // Estilo para los encabezados
    $headerStyle = [
      'font' => ['bold' => true],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
      'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFADD8E6'], // Color celeste claro
      ],
    ];
    $sheet->getStyle('B6:E6')->applyFromArray($headerStyle);

    $row = 7;
    $categorias = Categoria::all();
    $valorPorCategoria = [];

    foreach ($categorias as $categoria) {
      $valorPorCategoria[$categoria->nombre] = 0;

      foreach ($insumos as $insumo) {
        if ($insumo->id_categoria == $categoria->id) {
          foreach ($insumo->caracteristicas as $caracteristica) {
            if ($caracteristica->cantidad > 0) {
              $valorPorCategoria[$categoria->nombre] += $caracteristica->cantidad * $caracteristica->valor_unitario;
            }
          }
        }
      }

      $valor = $valorPorCategoria[$categoria->nombre];
      $iva = $valor * 0.19;
      $total = $valor + $iva;

      $sheet->setCellValue('B' . $row, $categoria->nombre);
      $sheet->setCellValue('C' . $row, number_format(floor($valor), 0));
      $sheet->setCellValue('D' . $row, number_format(floor($iva), 0));
      $sheet->setCellValue('E' . $row, number_format(floor($total), 0));

      $dataStyle = [
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
      ];
      $sheet->getStyle('B' . $row . ':E' . $row)->applyFromArray($dataStyle);

      $row++;
    }

    $sheet->setCellValue('B' . $row, 'Total General');
    $sheet->setCellValue('C' . $row, number_format(floor($valorTotal), 0));
    $sheet->setCellValue('D' . $row, number_format(floor($ivaGeneral), 0));
    $sheet->setCellValue('E' . $row, number_format(floor($valorConIvaGeneral), 0));

    $totalStyle = [
      'font' => ['bold' => true],
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
      'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
      'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FFFFE4B5'], // Color beige claro
      ],
    ];
    $sheet->getStyle('B' . $row . ':E' . $row)->applyFromArray($totalStyle);
    // Ajustar estilo para la tabla de categorías
    // Ajustar columnas automáticamente
    foreach (range('B', 'E') as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    $categoryStyle = [
      'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
      'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    ];
    $sheet->getStyle('B7:E' . $row)->applyFromArray($categoryStyle);

    // Tabla principal
    $sheet->setCellValue('A' . ($row + 2), 'Nombre del Insumo');
    $sheet->setCellValue('B' . ($row + 2), 'INVIMA');
    $sheet->setCellValue('C' . ($row + 2), 'Lote');
    $sheet->setCellValue('D' . ($row + 2), 'Fecha');
    $sheet->setCellValue('E' . ($row + 2), 'Marca');
    $sheet->setCellValue('F' . ($row + 2), 'Presentación');
    $sheet->setCellValue('G' . ($row + 2), 'Cantidad');
    $sheet->setCellValue('H' . ($row + 2), 'Unitario');
    $sheet->setCellValue('I' . ($row + 2), 'Subtotal');

    $sheet->getStyle('A' . ($row + 2) . ':I' . ($row + 2))->applyFromArray($headerStyle);

    $row += 3;
    foreach ($insumos as $insumo) {
      foreach ($insumo->caracteristicas as $caracteristica) {
        if ($caracteristica->cantidad > 0) {
          $subtotal = $caracteristica->cantidad * $caracteristica->valor_unitario;

          $sheet->setCellValue('A' . $row, $insumo->nombre);
          $sheet->setCellValue('B' . $row, $caracteristica->invima);
          $sheet->setCellValue('C' . $row, $caracteristica->lote);
          $sheet->setCellValue('D' . $row, $caracteristica->vencimiento);
          $sheet->setCellValue('E' . $row, $caracteristica->marca ? $caracteristica->marca->nombre : 'Sin Marca');
          $sheet->setCellValue('F' . $row, $caracteristica->presentacion ? $caracteristica->presentacion->nombre : 'Sin Presentación');
          $sheet->setCellValue('G' . $row, $caracteristica->cantidad);
          $sheet->setCellValue('H' . $row, number_format(floor($caracteristica->valor_unitario), 0));
          $sheet->setCellValue('I' . $row, number_format(floor($subtotal), 0));

          $dataStyle = [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
          ];
          $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray($dataStyle);

          foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
          }

          $row++;
        }
      }
    }

    $fileName = 'Inventario_Insumos.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($fileName);

    return response()->download($fileName)->deleteFileAfterSend(true);
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

  public function generarCodigosBarrasPDF()
  {
    // Obtener todos los insumos y ordenarlos alfabéticamente por nombre
    $insumos = Insumo::orderBy('nombre')->get();

    // Parámetros de tamaño de cada código de barras (en milímetros)
    $barcodeWidth = 45; // Ancho del código de barras
    $barcodeHeight = 25; // Alto del código de barras

    // Definir la cantidad de códigos por fila y por página
    $barcodesPerRow = 3; // Número de columnas
    $barcodesPerPage = 5 * $barcodesPerRow; // Total de códigos por página (filas * columnas)

    // Inicializar Dompdf
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);

    $dompdf = new \Dompdf\Dompdf($options);

    // Iniciar el HTML con los estilos
    $html = '<style>
              .page { 
                  page-break-after: always;
                  margin-top: 10mm;
                  margin-bottom: 0mm;
                  display: flex;
                  justify-content: center;
                  text-align: center;
              }
              .barcode-container {
                  display: inline-block;
                  width: ' . $barcodeWidth . 'mm;
                  height: ' . $barcodeHeight . 'mm;
                  text-align: center;
                  margin: 7mm 10mm 12mm 5mm;
              }
              .barcode {
                  width: 100%;
                  height: 70%;
              }
              .nombre-insumo {
                  text-align: center;
                  font-size: 8px;
                  margin-top: 2px;
                  font-weight: bold;
              }
              .codigo-insumo {
                  font-size: 12px;
                  letter-spacing: 15px;
                  font-weight: bold;
                  text-align: center;
              }
              .letra {
                  font-size: 30px;
                  font-weight: bold;
                  text-align: center;
                  margin-bottom: 30px;
              }
          </style>';

    $currentLetter = ''; // Para rastrear el cambio de letra
    $count = 0; // Contador de códigos de barras por página
    $mostrarLetra = true; // Bandera para mostrar la letra solo en la primera página del grupo

    foreach ($insumos as $insumo) {
      $codigo = $insumo->codigo;
      $nombreInsumo = $insumo->nombre;
      $letraActual = strtoupper(substr($nombreInsumo, 0, 1)); // Obtener la primera letra del nombre

      // Comprobar si se debe iniciar una nueva página para una nueva letra
      if ($letraActual !== $currentLetter) {
        if ($count > 0) {
          $html .= '</div>'; // Cerrar la página anterior
        }
        // Comenzar una nueva página para la nueva letra
        $html .= '<div class="page">';
        $html .= '<h1 class="letra"> ' . $letraActual . '</h1>'; // Mostrar la letra en la primera página del grupo
        $currentLetter = $letraActual; // Actualizar la letra actual
        $count = 0; // Reiniciar el contador para la nueva página
      }

      // Generar el código de barras usando Picqer
      $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
      $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

      // Añadir cada código de barras al HTML
      $html .= '
              <div class="barcode-container">
                  <p class="nombre-insumo">' . $nombreInsumo . '</p>
                  <img class="barcode" src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Código de Barras">
                  <p class="codigo-insumo">' . $codigo . '</p>
              </div>
          ';

      $count++;

      // Comprobar si se debe iniciar una nueva fila o una nueva página
      if ($count % $barcodesPerRow == 0) {
        $html .= '<br>'; // Crear una nueva fila
      }
      if ($count % $barcodesPerPage == 0) {
        $html .= '</div><div class="page">'; // Crear una nueva página
      }
    }

    $html .= '</div>'; // Cerrar la última página

    // Cargar el HTML en Dompdf
    $dompdf->loadHtml($html);

    // Configurar el tamaño del papel
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar el PDF
    $dompdf->render();

    // Descargar el PDF
    return $dompdf->stream('codigos_barras_completos.pdf', [
      'Attachment' => true
    ]);
  }



  public function generarCodigoBarrasPorInsumoPDF($id)
  {
    // Obtener el insumo por ID
    $insumo = Insumo::findOrFail($id);

    // Parámetros de tamaño de cada código de barras (en milímetros)
    $barcodeWidth = 40; // 6 cm de ancho
    $barcodeHeight = 20; // 3 cm de alto
    $barcodesPerRow = 3; // 4 columnas
    $barcodesPerPage = 18; // 24 códigos en total (6 filas)

    // Inicializar Dompdf
    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Arial');
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);

    $dompdf = new \Dompdf\Dompdf($options);

    // Comienza a crear el HTML para los códigos de barras
    $html = '<style>
                .page { 
                    page-break-after: always;
                    margin-top: 10mm; /* Margen superior */
                    margin-bottom: 0mm; /* Margen inferior */
                    display: flex;
                    flex-wrap: wrap; /* Permitir que los elementos se ajusten a múltiples líneas */
                    justify-content: center;
                    text-align: center;
                }
                .barcode-container {
                    display: inline-block;
                    width: ' . $barcodeWidth . 'mm;
                    height: ' . $barcodeHeight . 'mm;
                    text-align: center;
                    margin: 7mm 10mm 12mm 5mm; /* Espacio entre los códigos de barras */
                }
                .barcode {
                    width: 100%;
                    height: 70%; /* Ajusta para incluir nombre y código */
                }
                .nombre-insumo {
                    text-align: center;
                    font-size: 7px;
                    margin-top: 2px;
                    font-weight: bold;
                }
                .codigo-insumo {
                    font-size: 11px;
                    letter-spacing: 15px;
                    font-weight: bold;
                    text-align: center;
                }
            </style>';

    // Añadir la información del insumo y generar el código de barras
    $codigo = $insumo->codigo;
    $nombreInsumo = $insumo->nombre;

    // Generar el código de barras usando Picqer
    $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

    // Iniciar la primera página
    $html .= '<div class="page">';

    // Repetir el código de barras en un formato de 4 columnas y 6 filas (total de 24)
    for ($i = 0; $i < $barcodesPerPage; $i++) {
      // Añadir cada código de barras al HTML
      $html .= '
              <div class="barcode-container">
                  <p class="nombre-insumo">' . $nombreInsumo . '</p>
                  <img class="barcode" src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Código de Barras">
                  <p class="codigo-insumo">' . $codigo . '</p>
              </div>
          ';
    }

    $html .= '</div>'; // Cerrar la página

    // Cargar el HTML en Dompdf
    $dompdf->loadHtml($html);

    // Configurar el tamaño del papel
    $dompdf->setPaper('A4', 'portrait'); // A4 en orientación vertical

    // Renderizar el PDF
    $dompdf->render();

    // Descargar el PDF
    return $dompdf->stream('codigo_barras_' . $codigo . '.pdf', [
      'Attachment' => true
    ]);
  }
}
