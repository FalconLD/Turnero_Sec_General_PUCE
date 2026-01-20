<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();

            // Identificación
            $table->string('names');
            $table->string('cedula', 36)->unique();
            $table->string('banner_id')->nullable();
            $table->string('correo_puce')->unique();

            // Datos personales
            $table->integer('edad')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('telefono', 255)->nullable();
            $table->string('direccion', 255)->nullable();

            // Datos académicos (Aquí añadimos plan_estudio)
            $table->string('facultad')->nullable();
            $table->string('carrera')->nullable();
            $table->string('nivel')->nullable();
            $table->string('plan_estudio')->nullable(); // <-- COLUMNA AÑADIDA
            $table->text('motivo')->nullable();

            // Selección y campos técnicos
            $table->string('nivel_instruccion')->nullable();
            $table->string('beca_san_ignacio')->nullable();

            // Estado y archivos
            $table->boolean('acepta_terminos')->default(false);
            $table->boolean('tomado')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
    }
};
