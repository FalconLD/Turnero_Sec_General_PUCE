<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_registrations', function (Blueprint $table) {
            // Hacemos que todos los campos de perfil sean opcionales
            $table->integer('edad')->nullable()->change();
            $table->date('fecha_nacimiento')->nullable()->change();
            $table->string('telefono')->nullable()->change();
            $table->string('direccion')->nullable()->change();
            $table->string('nivel')->nullable()->change();
            $table->string('motivo')->nullable()->change();
            $table->string('nivel_instruccion')->nullable()->change();
            $table->string('beca_san_ignacio')->nullable()->change();
            $table->decimal('valor_pagar', 10, 2)->nullable()->change();
            $table->string('forma_pago')->nullable()->change();
            $table->string('comprobante')->nullable()->change();
            $table->boolean('tomado')->default(false)->change();
            $table->text('comprobante_base64')->nullable()->change();
            $table->string('comprobante_mime')->nullable()->change();
        });
    }

    public function down(): void
    {
        // No es estrictamente necesario para esta prueba, 
        // pero se recomienda dejarlo vacío o revertir a notNullable si fuera el caso.
    }
};