<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Cambiar el enum de 'justificada' a 'justificado'
        DB::statement("ALTER TABLE asistencias MODIFY COLUMN estado ENUM('presente', 'ausente', 'tardanza', 'justificado') DEFAULT 'presente'");
    }

    public function down()
    {
        // Volver al estado anterior si es necesario
        DB::statement("ALTER TABLE asistencias MODIFY COLUMN estado ENUM('presente', 'ausente', 'tardanza', 'justificada') DEFAULT 'presente'");
    }
};