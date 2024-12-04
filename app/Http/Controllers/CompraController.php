<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Categoria;
use App\Models\Compra;
use Illuminate\Support\Facades\Auth;
use App\Models\Comprobante;
use App\Models\Insumo;
use App\Models\InsumoCaracteristica;
use App\Models\Kardex;
use App\Models\Marca;
use App\Models\Presentacione;
use App\Models\Proveedore;
use App\Models\Servicio;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $query = Compra::with(['insumos.caracteristicas' => function ($query) {
            $query->whereNotNull('valor_unitario');
        }])->where('user_id', auth()->id());

        // Verifica si se enviaron fechas en la solicitud
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $fechaInicio = Carbon::createFromFormat('Y-m-d', $request->input('fecha_inicio'))->startOfDay();
            $fechaFin = Carbon::createFromFormat('Y-m-d', $request->input('fecha_fin'))->endOfDay();
            $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
        }

        $compras = $query->latest()->paginate(20);

        // Calcular el valor total para cada compra
        foreach ($compras as $compra) {
            $compra->total_compra = $compra->insumos->flatMap(function ($insumo) use ($compra) {
                // Filtrar las características que pertenecen a la compra actual
                return $insumo->caracteristicas->where('compra_id', $compra->id)->map(function ($caracteristica) {
                    return $caracteristica->valor_unitario * $caracteristica->cantidad_compra;
                });
            })->sum();
        }


        return view('crud.compra.index', compact('compras'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $presentaciones = Presentacione::all();
        $marcas = Marca::all();
        $proveedores = Proveedore::all();
        $comprobantes = Comprobante::all();

        // Generar el siguiente número de comprobante
        $numero_comprobante = Compra::generarNumeroComprobante();
        $comprobanteCompra = Comprobante::where('tipo_comprobante', 'Compra')->first();

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Filtrar insumos según el rol del usuario
        if ($user->roles->contains('name', 'Administrador')) {
            // Si es Administrador, obtener todos los insumos
            $insumos = Insumo::all();
        } elseif ($user->roles->contains('name', 'Laboratorio')) {
            // Si es Laboratorio, obtener solo los insumos de la categoría 6
            $insumos = Insumo::where('id_categoria', 12)->get();
        } else {
            // Si es otro rol, manejar como consideres, aquí se regresará una colección vacía
            $insumos = collect(); // Sin insumos disponibles
        }

        return view('crud.compra.create', compact('insumos', 'proveedores', 'comprobantes', 'numero_comprobante', 'comprobanteCompra', 'marcas', 'presentaciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompraRequest $request)
    {
        try {
            DB::beginTransaction();

            // Crear la compra con los datos validados y el usuario autenticado
            $compra = new Compra(array_merge($request->validated(), ['user_id' => auth()->id()]));

            // Deshabilitar las marcas de tiempo automáticas
            $compra->timestamps = false;

            // Sobrescribir el campo created_at con la fecha y hora proporcionada
            $compra->created_at = $request->input('fecha_hora');

            // Guardar la compra con la fecha personalizada
            $compra->save();

            // Reactivar las marcas de tiempo automáticas
            $compra->timestamps = true;

            $arrayInsumo = $request->get('arrayidinsumo');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayCaracteristicas = $request->get('arraycaracteristicas');

            // Verificar que los arrays tengan la misma longitud
            if (count($arrayInsumo) !== count($arrayCantidad) || count($arrayInsumo) !== count($arrayCaracteristicas)) {
                return redirect()->back()->withErrors(['error' => 'Los datos de entrada son inconsistentes.']);
            }

            // Inicializar la variable para calcular el valor total de la compra
            $valorTotal = 0;

            // Recorrer cada insumo y actualizar su información
            foreach ($arrayInsumo as $key => $insumoId) {
                $insumo = Insumo::find($insumoId);

                $tieneCaracteristicas = isset($arrayCaracteristicas[$key]) && is_array($arrayCaracteristicas[$key])
                    && !empty(array_filter($arrayCaracteristicas[$key]));

                $valorUnitario = $arrayCaracteristicas[$key]['valor_unitario'] ?? 0;

                $valorTotal += $valorUnitario * $arrayCantidad[$key];

                if (!$tieneCaracteristicas) {
                    $compra->insumos()->attach($insumoId, ['cantidad' => $arrayCantidad[$key]]);
                } else {
                    $compra->insumos()->syncWithoutDetaching([$insumoId => ['cantidad' => $arrayCantidad[$key]]]);

                    $insumo->caracteristicas()->create([
                        'invima' => $arrayCaracteristicas[$key]['invima'] ?? null,
                        'lote' => $arrayCaracteristicas[$key]['lote'] ?? null,
                        'vencimiento' => $arrayCaracteristicas[$key]['vencimiento'] ?? null,
                        'id_marca' => $arrayCaracteristicas[$key]['id_marca'] ?? null,
                        'id_presentacion' => $arrayCaracteristicas[$key]['id_presentacion'] ?? null,
                        'cantidad' => $arrayCantidad[$key],
                        'cantidad_compra' => $arrayCantidad[$key],
                        'valor_unitario' => $valorUnitario,
                        'compra_id' => $compra->id,
                        'created_at' => $request->input('fecha_hora'), // Sobrescribe created_at
                    ]);
                    
                }

                $insumo->increment('stock', intval($arrayCantidad[$key]));

                $this->agregarEntradaKardex($insumo->id, $request->input('fecha'), intval($arrayCantidad[$key]));
            }

            $compra->valor_total = $valorTotal;
            $compra->save();

            DB::commit();
            return redirect('compra')->with('Mensaje', 'Compra registrada con éxito.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Ocurrió un error al procesar la solicitud.']);
        }
    }


    /**
     * Agregar una entrada al Kardex para un insumo específico.
     */
    private function agregarEntradaKardex($insumoId, $fecha, $cantidad)
    {
        $fechaCompra = Carbon::createFromFormat('Y-m-d', $fecha);
        $mesCompra = $fechaCompra->month;
        $annoCompra = $fechaCompra->year;

        // Verificar si ya existe un registro para este mes y año
        $registroExistente = Kardex::where('insumo_id', $insumoId)
            ->where('mes', $mesCompra)
            ->where('anno', $annoCompra)
            ->first();

        if ($registroExistente) {
            // Si ya existe, sumamos los ingresos
            $registroExistente->ingresos += $cantidad;
            $registroExistente->saldo += $cantidad;
            $registroExistente->save();
        } else {
            // Si no existe, creamos un nuevo registro
            Kardex::create([
                'insumo_id' => $insumoId,
                'mes' => $mesCompra,
                'anno' => $annoCompra,
                'cantidad_inicial' => 0, // Ajusta esto según la lógica de tu inventario
                'ingresos' => $cantidad,
                'saldo' => $cantidad,
            ]);
        }
    }

    /**
     * Display the specified resource.
     */

    public function show($id)
    {
        $compra = Compra::findOrFail($id);

        // Obtener los insumos con las características específicas de esa compra
        $insumosConCaracteristicas = $compra->insumos->map(function ($insumo) use ($compra) {
            $insumo->caracteristicasCompra = $insumo->caracteristicas()->where('compra_id', $compra->id)->get();
            return $insumo;
        });

        // Ordenar los insumos por nombre
        $insumosConCaracteristicas = $insumosConCaracteristicas->sortBy('nombre');

        // Calcular el valor total de la compra
        $totalCompra = $insumosConCaracteristicas->flatMap(function ($insumo) {
            return $insumo->caracteristicasCompra->map(function ($caracteristica) {
                return $caracteristica->valor_unitario * $caracteristica->cantidad_compra;
            });
        })->sum();

        return view('crud.compra.show', compact('compra', 'insumosConCaracteristicas', 'totalCompra'));
    }


    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $categoriaId = $request->get('categoria_id');

        // Obtener todas las categorías
        $categorias = Categoria::all();

        // Iniciar la consulta
        $query = InsumoCaracteristica::query()
            ->join('insumos', 'insumo_caracteristicas.insumo_id', '=', 'insumos.id')
            ->select('insumo_caracteristicas.*', 'insumos.nombre as insumo_nombre', 'insumos.id_categoria')
            ->with('insumo') // Asegúrate de que hay una relación 'insumo' definida en el modelo
            ->orderBy('insumo_caracteristicas.created_at', 'desc');

        // Filtrar por fecha
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('insumo_caracteristicas.created_at', [$fechaInicio, $fechaFin]);
        }

        // Filtrar por categoría si se proporciona
        if ($categoriaId) {
            $query->where('insumos.id_categoria', $categoriaId);
        }

        // Obtener los insumos y paginarlos
        $insumos = $query->paginate(100);

        // Calcular el valor total de compra
        $valorTotalCompra = $insumos->sum(function ($insumo) {
            return $insumo->valor_unitario * $insumo->cantidad_compra;
        });

        return view('crud.compra.estadisticas', compact('insumos', 'fechaInicio', 'fechaFin', 'categoriaId', 'categorias', 'valorTotalCompra'));
    }

    /**
     * Show the form for editing the specified resource.
     */

    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy(string $id)
    {
        //
    }

    public function exportToPdf($id)
    {
        $compra = Compra::with('proveedor', 'comprobante')->findOrFail($id);

        // Obtener los insumos con las características específicas de esa compra
        $insumosConCaracteristicas = $compra->insumos->map(function ($insumo) use ($compra) {
            $insumo->caracteristicasCompra = $insumo->caracteristicas()->where('compra_id', $compra->id)->get();
            if ($insumo->caracteristicasCompra->isEmpty()) {
                $insumo->caracteristicasCompra = collect(); // Asegurarse de que siempre sea una colección
            }
            return $insumo;
        });

        // Ordenar los insumos por nombre
        $insumosConCaracteristicas = $insumosConCaracteristicas->sortBy('nombre');

        // HTML para el contenido del PDF
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid black;
                padding: 5px;
                text-align: center;
                font-size: 11px; /* Reduce the font size */
            }
            th {
                background-color: #f2f2f2;
            }
            td {
                word-wrap: break-word;
            }
        </style>
        <h1 style="text-align: center;">Detalle de Compra</h1>
        <p><strong>Proveedor:</strong> ' . $compra->proveedor->nombre . '</p>
        <p><strong>Fecha:</strong> ' . \Carbon\Carbon::parse($compra->fecha_hora)->format('d-m-Y') . '</p>
        <p><strong>Hora:</strong> ' . \Carbon\Carbon::parse($compra->fecha_hora)->format('H:i:s') . '</p>
        <table>
            <thead>
                <tr>
                    <th style="width: 20%;">Producto</th>
                    <th style="width: 15%;">Marca</th>
                    <th style="width: 10%;">Presentación</th>
                    <th style="width: 15%;">Invima</th>
                    <th style="width: 10%;">Lote</th>
                    <th style="width: 10%;">Vencimiento</th>
                    <th style="width: 10%;">Cantidad</th>
                </tr>
            </thead>
            <tbody>';

        // Agregar los insumos y cantidades a la tabla
        foreach ($insumosConCaracteristicas as $insumo) {
            foreach ($insumo->caracteristicasCompra as $caracteristica) {
                $html .= '
                <tr>
                    <td>' . $insumo->nombre . '</td>
                    <td>' . ($caracteristica->marca ? $caracteristica->marca->nombre : 'Sin Marca') . '</td>
                    <td>' . ($caracteristica->presentacion ? $caracteristica->presentacion->nombre : 'Sin Presentación') . '</td>
                    <td>' . $caracteristica->invima . '</td>
                    <td>' . $caracteristica->lote . '</td>
                    <td>' . \Carbon\Carbon::parse($caracteristica->vencimiento)->format('d-m-Y') . '</td>
                    <td>' . $caracteristica->cantidad_compra . '</td>
                </tr>';
            }
        }

        $html .= '
            </tbody>
        </table>';

        // Configurar el PDF
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar el PDF
        return $dompdf->stream('Detalle_compra_' . $compra->proveedor->nombre . '.pdf');
    }
}
