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
    Schema::table('elementos', function (Blueprint $table) {
        $table->integer('cantidad_necesaria')->after('nombre');
    });
}

public function down()
{
    Schema::table('elementos', function (Blueprint $table) {
        $table->dropColumn('cantidad_necesaria');
    });
}

};
