<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixInscripcionesEstadoValues extends Migration
{
    public function up()
    {
        // PASO 1: Primero actualizar los datos existentes
        DB::table('inscripciones')
            ->where('estado', 'inscripto')
            ->update(['estado' => 'inscrito']);
        
        // PASO 2: Luego cambiar la estructura de la columna
        DB::statement("ALTER TABLE inscripciones MODIFY COLUMN estado ENUM('inscrito', 'retirado', 'completado') DEFAULT 'inscrito'");
    }

    public function down()
    {
        // Revertir los cambios
        DB::table('inscripciones')
            ->where('estado', 'inscrito')
            ->update(['estado' => 'inscripto']);
            
        DB::statement("ALTER TABLE inscripciones MODIFY COLUMN estado ENUM('inscripto', 'retirado', 'completado') DEFAULT 'inscripto'");
    }
}