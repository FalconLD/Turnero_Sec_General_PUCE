<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de facultades que encabeza la jerarquía académica.
     */
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id(); // Identificador único

            // Columnas de información académica
            $table->string('facultad');       // Nombre de la facultad
            $table->string('programa_desc');  // Descripción del programa o carrera
            $table->string('nivel');          // Nivel académico (Grado, Posgrado, etc.)

            $table->timestamps(); // Registros de creación y edición
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
