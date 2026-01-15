<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de áreas operativas vinculadas a las facultades.
     */
    public function up(): void
    {
        Schema::create('operating_areas', function (Blueprint $table) {
            $table->id(); // Identificador único

            // Relación con la facultad: Es obligatorio que la tabla 'faculties' ya exista
            $table->foreignId('faculty_id')
                  ->constrained('faculties') // Referencia a la tabla maestra
                  ->onDelete('cascade');     // Si se borra la facultad, se borran sus áreas

            $table->string('name'); // Nombre del área (ej. Secretaría, Bienestar)
            $table->text('description')->nullable(); // Detalles adicionales opcionales

            $table->timestamps(); // Registros de auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_areas');
    }
};
