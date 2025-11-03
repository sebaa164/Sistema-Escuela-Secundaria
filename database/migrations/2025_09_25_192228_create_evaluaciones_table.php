<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionesTable extends Migration
{
    public function up()
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('seccion_id');
            $table->unsignedBigInteger('tipo_evaluacion_id');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->date('fecha_evaluacion')->nullable();
            $table->dateTime('fecha_limite')->nullable();
            $table->decimal('nota_maxima', 5, 2)->default(100.00);
            $table->decimal('porcentaje', 5, 2);
            $table->enum('estado', ['programada', 'activa', 'finalizada', 'cancelada'])->default('programada');
            $table->text('instrucciones')->nullable();
            $table->timestamps();
            
            $table->foreign('seccion_id')->references('id')->on('secciones')->onDelete('cascade');
            $table->foreign('tipo_evaluacion_id')->references('id')->on('tipos_evaluacion')->onDelete('restrict');
            $table->index('seccion_id');
            $table->index('fecha_evaluacion');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('evaluaciones');
    }
}