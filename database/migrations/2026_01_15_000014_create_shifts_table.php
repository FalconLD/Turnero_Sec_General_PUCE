<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de turnos (Shifts) vinculando cubículos, horarios y estudiantes.
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id_shift')->primary(); // Identificador único del turno
            $table->uuid('schedule_shift');      // Relación con el horario maestro
            $table->unsignedBigInteger('cubicle_shift'); // Relación con el cubículo de atención

            $table->date('date_shift');          // Fecha específica del turno
            $table->timeTz('start_shift');       // Hora exacta de inicio
            $table->timeTz('end_shift');         // Hora exacta de finalización

            // Vinculación con el estudiante mediante la cédula
            $table->string('person_shift', 36)->nullable();

            $table->smallInteger('status_shift')->default(1); // Estado del turno (1: Disponible, etc.)
            $table->timestamps(); // Auditoría de creación

            // Definición de Claves Foráneas
            $table->foreign('cubicle_shift')
                  ->references('id')->on('cubiculos')
                  ->onDelete('cascade');

            $table->foreign('person_shift')
                  ->references('cedula')->on('student_registrations')
                  ->onDelete('set null'); // Si se borra el registro, el turno queda libre pero existe el historial

            $table->foreign('schedule_shift')
                  ->references('id_hor')->on('schedules')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
