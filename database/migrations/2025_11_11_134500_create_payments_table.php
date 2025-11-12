<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // --- El Vínculo Clave ---
            // Conecta este pago con el registro de estudiante
            $table->foreignId('student_registration_id')
                ->constrained('student_registrations') // Enlaza a la tabla 'student_registrations'
                ->cascadeOnDelete(); // Si se borra el registro, se borra el pago

            // --- Datos del Pago (copiados del registro) ---
            $table->decimal('amount', 8, 2); // El 'valor_pagar'
            $table->string('payment_method'); // 'efectivo', 'transferencia', etc.
            $table->string('comprobante_path')->nullable(); // Ruta al archivo

            // --- El Estado (Lo que queríamos añadir) ---
            $table->string('status', 20)->default('pending'); // 'pending', 'verified', 'rejected'

            // --- Datos de Verificación ---
            $table->foreignId('verified_by')->nullable()->constrained('users'); // ID del admin que verificó
            $table->timestamp('verified_at')->nullable(); // Cuándo se verificó

            $table->timestamps(); // created_at, updated_at
        });
    }
};
