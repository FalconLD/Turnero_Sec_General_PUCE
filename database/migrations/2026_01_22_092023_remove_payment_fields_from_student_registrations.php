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
    Schema::table('student_registrations', function (Blueprint $table) {
        // Verificamos si la columna existe antes de borrar
        if (Schema::hasColumn('student_registrations', 'forma_pago')) {
            $table->dropColumn('forma_pago');
        }
        
        if (Schema::hasColumn('student_registrations', 'valor_pagar')) {
            $table->dropColumn('valor_pagar');
        }
    });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_registrations', function (Blueprint $table) {
            // Rollback: Restauramos los campos tal como estaban
            $table->string('forma_pago')->nullable();
            $table->decimal('valor_pagar', 10, 2)->nullable();
        });
    }
};
