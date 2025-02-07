<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprobanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Insertar los registros en la tabla comprobantes
        DB::table('comprobantes')->insert([
            ['id' => 1, 'tipo_comprobante' => 'Entrega', 'estado' => 1],
            ['id' => 2, 'tipo_comprobante' => 'Compra', 'estado' => 1],
        ]);
    }
}
