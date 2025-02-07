<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Cambiar el nombre de la columna 'nombre' a 'ubicacion_general'
        DB::statement('ALTER TABLE activos CHANGE nombre ubicacion_general VARCHAR(255)');
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Volver a cambiar el nombre de la columna 'ubicacion_general' a 'nombre'
        DB::statement('ALTER TABLE activos CHANGE ubicacion_general nombre VARCHAR(255)');
    }
};
