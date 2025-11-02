<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePeriodosAcademicosTable extends Migration
{
    public function up()
    {
        Schema::create('periodos_academicos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('estado', ['activo', 'inactivo', 'finalizado'])->default('activo');
            $table->timestamps();
            
            $table->index('estado');
            $table->index(['fecha_inicio', 'fecha_fin']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('periodos_academicos');
    }
}