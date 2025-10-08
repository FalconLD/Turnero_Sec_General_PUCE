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
    Schema::create('parameters', function (Blueprint $table) {
        $table->id();
        $table->string('clave');
        $table->text('descripcion');
        $table->string('parametro');
        $table->timestamps(); // crea created_at y updated_at automáticamente
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
