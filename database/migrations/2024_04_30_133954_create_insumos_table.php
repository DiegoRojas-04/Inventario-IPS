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
        Schema::create('insumos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 80);
            $table->string('descripcion', 255)->nullable();
            $table->integer('stock')->unsigned()->default(0);
            $table->boolean('requiere_lote')->default(false);
            $table->boolean('requiere_invima')->default(false);
            $table->string('riesgo');
            $table->string('vida_util');
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('id_categoria')->nullable()->constrained('categorias')->cascadeOnDelete()->nullOnDelete();
            $table->foreignId('id_marca')->nullable()->constrained('marcas')->cascadeOnDelete()->nullOnDelete();
            $table->foreignId('id_presentacion')->nullable()->constrained('presentaciones')->cascadeOnDelete()->nullOnDelete();
            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insumos');
    }
};
