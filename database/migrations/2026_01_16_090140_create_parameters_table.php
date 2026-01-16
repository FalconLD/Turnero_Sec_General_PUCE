<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('parameters', function (Blueprint $table) {
        $table->id();
        $table->string('clave')->unique(); // Aquí buscará 'TERM'
        $table->text('valor')->nullable(); // Aquí irán los términos
        $table->string('descripcion')->nullable();
        $table->timestamps();
    });

    // Opcional: Insertar un valor por defecto para que no sea null
    DB::table('parameters')->insert([
        'clave' => 'TERM',
        'valor' => 'Términos y condiciones de uso del turnero...',
        'descripcion' => 'Texto legal para el registro de estudiantes'
    ]);
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parameters');
    }
};
