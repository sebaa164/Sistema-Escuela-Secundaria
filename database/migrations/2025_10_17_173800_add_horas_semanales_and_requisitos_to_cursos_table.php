<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            // Agregar horas_semanales después de creditos
            if (!Schema::hasColumn('cursos', 'horas_semanales')) {
                $table->integer('horas_semanales')->default(4)->after('creditos');
            }
            
            // Agregar requisitos después de carrera
            if (!Schema::hasColumn('cursos', 'requisitos')) {
                $table->text('requisitos')->nullable()->after('carrera');
            }
        });
    }

    public function down()
    {
        Schema::table('cursos', function (Blueprint $table) {
            if (Schema::hasColumn('cursos', 'horas_semanales')) {
                $table->dropColumn('horas_semanales');
            }
            if (Schema::hasColumn('cursos', 'requisitos')) {
                $table->dropColumn('requisitos');
            }
        });
    }
};