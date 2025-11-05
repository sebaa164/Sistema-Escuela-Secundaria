<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalificacionesTable extends Migration
{
    public function up()
    {
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('evaluacion_id');
            $table->unsignedBigInteger('estudiante_id');
            $table->decimal('nota', 5, 2)->nullable();
            $table->text('comentarios')->nullable();
            $table->timestamp('fecha_calificacion')->nullable();
            $table->enum('estado', ['pendiente', 'calificada', 'revisada'])->default('pendiente');
            $table->integer('intentos')->default(0);
            $table->integer('tiempo_empleado')->nullable();
            $table->timestamps();
            
            $table->foreign('evaluacion_id')->references('id')->on('evaluaciones')->onDelete('cascade');
            $table->foreign('estudiante_id')->references('id')->on('usuarios')->onDelete('cascade');
            $table->unique(['evaluacion_id', 'estudiante_id'], 'unique_calificacion');
            $table->index('estudiante_id');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('calificaciones');
    }
}