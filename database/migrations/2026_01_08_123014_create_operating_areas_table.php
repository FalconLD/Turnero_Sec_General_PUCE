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
        Schema::create('operating_areas', function (Blueprint $table) {
            $table->id();
            // Relación con la facultad (Asegúrate de que la tabla 'faculties' ya exista)
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
            $table->string('name'); // Ej: Secretaría, Bienestar Estudiantil, etc.
            $table->text('description')->nullable(); // Para detalles adicionales
            $table->timestamps();
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
