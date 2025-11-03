<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadoProfesorToUsuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            // Agregar columna estado_profesor para profesores
            $table->enum('estado_profesor', ['titular', 'interino', 'suplente', 'licencia', 'jubilado', 'suspendido'])
                  ->nullable()
                  ->after('estado_estudiante')
                  ->comment('Estado específico para usuarios tipo profesor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn('estado_profesor');
        });
    }
}
