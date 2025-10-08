<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    // database/migrations/..._create_schedules_table.php

        public function up(): void
        {
            Schema::create('schedules', function (Blueprint $table) {
                $table->id();
                $table->time('hora_inicio');
                $table->time('hora_fin');
                $table->integer('descanso')->comment('Duración en minutos');
                $table->integer('atencion')->comment('Duración en minutos');
                $table->date('vigencia_desde');
                $table->date('vigencia_hasta');
                $table->integer('numeroturnos')->default(0);
                $table->integer('ocupados')->default(0);
                $table->foreignId('cubiculo_id')->constrained('cubiculos');
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
