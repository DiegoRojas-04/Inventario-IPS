<?php

namespace App\Http\Controllers;

use App\Models\Entrega;
use Illuminate\Http\Request;
use App\Models\Pedido;
use App\Models\Insumo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::with('user')->latest()->get();
        return view('crud.pedido.index', compact('pedidos'));
    }

    // Dentro de tu controlador PedidoController
    public function show($id)
    {
        $pedido = Pedido::with('insumos', 'user')->findOrFail($id);
        $servicio = $pedido->user->servicio;
        $observacion = $pedido->observacion; 

        // Verificar que el usuario tenga un servicio
        if (!$servicio) {
            return redirect()->back()->withErrors(['msg' => 'El usuario no tiene un servicio asociado.']);
        }

        // Obtener la última entrega para cada insumo en la semana pasada
        $insumosConUltimaEntrega = $pedido->insumos->map(function ($insumo) use ($servicio) {
            $ultimaEntregaSemanaPasada = Entrega::where('servicio_id', $servicio->id)
                ->whereHas('insumos', function ($query) use ($insumo) {
                    $query->where('insumo_id', $insumo->id);
                })
                ->whereBetween('fecha_hora', [now()->subWeek(), now()])
                ->orderBy('fecha_hora', 'desc')
                ->first();

            // Pasar la entrega a los insumos
            $insumo->ultima_entrega = $ultimaEntregaSemanaPasada ? $ultimaEntregaSemanaPasada->insumos->find($insumo->id) : null;

            return $insumo;
        });

        return view('crud.pedido.show', compact('pedido', 'insumosConUltimaEntrega','observacion'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'insumos' => 'required|string',
            'cantidades' => 'required|string',
            'restantes' => 'required|string',
            'observacion' => 'nullable|min:3',
            'esPedidoEspecial' => 'nullable|boolean', // Validar si es un pedido especial
        ]);

        // Obtener si es un pedido especial desde el checkbox
        $esPedidoEspecial = $request->input('esPedidoEspecial', false); // False si no está presente

        $insumos = json_decode($data['insumos'], true);
        $cantidades = json_decode($data['cantidades'], true);
        $restantes = json_decode($data['restantes'], true);

        if (!is_array($insumos) || !is_array($cantidades) || !is_array($restantes)) {
            return redirect()->back()->withErrors(['msg' => 'Los datos de insumos, cantidades y restantes no son válidos.']);
        }

        $pedido = new Pedido;
        $pedido->fecha_hora = now();
        $pedido->user_id = auth()->id();
        $pedido->estado = 1;
        $pedido->observacion = $data['observacion'];

        // Establecer el tipo de pedido basado en el valor del checkbox
        if ($esPedidoEspecial) {
            $pedido->tipo = 'Pedido Especial';
        } else {
            $pedido->tipo = 'Pedido';
        }

        $pedido->save();

        for ($i = 0; $i < count($insumos); $i++) {
            $pedido->insumos()->attach($insumos[$i], [
                'cantidad' => $cantidades[$i],
                'restante' => $restantes[$i]
            ]);
        }

        return redirect('home')->with('Mensaje', 'Pedido guardado exitosamente.');
    }

    public function create(Request $request)
    {
        // Verificar si el parámetro especial fue pasado en la URL
        $esPedidoEspecial = $request->query('especial', false);
        $insumos = Insumo::orderBy('nombre', 'asc')->get();

        // Pasar el valor de $esPedidoEspecial a la vista
        return view('crud.pedido.create', compact('insumos', 'esPedidoEspecial'));
    }


    public function exportToPdf($id)
    {
        $pedido = Pedido::with('insumos', 'user')->findOrFail($id);
        $servicio = $pedido->user->servicio;

        if (!$servicio) {
            return redirect()->back()->withErrors(['msg' => 'El usuario no tiene un servicio asociado.']);
        }

        $insumosConUltimaEntrega = $pedido->insumos->map(function ($insumo) use ($servicio) {
            $ultimaEntregaSemanaPasada = Entrega::where('servicio_id', $servicio->id)
                ->whereHas('insumos', function ($query) use ($insumo) {
                    $query->where('insumo_id', $insumo->id);
                })
                ->whereBetween('fecha_hora', [now()->subWeek(), now()])
                ->orderBy('fecha_hora', 'desc')
                ->first();

            $insumo->ultima_entrega = $ultimaEntregaSemanaPasada ? $ultimaEntregaSemanaPasada->insumos->find($insumo->id) : null;
            $insumo->cantidad_anterior = $insumo->ultima_entrega ? $insumo->ultima_entrega->pivot->cantidad : 'Sin pedido';

            return $insumo;
        });

        $insumosOrdenados = $insumosConUltimaEntrega->sortBy('nombre');

        // HTML para el contenido del PDF
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
            }
            .cantidad {
                color: red;
                font-weight: bold;
            }
        </style>
        <h1 style="text-align: center;">Detalles del Pedido</h1>
        <p><strong>Usuario:</strong> ' . $pedido->user->name . '</p>
        <p><strong>Fecha:</strong> ' . \Carbon\Carbon::parse($pedido->fecha_hora)->format('d-m-Y') . '</p>
        <p><strong>Hora:</strong> ' . \Carbon\Carbon::parse($pedido->fecha_hora)->format('H:i:s') . '</p>
        <p><strong>Observación:</strong> ' . $pedido->observacion . '</p>
        <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; text-align: center;">
            <thead>
                <tr>
                    <th>Insumo</th>
                    <th>Anterior</th>
                    <th>Restante</th>
                    <th>Cantidad</th>
                    <th>Check</th>
                    <th>Lote-Inv</th>

                </tr>
            </thead>
            <tbody>';

        foreach ($insumosOrdenados as $insumo) {
            $html .= '
            <tr>
                <td>' . $insumo->nombre . '</td>
                <td>' . ($insumo->cantidad_anterior === 'Sin pedido' ? 'Sin pedido' : $insumo->cantidad_anterior) . '</td>
                <td>' . $insumo->pivot->restante . '</td>
                <td class="cantidad">' . $insumo->pivot->cantidad . '</td>
                <td></td>
                <td></td>

            </tr>';
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
        return $dompdf->stream('Detalle_Pedido_' . $pedido->user->name . '.pdf');
    }
}
