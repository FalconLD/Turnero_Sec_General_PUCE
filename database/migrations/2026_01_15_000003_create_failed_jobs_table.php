<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla para registrar los trabajos (jobs) que fallan en las colas.
     */
    public function up(): void
    {
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // Identificador único
            $table->string('uuid')->unique(); // Identificador único universal para el trabajo
            $table->text('connection'); // Conexión de la cola
            $table->text('queue'); // Nombre de la cola
            $table->longText('payload'); // Datos del trabajo
            $table->longText('exception'); // Detalle de la excepción/error
            $table->timestamp('failed_at')->useCurrent(); // Fecha y hora del fallo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_jobs');
    }
};
