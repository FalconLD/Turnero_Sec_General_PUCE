<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id_shift')->primary();
            $table->uuid('schedule_shift');
            $table->unsignedBigInteger('cubicle_shift');
            $table->date('date_shift');
            $table->timeTz('start_shift');
            $table->timeTz('end_shift');
            $table->string('person_shift', 36)->nullable(); // debe ser compatible con student_registrations.cedula
            $table->smallInteger('status_shift')->default(1);
            $table->timestamps();

            // Foreign keys
            $table->foreign('cubicle_shift')
                  ->references('id')->on('cubiculos')
                  ->onDelete('cascade');

            $table->foreign('person_shift')
                  ->references('cedula')->on('student_registrations')
                  ->onDelete('set null');

            $table->foreign('schedule_shift')
                  ->references('id_hor')->on('schedules')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['cubicle_shift']);
            $table->dropForeign(['person_shift']);
            $table->dropForeign(['schedule_shift']);
        });

        Schema::dropIfExists('shifts');
    }
};
