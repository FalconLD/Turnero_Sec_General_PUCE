<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de cubículos consolidando ubicación y área operativa.
     */
    public function up(): void
    {
        Schema::create('cubiculos', function (Blueprint $table) {
            $table->id(); // Identificador único del cubículo
            $table->string('nombre'); // Nombre descriptivo (ej. Cubículo 1)

            // Definición del tipo de atención
            $table->string('tipo_atencion')->default('virtual');

            // Ubicación física o enlace de plataforma virtual
            $table->string('enlace_o_ubicacion')->nullable();

            // Relación con el Operador/Psicólogo responsable
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');

            // Relación con el Área Operativa
            $table->foreignId('operating_area_id')
                  ->constrained('operating_areas')
                  ->onDelete('cascade');

            $table->timestamps(); // Registros de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cubiculos');
    }
};
