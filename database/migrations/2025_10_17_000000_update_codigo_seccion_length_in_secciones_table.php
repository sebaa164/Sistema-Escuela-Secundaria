<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCodigoSeccionLengthInSeccionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->string('codigo_seccion', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->string('codigo_seccion', 20)->change();
        });
    }
}