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
        Schema::create('schedules', function (Blueprint $table) {
            $table->uuid('id_hor')->primary();
            $table->timeTz('start_time'); // Start hour
            $table->timeTz('end_time');   // End hour
            $table->dateTime('valid_from')->comment('Date when the schedule becomes active');
            $table->unsignedInteger('break_minutes')->default(0)->comment('Break duration in minutes');
            $table->unsignedInteger('attention_minutes')->default(1)->comment('Attention duration in minutes');
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
