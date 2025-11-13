<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Añade esta línea
            $table->string('rejection_reason')->nullable()->after('verified_by');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Añade esta línea
            $table->dropColumn('rejection_reason');
        });
    }
};
