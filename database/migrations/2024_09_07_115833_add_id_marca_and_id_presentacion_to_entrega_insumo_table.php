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
        Schema::table('entrega_insumo', function (Blueprint $table) {
            $table->foreignId('id_marca')->nullable()->after('cantidad')->constrained('marcas')->onDelete('set null');
            $table->foreignId('id_presentacion')->nullable()->after('id_marca')->constrained('presentaciones')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('entrega_insumo', function (Blueprint $table) {
            $table->dropForeign(['id_marca']);
            $table->dropColumn('id_marca');
            $table->dropForeign(['id_presentacion']);
            $table->dropColumn('id_presentacion');
        });
    }
};
