<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



class CreateKardexTable extends Migration
{
    public function up()
    {
        Schema::create('kardexes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('insumo_id')->constrained()->onDelete('cascade');
            $table->integer('mes');
            $table->integer('anno');
            $table->decimal('cantidad_inicial', 10, 2)->default(0);
            $table->decimal('ingresos', 10, 2)->default(0);
            $table->decimal('egresos', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kardexes');
    }
}
