<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateElementosTable extends Migration
{
    public function up()
{
    Schema::create('elementos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('categoria')->nullable();
        $table->string('descripcion')->nullable();     
        $table->tinyInteger('estado')->default(1);
        $table->timestamps();
    });
}


    public function down()
    {
        Schema::dropIfExists('elementos');
    }
}
