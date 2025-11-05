<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFechaRetiroToInscripcionesTable extends Migration
{
    public function up()
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            // Agregar la columna fecha_retiro solo si no existe
            if (!Schema::hasColumn('inscripciones', 'fecha_retiro')) {
                $table->timestamp('fecha_retiro')->nullable()->after('fecha_inscripcion');
            }
        });
    }

    public function down()
    {
        Schema::table('inscripciones', function (Blueprint $table) {
            if (Schema::hasColumn('inscripciones', 'fecha_retiro')) {
                $table->dropColumn('fecha_retiro');
            }
        });
    }
}