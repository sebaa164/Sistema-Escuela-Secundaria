<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuariosTable extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('email', 191)->unique();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('tipo_usuario', ['estudiante', 'profesor', 'administrador'])->default('estudiante');
            $table->enum('estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            
            $table->index('tipo_usuario');
            $table->index('estado');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
}