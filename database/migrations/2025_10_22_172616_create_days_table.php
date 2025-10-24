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
        Schema::create('days', function (Blueprint $table) {
            $table->uuid('schedule_day'); // foreign key to schedules
            $table->date('date_day'); // date of the day
            $table->foreign('schedule_day')->references('id_hor')->on('schedules');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('days', function (Blueprint $table) {
            $table->dropForeign(['schedule_day']);
        });
        Schema::dropIfExists('days');
    }
};
