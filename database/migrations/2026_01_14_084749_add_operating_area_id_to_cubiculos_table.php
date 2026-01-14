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
        Schema::table('cubiculos', function (Blueprint $table) {
            // Añadimos la columna después de user_id
            $table->unsignedBigInteger('operating_area_id')->nullable()->after('user_id');
            
            // Opcional: Crear la llave foránea real
            $table->foreign('operating_area_id')->references('id')->on('operating_areas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cubiculos', function (Blueprint $table) {
            //
        });
    }
};
