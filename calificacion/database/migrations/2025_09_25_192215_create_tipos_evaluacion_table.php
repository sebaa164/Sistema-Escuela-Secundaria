<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabla de tipos de evaluación
        Schema::create('tipos_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Insertar tipos por defecto
        DB::table('tipos_evaluacion')->insert([
            ['nombre' => 'Examen Parcial', 'descripcion' => 'Evaluación parcial del contenido', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Examen Final', 'descripcion' => 'Evaluación final acumulativa', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tarea', 'descripcion' => 'Tarea o ejercicio asignado', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Proyecto', 'descripcion' => 'Proyecto práctico o investigación', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Quiz', 'descripcion' => 'Evaluación rápida', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Laboratorio', 'descripcion' => 'Práctica de laboratorio', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Presentación', 'descripcion' => 'Exposición oral', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Participación', 'descripcion' => 'Participación en clase', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_evaluacion');
    }
};