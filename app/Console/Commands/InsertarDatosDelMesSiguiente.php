<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kardex;
use Carbon\Carbon;

class InsertarDatosDelMesSiguiente extends Command
{
    protected $signature = 'kardex:insertar-nuevos-datos';
    protected $description = 'Insertar nuevos datos del mes siguiente en la tabla Kardex';

    public function handle()
    {
        $hoy = Carbon::now();
        
        // Verifica si es el día 1, 2, 3, 4 o 5 del mes
        if ($hoy->day >= 1 && $hoy->day <= 5) {
            
            // Verifica si ya se ha insertado en el mes actual
            $yaInsertadoEsteMes = Kardex::where('mes', $hoy->month)
                ->where('anno', $hoy->year)
                ->exists();

            if ($yaInsertadoEsteMes) {
                $this->info('Ya se insertaron datos este mes. No se volverá a insertar.');
                return;
            }

            // Obtener el mes anterior
            $mesAnterior = $hoy->copy()->subMonth();

            // Lógica para obtener los datos del mes anterior
            $datosDelMesAnterior = Kardex::where('mes', $mesAnterior->month)
                ->where('anno', $mesAnterior->year)
                ->get();

            if ($datosDelMesAnterior->isEmpty()) {
                $this->info('No se encontraron datos para el mes anterior.');
                return;
            }

            foreach ($datosDelMesAnterior as $dato) {
                Kardex::create([
                    'insumo_id' => $dato->insumo_id,
                    'mes' => $hoy->month,
                    'anno' => $hoy->year,
                    'cantidad_inicial' => $dato->saldo,
                    'ingresos' => 0,
                    'egresos' => 0,
                    'saldo' => $dato->saldo,
                ]);
            }

            $this->info('Datos del mes siguiente insertados correctamente.');
        } else {
            $this->info('Hoy no es un día válido para la inserción.');
        }
    }
}
