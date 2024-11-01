<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUbicacionIdToActivosTable extends Migration
{
    // public function up()
    // {
    //     Schema::table('activos', function (Blueprint $table) {
    //         $table->foreignId('ubicacion_id')->constrained('ubicaciones')->onDelete('cascade');
    //     });
    // }

    // public function down()
    // {
    //     Schema::table('activos', function (Blueprint $table) {
    //         $table->dropForeign(['ubicacion_id']);
    //         $table->dropColumn('ubicacion_id');
    //     });
    // }
}