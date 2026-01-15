<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Consolida la estructura base y la identificación por DNI.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Identificador único autoincremental
            $table->string('name'); // Nombre completo del usuario
            $table->string('email')->unique(); // Correo electrónico único
            $table->timestamp('email_verified_at')->nullable(); // Marca de tiempo de verificación

            // Campo DNI integrado directamente después de la verificación del correo
            $table->string('DNI')->nullable(); // Documento Nacional de Identidad

            $table->string('password'); // Contraseña encriptada
            $table->rememberToken(); // Token para sesión persistente
            $table->timestamps(); // Columnas created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
