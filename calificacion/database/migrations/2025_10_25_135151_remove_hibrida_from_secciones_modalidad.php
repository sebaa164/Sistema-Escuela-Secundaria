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
        // Paso 1: Convertir todas las secciones "hibrida" a "presencial"
        DB::table('secciones')
            ->where('modalidad', 'hibrida')
            ->update(['modalidad' => 'presencial']);
        
        // Paso 2: Modificar la columna para eliminar "hibrida" del ENUM
        // Nota: En MySQL, no se puede modificar directamente un ENUM,
        // hay que recrear la columna
        
        // Si tu BD es MySQL/MariaDB:
        DB::statement("ALTER TABLE secciones MODIFY COLUMN modalidad ENUM('presencial', 'virtual') DEFAULT 'presencial'");
        
        // Si tu BD es PostgreSQL, usa este código en su lugar:
        // DB::statement("ALTER TABLE secciones DROP CONSTRAINT IF EXISTS secciones_modalidad_check");
        // DB::statement("ALTER TABLE secciones ADD CONSTRAINT secciones_modalidad_check CHECK (modalidad IN ('presencial', 'virtual'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar el ENUM con "hibrida"
        DB::statement("ALTER TABLE secciones MODIFY COLUMN modalidad ENUM('presencial', 'virtual', 'hibrida') DEFAULT 'presencial'");
        
        // Para PostgreSQL:
        // DB::statement("ALTER TABLE secciones DROP CONSTRAINT IF EXISTS secciones_modalidad_check");
        // DB::statement("ALTER TABLE secciones ADD CONSTRAINT secciones_modalidad_check CHECK (modalidad IN ('presencial', 'virtual', 'hibrida'))");
    }
};