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
        Schema::table('cubiculos', function (Blueprint $table) {
            $table->string('enlace_o_ubicacion')->nullable()->after('tipo_atencion');
        });
    }

    public function down(): void
    {
        Schema::table('cubiculos', function (Blueprint $table) {
            $table->dropColumn('enlace_o_ubicacion');
        });
    }

};
