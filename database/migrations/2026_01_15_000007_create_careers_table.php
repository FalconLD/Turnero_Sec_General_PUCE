<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de carreras vinculadas a las áreas operativas.
     */
    public function up(): void
    {
        Schema::create('careers', function (Blueprint $table) {
            $table->id(); // Identificador único de la carrera
            $table->string('name'); // Nombre oficial de la carrera
            $table->string('career_code')->nullable(); // Código identificador opcional

            // Relación con el área operativa: La tabla 'operating_areas' debe existir previamente
            $table->foreignId('operating_area_id')
                  ->constrained('operating_areas') // Enlace a la tabla de áreas
                  ->onDelete('cascade');           // Limpieza automática al borrar el área

            $table->timestamps(); // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
