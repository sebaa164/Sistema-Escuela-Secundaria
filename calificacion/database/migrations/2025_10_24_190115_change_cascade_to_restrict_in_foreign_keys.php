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
        // ⚠️ IMPORTANTE: Cambiar foreign keys de CASCADE a RESTRICT
        // Esto evita eliminaciones accidentales en cascada
        
        // 1️⃣ CALIFICACIONES -> EVALUACIONES
        Schema::table('calificaciones', function (Blueprint $table) {
            // Eliminar foreign key existente
            $table->dropForeign(['evaluacion_id']);
            
            // Recrear con RESTRICT
            $table->foreign('evaluacion_id')
                  ->references('id')
                  ->on('evaluaciones')
                  ->onDelete('restrict');
        });

        // 2️⃣ CALIFICACIONES -> USUARIOS (estudiante)
        Schema::table('calificaciones', function (Blueprint $table) {
            // Eliminar foreign key existente
            $table->dropForeign(['estudiante_id']);
            
            // Recrear con RESTRICT
            $table->foreign('estudiante_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('restrict');
        });

        // 3️⃣ EVALUACIONES -> SECCIONES
        Schema::table('evaluaciones', function (Blueprint $table) {
            // Eliminar foreign key existente
            $table->dropForeign(['seccion_id']);
            
            // Recrear con RESTRICT
            $table->foreign('seccion_id')
                  ->references('id')
                  ->on('secciones')
                  ->onDelete('restrict');
        });

        // 4️⃣ INSCRIPCIONES -> SECCIONES
        Schema::table('inscripciones', function (Blueprint $table) {
            // Eliminar foreign key existente
            $table->dropForeign(['seccion_id']);
            
            // Recrear con RESTRICT
            $table->foreign('seccion_id')
                  ->references('id')
                  ->on('secciones')
                  ->onDelete('restrict');
        });

        // 5️⃣ INSCRIPCIONES -> USUARIOS (estudiante)
        Schema::table('inscripciones', function (Blueprint $table) {
            // Eliminar foreign key existente
            $table->dropForeign(['estudiante_id']);
            
            // Recrear con RESTRICT
            $table->foreign('estudiante_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('restrict');
        });

        // 6️⃣ ASISTENCIAS -> INSCRIPCIONES (esta puede quedarse en cascade)
        // Las asistencias SÍ deben eliminarse si se elimina la inscripción
        // Por eso esta NO la cambiamos
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ⏮️ REVERTIR: Volver a CASCADE
        
        // 1️⃣ Calificaciones -> Evaluaciones
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropForeign(['evaluacion_id']);
            $table->foreign('evaluacion_id')
                  ->references('id')
                  ->on('evaluaciones')
                  ->onDelete('cascade');
        });

        // 2️⃣ Calificaciones -> Usuarios
        Schema::table('calificaciones', function (Blueprint $table) {
            $table->dropForeign(['estudiante_id']);
            $table->foreign('estudiante_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });

        // 3️⃣ Evaluaciones -> Secciones
        Schema::table('evaluaciones', function (Blueprint $table) {
            $table->dropForeign(['seccion_id']);
            $table->foreign('seccion_id')
                  ->references('id')
                  ->on('secciones')
                  ->onDelete('cascade');
        });

        // 4️⃣ Inscripciones -> Secciones
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropForeign(['seccion_id']);
            $table->foreign('seccion_id')
                  ->references('id')
                  ->on('secciones')
                  ->onDelete('cascade');
        });

        // 5️⃣ Inscripciones -> Usuarios
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->dropForeign(['estudiante_id']);
            $table->foreign('estudiante_id')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }
};