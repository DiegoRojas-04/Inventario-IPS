<?php

namespace App\Http\Controllers;

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
    public function show($id)
    {
        $pedido = Pedido::with(['insumos' => function ($query) {
            $query->orderBy('nombre', 'asc');
        }])->findOrFail($id);

        return view('crud.pedido.show', compact('pedido'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'insumos' => 'required|string',
            'cantidades' => 'required|string',
            'restantes' => 'required|string',
            'observacion' => 'nullable|min:3' // Validar observacion
        ]);

        $insumos = json_decode($data['insumos'], true);
        $cantidades = json_decode($data['cantidades'], true);
        $restantes = json_decode($data['restantes'], true);

        // Verifica que insumos, cantidades y restantes son arrays
        if (!is_array($insumos) || !is_array($cantidades) || !is_array($restantes)) {
            return redirect()->back()->withErrors(['msg' => 'Los datos de insumos, cantidades y restantes no son válidos.']);
        }

        $pedido = new Pedido;
        $pedido->fecha_hora = now();
        $pedido->user_id = auth()->id();
        $pedido->estado = 1;
        $pedido->observacion = $data['observacion']; // Asigna la observación
        $pedido->save();

        for ($i = 0; $i < count($insumos); $i++) {
            $pedido->insumos()->attach($insumos[$i], [
                'cantidad' => $cantidades[$i],
                'restante' => $restantes[$i]
            ]);
        }

        return redirect('home')->with('Mensaje', 'Insumo');
    }


    public function create()
    {
        $insumos = Insumo::all();
        return view('crud.pedido.create', compact('insumos'));
    }


    public function exportToPdf($id)
    {
        $pedido = Pedido::with('insumos', 'user')->findOrFail($id);
        $insumosOrdenados = $pedido->insumos->sortBy('nombre');

        // HTML para el contenido del PDF
        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
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
                    <th>Restante</th>
                    <th>Cantidad</th>
                    <th>Check</th>
                </tr>
            </thead>
            <tbody>';

        // Agregar los insumos y cantidades a la tabla
        foreach ($insumosOrdenados as $insumo) {
            $html .= '
            <tr>
                <td>' . $insumo->nombre . '</td>
                <td>' . $insumo->pivot->restante . '</td>
                <td>' . $insumo->pivot->cantidad . '</td>
                <td>' . '' . '</td>
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
