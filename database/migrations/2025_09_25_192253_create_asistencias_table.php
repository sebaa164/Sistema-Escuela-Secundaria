<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsistenciasTable extends Migration
{
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('inscripcion_id');
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'justificado'])->default('presente'); // ✅ Cambié 'justificada' por 'justificado'
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->foreign('inscripcion_id')->references('id')->on('inscripciones')->onDelete('cascade');
            $table->unique(['inscripcion_id', 'fecha'], 'unique_asistencia');
            $table->index('fecha');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
}