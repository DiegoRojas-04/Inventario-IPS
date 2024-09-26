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
        Schema::create('consultorio_elemento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultorio_id')->constrained()->onDelete('cascade');
            $table->foreignId('elemento_id')->constrained()->onDelete('cascade');
            $table->integer('cantidad')->default(0);
            $table->string('observacion')->nullable();
            $table->string('estado')->default('bueno');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultorio_elemento');
    }
};
