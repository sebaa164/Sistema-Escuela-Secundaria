<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL: hacer nullable la columna porcentaje
        DB::statement('ALTER TABLE evaluaciones MODIFY porcentaje DECIMAL(5,2) NULL');
    }

    public function down(): void
    {
        // Revertir a NOT NULL con valor por defecto 0.00
        DB::statement("ALTER TABLE evaluaciones MODIFY porcentaje DECIMAL(5,2) NOT NULL DEFAULT 0.00");
    }
};
