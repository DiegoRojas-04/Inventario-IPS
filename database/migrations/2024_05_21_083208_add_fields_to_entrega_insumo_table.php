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
            $table->string('invima')->nullable()->after('cantidad'); 
            $table->string('lote')->nullable()->after('invima'); 
            $table->date('vencimiento')->nullable()->after('lote');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrega_insumo', function (Blueprint $table) {
            $table->dropColumn('invima');
            $table->dropColumn('lote');
            $table->dropColumn('vencimiento');
        });
    }
};
