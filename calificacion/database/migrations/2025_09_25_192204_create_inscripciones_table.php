<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInscripcionesTable extends Migration
{
    public function up()
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('estudiante_id');
            $table->unsignedBigInteger('seccion_id');
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->enum('estado', ['inscripto', 'retirado', 'completado'])->default('inscripto');
            $table->decimal('nota_final', 5, 2)->nullable();
            $table->timestamps();
            
            $table->foreign('estudiante_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('seccion_id')->references('id')->on('secciones')->onDelete('cascade');
            $table->unique(['estudiante_id', 'seccion_id'], 'unique_inscripcion');
            $table->index('estudiante_id');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscripciones');
    }
}