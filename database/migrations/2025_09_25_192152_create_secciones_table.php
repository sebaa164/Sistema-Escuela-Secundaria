<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeccionesTable extends Migration
{
    public function up()
    {
        Schema::create('secciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('curso_id');
            $table->unsignedBigInteger('periodo_academico_id');
            $table->unsignedBigInteger('profesor_id');
            $table->string('nombre', 50)->nullable(); // Agregar ->nullable()
            $table->string('codigo_seccion', 10);
            $table->integer('cupo_maximo')->default(30);
            $table->text('horario')->nullable();
            $table->string('aula', 50)->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'finalizado'])->default('activo');
            $table->timestamps();
            
            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('restrict');
            $table->foreign('periodo_academico_id')->references('id')->on('periodos_academicos')->onDelete('restrict');
            $table->foreign('profesor_id')->references('id')->on('usuarios')->onDelete('restrict');
            $table->unique(['curso_id', 'periodo_academico_id', 'codigo_seccion'], 'unique_seccion');
            $table->index('profesor_id');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('secciones');
    }
}