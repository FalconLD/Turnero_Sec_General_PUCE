<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cubiculos_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('cubiculo_id');
            $table->uuid('schedule_id');
            $table->timestamps();

            // Índices necesarios para claves foráneas
            $table->index('cubiculo_id');
            $table->index('schedule_id');

            // Claves foráneas
            $table->foreign('cubiculo_id')->references('id')->on('cubiculos')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id_hor')->on('schedules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cubiculos_schedules');
    }
};
