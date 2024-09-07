<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('insumo_caracteristicas', function (Blueprint $table) {
        $table->unsignedBigInteger('id_marca')->nullable()->after('compra_id');
        $table->unsignedBigInteger('id_presentacion')->nullable()->after('id_marca');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('insumo_caracteristicas', function (Blueprint $table) {
            //
        });
    }
};
