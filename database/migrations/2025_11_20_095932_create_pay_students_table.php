<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayStudentsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pay_students', function (Blueprint $table) {
            $table->id();

            // Cédula del estudiante
            $table->string('cedula');

            // Valor a pagar según student_registration
            $table->decimal('valor_pagar', 10, 2);

            // Forma de pago (transferencia, tarjeta, etc.)
            $table->string('forma_pago');

            // Comprobante (base64 o ruta del archivo)
            $table->longText('comprobante')->nullable();

            // Relación opcional
            $table->unsignedBigInteger('student_registration_id')->nullable();
            $table->foreign('student_registration_id')
                ->references('id')
                ->on('student_registrations')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pay_students');
    }
}
