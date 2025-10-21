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

            // Datos personales
            $table->string('names');
            $table->string('cedula', 20);
            $table->integer('edad');
            $table->date('fecha_nacimiento');
            $table->string('telefono', 20);
            $table->string('direccion');
            $table->string('correo_puce')->unique();
            $table->string('facultad');
            $table->string('carrera');
            $table->string('nivel');
            $table->text('motivo');

            // Campos de selección
            $table->enum('nivel_instruccion', ['grado', 'posgrado']);
            $table->enum('beca_san_ignacio', ['si', 'no']);

            // Valor calculado
            $table->decimal('valor_pagar', 5, 2)->default(0);

            // Forma de pago
            $table->enum('forma_pago', ['DeUna', 'Transferencia', 'Efectivo']);

            // Estado de aceptación de términos
            $table->boolean('acepta_terminos')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registrations');
    }
};
