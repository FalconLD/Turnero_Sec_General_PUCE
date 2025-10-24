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
            Schema::table('audits', function (Blueprint $table) {
                $table->string('auditable_id', 36)->change(); // UUID suele ser 36 caracteres
            });
        }

        public function down()
        {
            Schema::table('audits', function (Blueprint $table) {
                $table->bigInteger('auditable_id')->change();
            });
        }

};
