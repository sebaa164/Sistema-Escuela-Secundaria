<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModalidadToSeccionesTable extends Migration
{
    public function up()
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->enum('modalidad', ['presencial', 'virtual', 'hibrida'])
                  ->default('presencial')
                  ->after('aula'); // La agrega después de la columna 'aula'
        });
    }

    public function down()
    {
        Schema::table('secciones', function (Blueprint $table) {
            $table->dropColumn('modalidad');
        });
    }
}