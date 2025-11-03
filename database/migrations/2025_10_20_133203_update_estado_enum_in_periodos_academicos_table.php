<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar valores existentes a mayúsculas
        DB::table('periodos_academicos')->where('estado', 'activo')->update(['estado' => 'Activo']);
        DB::table('periodos_academicos')->where('estado', 'inactivo')->update(['estado' => 'Inactivo']);
        DB::table('periodos_academicos')->where('estado', 'finalizado')->update(['estado' => 'Finalizado']);

        // Modificar el ENUM para usar mayúsculas
        DB::statement("ALTER TABLE periodos_academicos MODIFY COLUMN estado ENUM('Activo', 'Inactivo', 'Finalizado') DEFAULT 'Activo'");
    }

    public function down(): void
    {
        // Revertir a minúsculas
        DB::table('periodos_academicos')->where('estado', 'Activo')->update(['estado' => 'activo']);
        DB::table('periodos_academicos')->where('estado', 'Inactivo')->update(['estado' => 'inactivo']);
        DB::table('periodos_academicos')->where('estado', 'Finalizado')->update(['estado' => 'finalizado']);

        DB::statement("ALTER TABLE periodos_academicos MODIFY COLUMN estado ENUM('activo', 'inactivo', 'finalizado') DEFAULT 'activo'");
    }
};