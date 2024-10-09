<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Insumo;

class AsignarCodigosInsumos extends Command
{
    protected $signature = 'insumos:asignar-codigos';
    protected $description = 'Asignar códigos únicos a los insumos existentes';

    public function handle()
    {
        $insumos = Insumo::whereNull('codigo')->get(); // Obtener insumos sin código

        foreach ($insumos as $insumo) {
            do {
                $codigo = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            } while (Insumo::where('codigo', $codigo)->exists()); // Verifica si el código ya existe

            $insumo->codigo = $codigo; // Asigna el código generado
            $insumo->save(); // Guarda el insumo
        }

        $this->info('Códigos asignados a los insumos existentes con éxito.');
    }
}
