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
            // 1. Agregamos la columna 'plan' de tipo string.
            // Usamos nullable() por si ya tenemmos registros antiguos, 
            // para que no de error al intentar llenar los viejos con nulo.
            $table->string('plan')->nullable()->after('nivel'); 
        });
    }

    public function down(): void
    {
        Schema::table('student_registrations', function (Blueprint $table) {
            // 2. Definimos cómo deshacer el cambio: eliminando la columna.
            $table->dropColumn('plan');
        });
    }
};
