<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kardex; // Asegúrate de importar tu modelo
use Carbon\Carbon;

class InsertarDatosDelMesSiguiente extends Command
{
    protected $signature = 'kardex:insertar-nuevos-datos';
    protected $description = 'Insertar nuevos datos del mes siguiente en la tabla Kardex';

    public function handle()
    {
        $hoy = Carbon::now();
        
        // Verifica si es el primer día del mes
        if ($hoy->isToday() && $hoy->day == 1) {
            // Obtener el mes anterior
            $mesAnterior = $hoy->subMonth(); // Obtiene el mes anterior
            
            // Lógica para obtener los datos del mes anterior
            $datosDelMesAnterior = Kardex::where('mes', $mesAnterior->month)
                ->where('anno', $mesAnterior->year)
                ->get();

            // Restablecer la fecha de hoy para usar en la inserción
            $hoy = Carbon::now(); // Reinicializa hoy para obtener el mes actual nuevamente

            if ($datosDelMesAnterior->isEmpty()) {
                $this->info('No se encontraron datos para el mes anterior.');
                return;
            }

            foreach ($datosDelMesAnterior as $dato) {
                Kardex::create([
                    'insumo_id' => $dato->insumo_id,
                    'mes' => $hoy->month, // Inserta el mes actual (10 en octubre)
                    'anno' => $hoy->year, // Asegúrate de que sea el año correcto
                    'cantidad_inicial' => $dato->saldo, // Asegúrate de que esta sea la cantidad que necesitas
                    'ingresos' => 0,
                    'egresos' => 0,
                    'saldo' => $dato->saldo, // Considera usar el saldo del mes anterior aquí
                ]);
            }

            $this->info('Datos del mes siguiente insertados correctamente.');
        } else {
            $this->info('No es el primer día del mes.');
        }
    }
}
