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
    Schema::create('cubiculos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre'); 
        $table->enum('tipo_atencion', ['virtual', 'presencial']); 
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // relación con usuarios
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cubiculos');
    }
};
