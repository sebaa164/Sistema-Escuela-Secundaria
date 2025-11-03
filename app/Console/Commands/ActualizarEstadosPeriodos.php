<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeriodoAcademico;
use Carbon\Carbon;

class ActualizarEstadosPeriodos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'periodos:actualizar-estados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza automáticamente los estados de los períodos académicos según las fechas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Actualizando estados de períodos académicos...');

        $hoy = Carbon::now();
        $actualizados = 0;

        // Obtener todos los períodos
        $periodos = PeriodoAcademico::all();

        foreach ($periodos as $periodo) {
            $fechaInicio = Carbon::parse($periodo->fecha_inicio);
            $fechaFin = Carbon::parse($periodo->fecha_fin);
            $estadoAnterior = $periodo->estado;
            $nuevoEstado = null;

            // Determinar el estado correcto según las fechas
            if ($hoy->lt($fechaInicio)) {
                // El período aún no ha comenzado
                $nuevoEstado = 'Inactivo';
            } elseif ($hoy->between($fechaInicio, $fechaFin)) {
                // El período está en curso
                $nuevoEstado = 'Activo';
            } else {
                // El período ya terminó
                $nuevoEstado = 'Finalizado';
            }

            // Actualizar solo si hay cambio de estado
            if ($estadoAnterior !== $nuevoEstado) {
                $periodo->update(['estado' => $nuevoEstado]);
                $actualizados++;

                $this->line("  ✅ Período '{$periodo->nombre}' actualizado: {$estadoAnterior} → {$nuevoEstado}");
            }
        }

        if ($actualizados > 0) {
            $this->info("✅ Se actualizaron {$actualizados} período(s) académico(s)");
        } else {
            $this->info("ℹ️  Todos los períodos ya están con el estado correcto");
        }

        // Mostrar período activo actual
        $periodoActivo = PeriodoAcademico::where('estado', 'Activo')
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy)
            ->first();

        if ($periodoActivo) {
            $this->info("📅 Período activo: {$periodoActivo->nombre}");
        } else {
            $this->warn("⚠️  No hay período activo actualmente (vacaciones)");
        }

        return 0;
    }
}
