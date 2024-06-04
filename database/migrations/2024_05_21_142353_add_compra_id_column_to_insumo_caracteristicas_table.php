<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('insumo_caracteristicas', function (Blueprint $table) {
            $table->unsignedBigInteger('compra_id')->nullable()->after('insumo_id');
            // No agregamos la restricción de clave foránea aquí
        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('insumo_caracteristicas', function (Blueprint $table) {
            $table->dropColumn('compra_id');
        });
    }
};
