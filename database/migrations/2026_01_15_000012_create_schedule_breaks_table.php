<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Define los periodos de descanso vinculados a un horario maestro.
     */
    public function up(): void
    {
        Schema::create('schedule_breaks', function (Blueprint $table) {
            $table->id(); // Identificador único del registro de descanso

            // Relación con la tabla de horarios (UUID)
            $table->uuid('schedule_id');

            // Horas que delimitan el receso
            $table->time('start_break'); // Inicio del descanso
            $table->time('end_break');   // Fin del descanso

            $table->timestamps(); // Registros de creación y edición

            // Definición de la llave foránea
            $table->foreign('schedule_id')
                ->references('id_hor')
                ->on('schedules')
                ->onDelete('cascade'); // Borrado automático si el horario desaparece
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_breaks');
    }
};
