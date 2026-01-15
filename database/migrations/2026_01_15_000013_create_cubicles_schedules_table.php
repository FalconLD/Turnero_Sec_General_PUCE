<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla pivote para relacionar cubículos con sus horarios de atención.
     */
    public function up(): void
    {
        Schema::create('cubiculos_schedules', function (Blueprint $table) {
            // Relación con el cubículo (ID numérico)
            $table->unsignedBigInteger('cubiculo_id');

            // Relación con el horario (ID UUID)
            $table->uuid('schedule_id');

            $table->timestamps(); // Registros de auditoría

            // Índices para optimizar las consultas de búsqueda
            $table->index('cubiculo_id');
            $table->index('schedule_id');

            // Definición de claves foráneas con borrado en cascada
            $table->foreign('cubiculo_id')
                  ->references('id')
                  ->on('cubiculos')
                  ->onDelete('cascade');

            $table->foreign('schedule_id')
                  ->references('id_hor')
                  ->on('schedules')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cubiculos_schedules');
    }
};
