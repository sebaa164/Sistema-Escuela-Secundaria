<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('periodos_academicos', function (Blueprint $table) {
            if (!Schema::hasColumn('periodos_academicos', 'codigo')) {
                $table->string('codigo', 50)->unique()->after('id');
            }
            if (!Schema::hasColumn('periodos_academicos', 'ciclo_escolar')) {
                $table->string('ciclo_escolar', 20)->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('periodos_academicos', 'año_academico')) {
                $table->year('año_academico')->nullable()->after('ciclo_escolar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('periodos_academicos', function (Blueprint $table) {
            $table->dropColumn(['codigo', 'ciclo_escolar', 'año_academico']);
        });
    }
};