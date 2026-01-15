<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Vincula fechas específicas con los horarios maestros (Schedules).
     */
    public function up(): void
    {
        Schema::create('days', function (Blueprint $table) {
            // Clave foránea de tipo UUID que apunta a schedules
            $table->uuid('schedule_day');

            // Fecha específica del día de atención
            $table->date('date_day');

            // Definición de la relación de integridad
            $table->foreign('schedule_day')
                  ->references('id_hor')
                  ->on('schedules')
                  ->onDelete('cascade'); // Si se borra el horario, se borran sus días asignados

            $table->timestamps(); // Registros de auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('days', function (Blueprint $table) {
            $table->dropForeign(['schedule_day']);
        });
        Schema::dropIfExists('days');
    }
};
