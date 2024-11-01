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
            $table->decimal('valor_unitario', 10, 0)->nullable()->after('cantidad');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrega_insumo', function (Blueprint $table) {
            $table->dropColumn('valor_unitario');
        });
    }
};
