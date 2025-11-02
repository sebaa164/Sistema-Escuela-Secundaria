<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seccion_id');
            $table->enum('dia_semana', [
                'Lunes', 
                'Martes', 
                'Miércoles', 
                'Jueves', 
                'Viernes', 
                'Sábado', 
                'Domingo'
            ]);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('aula', 50)->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('seccion_id')
                ->references('id')
                ->on('secciones')
                ->onDelete('cascade');

            // Índices
            $table->index('seccion_id');
            $table->index('dia_semana');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};