<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla maestra de horarios utilizando UUID.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            // Identificador único universal (UUID) como llave primaria
            $table->uuid('id_hor')->primary();

            // Definición de rangos de tiempo con zona horaria
            $table->timeTz('start_time'); // Hora de inicio de la jornada
            $table->timeTz('end_time');   // Hora de fin de la jornada

            // Fecha de activación del horario
            $table->dateTime('valid_from')->comment('Fecha en la que el horario entra en vigor');

            // Configuración de intervalos
            $table->unsignedInteger('break_minutes')->default(0)->comment('Duración del receso en minutos');
            $table->unsignedInteger('attention_minutes')->default(1)->comment('Duración de cada atención en minutos');

            $table->timestamps(); // Registros de auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
