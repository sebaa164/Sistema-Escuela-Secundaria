<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->string('nombre', 50)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->string('nombre', 50)->nullable(false)->change();
        });
    }
};