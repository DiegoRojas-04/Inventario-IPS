<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Insumo;
use App\Models\Kardex;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Log; // Importa el facade Log
use PhpOffice\PhpWord\IOFactory;

class KardexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener el mes y el año seleccionados en el formulario
        $selectedMonth = $request->input('mes', date('n'));
        $selectedYear = $request->input('anno', date('Y'));

        // Obtener todas las categorías
        $categorias = Categoria::all();

        // Obtener los insumos con paginación
        $query = Insumo::with('detallesTransaccion', 'compras', 'entregas')->orderBy('nombre', 'asc');

        // Filtrar por categoría si se selecciona una
        if ($request->has('id_categoria')) {
            $idCategoria = $request->input('id_categoria');
            // Filtrar solo si se selecciona una categoría específica
            if ($idCategoria != "") {
                $query->where('id_categoria', $idCategoria);
            }
        }

        // Aplicar paginación
        $insumos = $query->paginate(20);

        // Calcular los datos del Kardex para cada insumo
        $insumos->getCollection()->transform(function ($insumo) use ($selectedMonth, $selectedYear) {
            $insumo->cantidad_inicial_mes = $this->calcularCantidadInicialMes($insumo, $selectedMonth, $selectedYear);
            $insumo->ingresos_mes = $insumo->ingresosDelMes($selectedMonth, $selectedYear);
            $insumo->egresos_mes = $insumo->egresosDelMes($selectedMonth, $selectedYear);
            $insumo->saldo_final_mes = $insumo->cantidad_inicial_mes + $insumo->ingresos_mes - $insumo->egresos_mes;
            return $insumo;
        });

        // Pasar los datos a la vista
        return view('crud.kardex', compact('insumos', 'selectedMonth', 'selectedYear', 'categorias'));
    }



    private function calcularCantidadInicialMes($insumo, $mes, $anno)
    {
        // Obtener el mes y año anterior
        $fecha = Carbon::createFromDate($anno, $mes, 1);
        $fechaAnterior = $fecha->subMonth();
        $mesAnterior = $fechaAnterior->month;
        $annoAnterior = $fechaAnterior->year;

        // Calcular el saldo final del mes anterior como la cantidad inicial del mes actual
        $kardexAnterior = $insumo->kardex()->where('mes', $mesAnterior)->where('anno', $annoAnterior)->first();
        return $kardexAnterior ? $kardexAnterior->saldo : 0;
    }

    public function ObtenerDatosParaExportar($request)
    {
        $selectedMonth = $request->input('mes');
        $selectedYear = $request->input('anno');
        $idCategoria = $request->input('id_categoria');

        $query = Insumo::with('detallesTransaccion', 'compras', 'entregas')->orderBy('nombre', 'asc');

        if ($idCategoria) {
            $query->where('id_categoria', $idCategoria);
        }

        $insumos = $query->get();

        $insumos->transform(function ($insumo) use ($selectedMonth, $selectedYear) {
            $insumo->cantidad_inicial_mes = $this->calcularCantidadInicialMes($insumo, $selectedMonth, $selectedYear);
            $insumo->ingresos_mes = $insumo->ingresosDelMes($selectedMonth, $selectedYear);
            $insumo->egresos_mes = $insumo->egresosDelMes($selectedMonth, $selectedYear);
            $insumo->saldo_final_mes = $insumo->cantidad_inicial_mes + $insumo->ingresos_mes - $insumo->egresos_mes;
            return $insumo;
        });

        return $insumos;
    }


    public function exportToExcel(Request $request)
    {
        $data = $this->ObtenerDatosParaExportar($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Obtener el nombre del mes
        $selectedMonthNumber = $request->input('mes');
        $selectedYear = $request->input('anno');
        $selectedMonth = Carbon::create()->month($selectedMonthNumber)->format('F');

        // Obtener el nombre de la categoría
        $idCategoria = $request->input('id_categoria');
        $categoriaNombre = $idCategoria ? Categoria::find($idCategoria)->nombre : '';

        // Escribir título encima de la tabla
        $titulo = 'Kardex Medicare IPS (' . $selectedMonth . ' ' . $selectedYear . ($categoriaNombre ? ' - ' . $categoriaNombre : '') . ')';
        $sheet->mergeCells('A1:E1');
        $sheet->setCellValue('A1', $titulo);

        // Estilo para el título
        $titleStyle = [
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Escribir encabezados
        $sheet->setCellValue('A2', 'Nombre del Insumo');
        $sheet->setCellValue('B2', 'Inicio Mes');
        $sheet->setCellValue('C2', 'Ingresos');
        $sheet->setCellValue('D2', 'Egresos');
        $sheet->setCellValue('E2', 'Saldo Fin');

        // Estilo para los encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFADD8E6'], // Azul claro
            ],
        ];
        $sheet->getStyle('A2:E2')->applyFromArray($headerStyle);

        // Escribir datos
        $row = 3;
        foreach ($data as $item) {
            $sheet->setCellValue('A' . $row, $item->nombre);
            $sheet->setCellValue('B' . $row, $item->cantidad_inicial_mes);
            $sheet->setCellValue('C' . $row, $item->ingresos_mes);
            $sheet->setCellValue('D' . $row, $item->egresos_mes);
            $sheet->setCellValue('E' . $row, $item->saldo_final_mes);

            // Estilo para los nombres de insumos en negrilla
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            // Alternar color de fondo de las filas
            $rowColor = $row % 2 == 0 ? 'FFADD8E6' : 'FFFFFFFF'; // Alternar entre blanco y azul claro
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $rowColor],
                ],
            ]);

            $row++;
        }

        // Ajustar el tamaño de las columnas automáticamente
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Establecer bordes a todas las celdas de la tabla
        $tableRange = 'A2:E' . ($row - 1);
        $sheet->getStyle($tableRange)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Configurar el nombre del archivo y descargar
        $filename = 'KardexMedicare.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        return response()->download($filename)->deleteFileAfterSend(true);
    }

    public function exportToPdf(Request $request)
    {
        $data = $this->ObtenerDatosParaExportar($request);

        // Ordenar por nombre del insumo alfabéticamente
        $data = $data->sortBy('nombre');

        // Obtener el nombre del mes y categoría
        $selectedMonthNumber = $request->input('mes');
        $selectedYear = $request->input('anno');
        $selectedMonth = Carbon::create()->month($selectedMonthNumber)->format('F');
        $idCategoria = $request->input('id_categoria');
        $categoriaNombre = $idCategoria ? Categoria::find($idCategoria)->nombre : '';

        // Crear el HTML para el PDF
        $titulo = 'Kardex Medicare IPS (' . $selectedMonth . ' ' . $selectedYear . ($categoriaNombre ? ' - ' . $categoriaNombre : '') . ')';
        $html = '<style>body { font-family: Arial, sans-serif; }';
        $html .= 'table { width: 100%; border-collapse: collapse; font-size: 12px; }';
        $html .= 'th, td { border: 1px solid black; text-align: center; padding: 8px; }';
        $html .= 'th { background-color: #f2f2f2; }';
        $html .= '</style>';
        $html .= '<h1 style="text-align: center;">' . $titulo . '</h1>';
        $html .= '<table>';
        $html .= '<tr><th>Nombre del Insumo</th><th>Inicio Mes</th><th>Ingresos</th><th>Egresos</th><th>Saldo Final</th></tr>';
        foreach ($data as $item) {
            $html .= '<tr>';
            $html .= '<td>' . $item->nombre . '</td>';
            $html .= '<td>' . round($item->cantidad_inicial_mes) . '</td>';
            $html .= '<td>' . round($item->ingresos_mes) . '</td>';
            $html .= '<td>' . round($item->egresos_mes) . '</td>';
            $html .= '<td>' . round($item->saldo_final_mes) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        // Configurar Dompdf para renderizar HTML
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);

        // Renderizar PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Descargar PDF
        return $dompdf->stream('Kardex_Medicare_IPS.pdf');
    }



    public function exportOrderToPdf(Request $request)
    {
        $data = $this->ObtenerDatosParaExportar($request);
    
        // Ordenar por nombre del insumo alfabéticamente
        $data = $data->sortBy('nombre');
    
        // Obtener el nombre del mes y categoría
        $selectedMonthNumber = $request->input('mes');
        $selectedYear = $request->input('anno');
        $selectedMonth = Carbon::create()->month($selectedMonthNumber)->format('F');
        $idCategoria = $request->input('id_categoria');
        $categoriaNombre = $idCategoria ? Categoria::find($idCategoria)->nombre : '';
    
        // Crear el HTML para el PDF
        $titulo = 'Pedido de Insumos - ' . $categoriaNombre;
        $html = '<style>
                    body { font-family: Arial, sans-serif; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 20px; }
                    th, td { border: 1px solid black; text-align: center; padding: 8px; }
                    th { background-color: #f2f2f2; }
                    .cantidad { color: red; font-weight: bold; }
                </style>';
        $html .= '<h2 style="text-align: center;">' . $titulo . '</h2>';
        $html .= '<p><strong>TIPO DE PEDIDO:</strong> ' . $categoriaNombre . '</p>';
        $html .= '<p><strong>PROVEEDOR:</strong> ' . '</p>';
        $html .= '<p><strong>FECHA DE PEDIDO:</strong> ' . $selectedMonth . ' ' . $selectedYear . '</p>';
        $html .= '<p><strong>NUMERO DE FACURA:</strong> ' . '</p>';
        $html .= '<br>';
        $html .= '<table>';
        $html .= '<tr><th>Nombre de Insumo</th><th>Presentación</th><th>Cantidad a Pedir</th></tr>';
    
        foreach ($data as $item) {
            // Calcular la cantidad a pedir
            $cantidadInicioMes = $item->cantidad_inicial_mes;
            $ingresos = $item->ingresos_mes;
            $egresos = $item->egresos_mes;
            $saldoFinal = $item->saldo_final_mes;
            $saldo = $cantidadInicioMes + $ingresos;
            $cantidadPedir = $saldo - $saldoFinal;
    
            // Añadir fila a la tabla
            $html .= '<tr>';
            $html .= '<td>' . $item->nombre . '</td>';
            $html .= '<td>' . $item->presentacione->nombre . '</td>';
            $html .= '<td class="cantidad">' . round($cantidadPedir) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
    
        // Configurar Dompdf para renderizar HTML
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
    
        // Renderizar PDF
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
    
        // Descargar PDF
        return $dompdf->stream('Pedido_Insumos_' . $categoriaNombre . '_' . $selectedMonth . '.pdf');
    }
}
