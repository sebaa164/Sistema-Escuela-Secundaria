<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeCedulaNullableInTutoresTable extends Migration
{
    public function up()
    {
        Schema::table('tutores', function (Blueprint $table) {
            $table->string('cedula', 20)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('tutores', function (Blueprint $table) {
            $table->string('cedula', 20)->nullable(false)->change();
        });
    }
}
