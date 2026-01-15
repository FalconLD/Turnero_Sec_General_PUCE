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
            // Añadimos el campo banner_id después del plan_estudio
            // Usamos string porque los Banner ID suelen ser alfanuméricos
            $table->string('banner_id')->nullable()->unique()->after('plan_estudio');
        });
    }

    public function down(): void
    {
        Schema::table('student_registrations', function (Blueprint $table) {
            $table->dropColumn('banner_id');
        });
    }
};
