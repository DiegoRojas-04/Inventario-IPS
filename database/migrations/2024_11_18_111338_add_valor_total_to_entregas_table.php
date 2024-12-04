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
        Schema::table('entregas', function (Blueprint $table) {
            $table->decimal('valor_total', 15)->after('comprobante_id')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('entregas', function (Blueprint $table) {
            $table->dropColumn('valor_total');
        });
    }
    
};
