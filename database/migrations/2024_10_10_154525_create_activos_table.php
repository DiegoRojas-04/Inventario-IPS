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
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 5)->unique();
            $table->string('nombre'); 
            $table->foreignId('categoria_id')->constrained('categoria_activos')->onDelete('cascade'); // Clave foránea
            $table->string('modelo')->nullable(); // Modelo del activo
            $table->string('serie')->nullable(); // Número de serie (nullable)
            $table->string('marca')->nullable(); // Marca del activo (nullable)
            $table->integer('cantidad'); // Cantidad del activo
            $table->string('medida')->nullable(); // Medida del activo
            $table->string('estado'); // Estado del activo (en uso, en reparación, etc.)
            $table->text('observacion')->nullable(); // Observaciones (nullable)
            $table->tinyInteger('condicion')->default(1);
            $table->timestamps(); // Created at y Updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('activos');
    }
};
