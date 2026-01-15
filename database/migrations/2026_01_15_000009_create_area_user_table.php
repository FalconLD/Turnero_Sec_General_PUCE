<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla pivote para asignar usuarios a múltiples áreas operativas.
     */
    public function up(): void
    {
        Schema::create('area_user', function (Blueprint $table) {
            $table->id(); // Identificador único de la relación

            // Relación con el usuario (Operador/Psicólogo)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // Si el usuario se elimina, se borra la asignación

            // Relación con el área operativa
            $table->foreignId('operating_area_id')
                  ->constrained('operating_areas')
                  ->onDelete('cascade'); // Si el área se elimina, se borra la asignación

            $table->timestamps(); // Registros de cuándo se realizó la asignación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_user');
    }
};
