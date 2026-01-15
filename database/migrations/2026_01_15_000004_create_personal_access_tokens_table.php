<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla para gestionar tokens de acceso personal (API tokens).
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id(); // Identificador único
            $table->morphs('tokenable'); // Relación polimórfica (permite tokens para usuarios u otros modelos)
            $table->string('name'); // Nombre descriptivo del token
            $table->string('token', 64)->unique(); // El token encriptado único
            $table->text('abilities')->nullable(); // Permisos o alcances del token
            $table->timestamp('last_used_at')->nullable(); // Registro de última actividad
            $table->timestamp('expires_at')->nullable(); // Fecha de expiración
            $table->timestamps(); // Registros de creación y edición
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
