<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompraRequest;
use App\Models\Categoria;
use App\Models\Compra;
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
        $query = Compra::query();

        // Verifica si se enviaron fechas en la solicitud
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            // Convierte las fechas de texto en objetos Carbon para poder compararlas
            $fechaInicio = Carbon::createFromFormat('Y-m-d', $request->input('fecha_inicio'))->startOfDay();
            $fechaFin = Carbon::createFromFormat('Y-m-d', $request->input('fecha_fin'))->endOfDay();

            // Filtra las compras dentro del rango de fechas seleccionado
            $query->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);
        }

        $compras = $query->latest()->paginate(10);

        return view('crud.compra.index', compact('compras'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $presentaciones = Presentacione::all();
        $marcas = Marca::all();
        $insumos = Insumo::all();
        $proveedores = Proveedore::all();
        $comprobantes = Comprobante::all();

        // Generar el siguiente número de comprobante
        $numero_comprobante = Compra::generarNumeroComprobante();
        $comprobanteCompra = Comprobante::where('tipo_comprobante', 'Compra')->first();

        return view('crud.compra.create', compact('insumos', 'proveedores', 'comprobantes', 'numero_comprobante', 'comprobanteCompra', 'marcas', 'presentaciones'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompraRequest $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();

            $compra = Compra::create($request->validated());
            $arrayInsumo = $request->get('arrayidinsumo');
            $arrayCantidad = $request->get('arraycantidad');
            $arrayCaracteristicas = $request->get('arraycaracteristicas');

            // Verificar que todos los arrays tengan la misma longitud
            if (count($arrayInsumo) !== count($arrayCantidad) || count($arrayInsumo) !== count($arrayCaracteristicas)) {
                return redirect()->back()->withErrors(['error' => 'Los datos de entrada son inconsistentes.']);
            }

            foreach ($arrayInsumo as $key => $insumoId) {
                $insumo = Insumo::find($insumoId);

                // Verificar si el insumo tiene características
                $tieneCaracteristicas = isset($arrayCaracteristicas[$key]) && is_array($arrayCaracteristicas[$key])
                    && !empty(array_filter($arrayCaracteristicas[$key]));

                if (!$tieneCaracteristicas) {
                    // Relación sin características
                    $compra->insumos()->attach($insumoId, ['cantidad' => $arrayCantidad[$key]]);
                    $insumo->increment('stock', intval($arrayCantidad[$key]));
                } else {
                    // Relación con características
                    $compra->insumos()->syncWithoutDetaching([$insumoId => ['cantidad' => $arrayCantidad[$key]]]);
                    $insumo->increment('stock', intval($arrayCantidad[$key]));

                    $insumo->caracteristicas()->create([
                        'invima' => $arrayCaracteristicas[$key]['invima'] ?? null,
                        'lote' => $arrayCaracteristicas[$key]['lote'] ?? null,
                        'vencimiento' => $arrayCaracteristicas[$key]['vencimiento'] ?? null,
                        'id_marca' => $arrayCaracteristicas[$key]['id_marca'] ?? null,
                        'id_presentacion' => $arrayCaracteristicas[$key]['id_presentacion'] ?? null,
                        'cantidad' => $arrayCantidad[$key],
                        'cantidad_compra' => $arrayCantidad[$key],
                        'compra_id' => $compra->id,
                    ]);
                }

                // Agregar entrada al kardex
                $this->agregarEntradaKardex($insumo->id, $request->input('fecha'), intval($arrayCantidad[$key]));
            }

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

        // Verificar si ya existe un registro para este mes
        $registroExistente = Kardex::where('insumo_id', $insumoId)
            ->where('mes', $mesCompra)
            ->where('anno', $annoCompra)
            ->first();

        if ($registroExistente) {
            // Si existe, simplemente agrega una nueva entrada
            $registroExistente->ingresos += $cantidad;
            $registroExistente->saldo += $cantidad;
            $registroExistente->save();
        } else {
            // Si no existe, crea un nuevo registro
            Kardex::create([
                'insumo_id' => $insumoId,
                'mes' => $mesCompra,
                'anno' => $annoCompra,
                'cantidad_inicial' => 0, // Debes ajustar esto según tu lógica
                'ingresos' => $cantidad,
                'saldo' => $cantidad,
                // Otros campos del Kardex
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

        return view('crud.compra.show', compact('compra', 'insumosConCaracteristicas'));
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
        $compra = Compra::with('insumos.marca', 'insumos.presentacione', 'proveedor', 'comprobante')->findOrFail($id);

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
        }
    </style>
    <h1 style="text-align: center;">Detalle de Compra</h1>
    <p><strong>Proveedor:</strong> ' . $compra->proveedor->nombre . '</p>
    <p><strong>Fecha:</strong> ' . \Carbon\Carbon::parse($compra->fecha_hora)->format('d-m-Y') . '</p>
    <p><strong>Hora:</strong> ' . \Carbon\Carbon::parse($compra->fecha_hora)->format('H:i:s') . '</p>
    <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; text-align: center;">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Marca</th>
                <th>Presentación</th>
                <th>Invima</th>
                <th>Lote</th>
                <th>Vencimiento</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>';

        // Agregar los insumos y cantidades a la tabla
        foreach ($insumosConCaracteristicas as $insumo) {
            foreach ($insumo->caracteristicasCompra as $caracteristica) {
                $html .= '
            <tr>
                <td>' . $insumo->nombre . '</td>
                <td>' . $insumo->marca->nombre . '</td>
                <td>' . $insumo->presentacione->nombre . '</td>
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
