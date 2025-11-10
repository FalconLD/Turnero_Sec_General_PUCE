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
        // Laravel crea la tabla 'faculties' (plural)
        Schema::create('faculties', function (Blueprint $table) {
            
            // --- Columnas estándar ---
            $table->id(); // Crea una columna 'id' auto-incremental (Primary Key)

            // --- columnas personalizadas ---
            $table->string('facultad');       // columna "FACULTAD"
            $table->string('programa_desc');  // columna "PROGRAMA_DESC"
            $table->string('nivel');          // columna "NIVEL"

            // --- Columnas estándar (opcionales pero recomendadas) ---
            $table->timestamps(); // Crea las columnas 'created_at' y 'updated_at'
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