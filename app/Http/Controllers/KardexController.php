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
        $selectedMonth = $request->input('mes', date('n')); // Mes actual por defecto
        $selectedYear = $request->input('anno', date('Y')); // Año actual por defecto

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
        $insumos = $query->paginate(50);

        // Calcular los datos del Kardex para cada insumo
        $insumos->getCollection()->transform(function ($insumo) use ($selectedMonth, $selectedYear) {
            // $insumo->cantidad_inicial_mes = $this->calcularCantidadInicialMes($insumo, $selectedMonth, $selectedYear);
            $insumo->cantidad_inicial_mes = $insumo->getCantidadInicialMes($selectedMonth, $selectedYear);
            $insumo->ingresos_mes = $insumo->ingresosDelMes($selectedMonth, $selectedYear); // Método que calcula los ingresos para el mes
            $insumo->egresos_mes = $insumo->egresosDelMes($selectedMonth, $selectedYear); // Método que calcula los egresos para el mes
            $insumo->saldo_final_mes = $insumo->cantidad_inicial_mes + $insumo->ingresos_mes - $insumo->egresos_mes; // Cálculo del saldo

            return $insumo;
        });

        // Pasar los datos a la vista
        return view('crud.kardex', compact('insumos', 'selectedMonth', 'selectedYear', 'categorias'));
    }

    public function ObtenerDatosParaExportar($request)
    {
        $selectedMonth = $request->input('mes');
        $selectedYear = $request->input('anno');
        $idCategoria = $request->input('id_categoria');

        $query = Insumo::with('detallesTransaccion', 'compras', 'entregas', 'caracteristicas.compras')
            ->orderBy('nombre', 'asc');

        if ($idCategoria) {
            $query->where('id_categoria', $idCategoria);
        }

        // Filtrar insumos actualizados en el mes y año seleccionado o con cantidades mayores a cero
        $query->where(function ($query) use ($selectedMonth, $selectedYear) {
            $query->whereHas('caracteristicas', function ($subQuery) use ($selectedMonth, $selectedYear) {
                $subQuery->whereMonth('updated_at', $selectedMonth)
                    ->whereYear('updated_at', $selectedYear)
                    ->orWhere('cantidad', '>', 0);
            });
        });

        $insumos = $query->get();

        $insumos->transform(function ($insumo) use ($selectedMonth, $selectedYear) {
            $insumo->cantidad_inicial_mes = $insumo->getCantidadInicialMes($selectedMonth, $selectedYear);
            $insumo->ingresos_mes = $insumo->ingresosDelMes($selectedMonth, $selectedYear);
            $insumo->egresos_mes = $insumo->egresosDelMes($selectedMonth, $selectedYear);
            $insumo->saldo_final_mes = $insumo->cantidad_inicial_mes + $insumo->ingresos_mes - $insumo->egresos_mes;

            // Filtrar características para mostrar solo las que son relevantes
            $insumo->caracteristicas = $insumo->caracteristicas->filter(function ($caracteristica) use ($selectedMonth, $selectedYear, $insumo) {
                $comprasDelMes = $caracteristica->compras ? $caracteristica->compras->filter(function ($compra) use ($selectedMonth, $selectedYear) {
                    return Carbon::parse($compra->fecha_hora)->month == $selectedMonth &&
                        Carbon::parse($compra->fecha_hora)->year == $selectedYear;
                }) : collect();

                // Incluir la característica si hay compras, si la cantidad es mayor a cero, o si fue actualizada en el mes filtrado
                $incluyeCaracteristica = $comprasDelMes->isNotEmpty() || $caracteristica->cantidad > 0;

                if ($caracteristica->cantidad == 0) {
                    $incluyeCaracteristica = $incluyeCaracteristica || (Carbon::parse($caracteristica->updated_at)->month == $selectedMonth &&
                        Carbon::parse($caracteristica->updated_at)->year == $selectedYear);
                }

                return $incluyeCaracteristica;
            });

            return $insumo;
        });

        // Filtrar insumos que no tengan características
        $insumos = $insumos->filter(function ($insumo) {
            return $insumo->caracteristicas->isNotEmpty(); // Retornar solo si hay características
        });

        return $insumos;
    }


    public function exportToExcel(Request $request)
    {
        $data = $this->ObtenerDatosParaExportar($request);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Obtener el nombre del mes y el año
        $selectedMonthNumber = $request->input('mes');
        $selectedYear = $request->input('anno');
        $selectedMonth = Carbon::create()->month($selectedMonthNumber)->format('F');

        // Escribir título encima de la tabla
        $titulo = 'Kardex Medicare IPS (' . $selectedMonth . ' ' . $selectedYear . ')';
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', $titulo);

        // Estilo para el título
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A1')->applyFromArray($titleStyle);

        // Escribir encabezados
        $sheet->setCellValue('A2', 'Nombre del Insumo');
        $sheet->setCellValue('B2', 'Marca');
        $sheet->setCellValue('C2', 'Presentación');
        $sheet->setCellValue('D2', 'INVIMA');
        $sheet->setCellValue('E2', 'Vencimiento');
        $sheet->setCellValue('F2', 'Lote');
        $sheet->setCellValue('G2', 'Inicio Mes');
        $sheet->setCellValue('H2', 'Ingresos Mes');
        $sheet->setCellValue('I2', 'Egresos Mes');
        $sheet->setCellValue('J2', 'Saldo Fin Mes');
        $sheet->setCellValue('K2', 'Cantidad Actual');

        // Estilo para los encabezados
        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFADD8E6'], // Azul claro
            ],
        ];
        $sheet->getStyle('A2:K2')->applyFromArray($headerStyle);

        // Escribir datos
        $row = 3;
        $previousItemName = null;
        $startRow = null;

        foreach ($data as $item) {
            $caracteristicas = $item->caracteristicas;

            foreach ($caracteristicas as $caracteristica) {
                if ($previousItemName === $item->nombre) {
                    // Combinar celdas si el nombre del insumo es el mismo
                    $sheet->setCellValue('B' . $row, $caracteristica['marca']->nombre);
                    $sheet->setCellValue('C' . $row, $caracteristica['presentacion']->nombre);
                    $sheet->setCellValue('D' . $row, $caracteristica['invima']);
                    $sheet->setCellValue('E' . $row, \Carbon\Carbon::parse($caracteristica['vencimiento'])->format('d-m-Y'));
                    $sheet->setCellValue('F' . $row, $caracteristica['lote']);
                    $sheet->setCellValue('K' . $row, $caracteristica['cantidad']);
                } else {
                    // Combinar las celdas de la fila anterior
                    if ($startRow !== null) {
                        $sheet->mergeCells("A{$startRow}:A" . ($row - 1));
                        $sheet->mergeCells("G{$startRow}:G" . ($row - 1));
                        $sheet->mergeCells("H{$startRow}:H" . ($row - 1));
                        $sheet->mergeCells("I{$startRow}:I" . ($row - 1));
                        $sheet->mergeCells("J{$startRow}:J" . ($row - 1));
                    }

                    // Actualizar el nombre del insumo y la fila inicial
                    $previousItemName = $item->nombre;
                    $startRow = $row;

                    // Escribir los valores en la nueva fila
                    $sheet->setCellValue('A' . $row, $item->nombre);
                    $sheet->setCellValue('B' . $row, $caracteristica['marca']->nombre);
                    $sheet->setCellValue('C' . $row, $caracteristica['presentacion']->nombre);
                    $sheet->setCellValue('D' . $row, $caracteristica['invima']);
                    $sheet->setCellValue('E' . $row, \Carbon\Carbon::parse($caracteristica['vencimiento'])->format('d-m-Y'));
                    $sheet->setCellValue('F' . $row, $caracteristica['lote']);
                    $sheet->setCellValue('G' . $row, $item->cantidad_inicial_mes);
                    $sheet->setCellValue('H' . $row, $item->ingresos_mes);
                    $sheet->setCellValue('I' . $row, $item->egresos_mes);
                    $sheet->setCellValue('J' . $row, $item->saldo_final_mes);
                    $sheet->setCellValue('K' . $row, $caracteristica['cantidad']);
                }
                $row++;
            }
        }

        // Combinar las celdas del último insumo procesado
        if ($startRow !== null) {
            $sheet->mergeCells("A{$startRow}:A" . ($row - 1));
            $sheet->mergeCells("G{$startRow}:G" . ($row - 1));
            $sheet->mergeCells("H{$startRow}:H" . ($row - 1));
            $sheet->mergeCells("I{$startRow}:I" . ($row - 1));
            $sheet->mergeCells("J{$startRow}:J" . ($row - 1));
        }

        // Ajustar el tamaño de las columnas automáticamente
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Estilo de la tabla
        $tableRange = 'A2:K' . ($row - 1);
        $sheet->getStyle($tableRange)->applyFromArray([
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
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
        $html .= '<p><strong>NUMERO DE FACTURA:</strong> ' . '</p>';
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
            $presentacion = $item->presentacion ? $item->presentacion->nombre : 'Sin presentación';
            // Solo añadir fila si cantidad a pedir es mayor que 0
            if ($cantidadPedir > 0) {
                $html .= '<tr>';
                $html .= '<td>' . $item->nombre . '</td>';
                $html .= '<td>' . $presentacion . '</td>'; // Aquí se muestra la presentación
                $html .= '<td class="cantidad">' . round($cantidadPedir) . '</td>';
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
        return $dompdf->stream('Pedido_Insumos_' . $categoriaNombre . '_' . $selectedMonth . '.pdf');
    }
}
