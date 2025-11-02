<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTutoresTable extends Migration
{
    public function up()
    {
        Schema::create('tutores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('cedula', 20)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('direccion')->nullable();
            $table->string('parentesco', 50); // relación con el estudiante: padre, madre, tutor, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutores');
    }
}
