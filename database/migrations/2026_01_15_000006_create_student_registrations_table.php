<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Crea la tabla de registros de estudiantes sin lógica de pagos.
     */
    public function up(): void
    {
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id(); // ID autoincremental

            // Datos personales del estudiante
            $table->string('names'); // Nombres completos
            $table->string('cedula', 36)->unique(); // Cédula única para vinculación con turnos
            $table->integer('edad'); // Edad del estudiante
            $table->date('fecha_nacimiento'); // Fecha de nacimiento
            $table->string('telefono', 20); // Teléfono de contacto
            $table->string('direccion'); // Dirección domiciliaria
            $table->string('correo_puce')->unique(); // Correo institucional único

            // Información Académica
            $table->string('facultad'); // Facultad a la que pertenece
            $table->string('carrera'); // Carrera que cursa
            $table->string('nivel'); // Nivel o semestre actual
            $table->string('plan_estudio')->nullable(); // Plan de estudio unificado
            $table->text('motivo'); // Motivo de la solicitud de atención

            // Selección de estado y beneficios
            $table->enum('nivel_instruccion', ['tec', 'grado', 'posgrado', 'especializacion']); // Nivel de instrucción
            $table->enum('beca_san_ignacio', ['si', 'no']); // Indicador de beca

            // Control del flujo del turnero
            $table->boolean('tomado')->default(0); // Estado de atención del registro
            $table->boolean('acepta_terminos')->default(false); // Consentimiento legal

            $table->timestamps(); // created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
    }
};
