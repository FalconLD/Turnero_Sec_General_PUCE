<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_registrations', function (Blueprint $table) {
            // Renombramos la columna
            $table->renameColumn('plan', 'plan_estudio');
        });
    }

    public function down(): void
    {
        Schema::table('student_registrations', function (Blueprint $table) {
            // Revertimos el cambio si es necesario
            $table->renameColumn('plan_estudio', 'plan');
        });
    }
};