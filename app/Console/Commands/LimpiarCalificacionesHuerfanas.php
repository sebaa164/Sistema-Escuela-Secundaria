<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LimpiarCalificacionesHuerfanas extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'calificaciones:limpiar-huerfanas';

    /**
     * The console command description.
     */
    protected $description = 'Elimina todas las calificaciones con datos inválidos (estudiantes, evaluaciones o secciones inexistentes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Iniciando limpieza de calificaciones huérfanas...');
        $this->newLine();

        try {
            DB::beginTransaction();

            // 1. Eliminar calificaciones sin estudiante válido
            $this->info('📋 Verificando estudiantes...');
            $deletedEstudiantes = DB::table('calificaciones')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('usuarios')
                        ->whereRaw('usuarios.id = calificaciones.estudiante_id');
                })
                ->delete();

            if ($deletedEstudiantes > 0) {
                $this->warn("   ❌ Eliminadas {$deletedEstudiantes} calificaciones sin estudiante válido");
            } else {
                $this->line("   ✅ No se encontraron calificaciones sin estudiante");
            }

            // 2. Eliminar calificaciones sin evaluación válida
            $this->info('📋 Verificando evaluaciones...');
            $deletedEvaluaciones = DB::table('calificaciones')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('evaluaciones')
                        ->whereRaw('evaluaciones.id = calificaciones.evaluacion_id');
                })
                ->delete();

            if ($deletedEvaluaciones > 0) {
                $this->warn("   ❌ Eliminadas {$deletedEvaluaciones} calificaciones sin evaluación válida");
            } else {
                $this->line("   ✅ No se encontraron calificaciones sin evaluación");
            }

            // 3. Eliminar calificaciones cuya evaluación no tiene sección válida
            $this->info('📋 Verificando secciones...');
            $deletedSecciones = DB::table('calificaciones')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('evaluaciones')
                        ->join('secciones', 'evaluaciones.seccion_id', '=', 'secciones.id')
                        ->whereRaw('evaluaciones.id = calificaciones.evaluacion_id');
                })
                ->delete();

            if ($deletedSecciones > 0) {
                $this->warn("   ❌ Eliminadas {$deletedSecciones} calificaciones sin sección válida");
            } else {
                $this->line("   ✅ No se encontraron calificaciones sin sección");
            }

            // 4. Eliminar calificaciones cuya sección no tiene curso válido
            $this->info('📋 Verificando cursos...');
            $deletedCursos = DB::table('calificaciones')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('evaluaciones')
                        ->join('secciones', 'evaluaciones.seccion_id', '=', 'secciones.id')
                        ->join('cursos', 'secciones.curso_id', '=', 'cursos.id')
                        ->whereRaw('evaluaciones.id = calificaciones.evaluacion_id');
                })
                ->delete();

            if ($deletedCursos > 0) {
                $this->warn("   ❌ Eliminadas {$deletedCursos} calificaciones sin curso válido");
            } else {
                $this->line("   ✅ No se encontraron calificaciones sin curso");
            }

            DB::commit();

            $total = $deletedEstudiantes + $deletedEvaluaciones + $deletedSecciones + $deletedCursos;

            $this->newLine();
            if ($total > 0) {
                $this->info("═══════════════════════════════════════");
                $this->info("✅ LIMPIEZA COMPLETADA EXITOSAMENTE");
                $this->info("═══════════════════════════════════════");
                $this->warn("📊 Total eliminadas: {$total} calificaciones");
                $this->line("   • Sin estudiante: {$deletedEstudiantes}");
                $this->line("   • Sin evaluación: {$deletedEvaluaciones}");
                $this->line("   • Sin sección: {$deletedSecciones}");
                $this->line("   • Sin curso: {$deletedCursos}");
                $this->info("═══════════════════════════════════════");
                
                Log::info("Limpieza manual completada: {$total} calificaciones eliminadas");
            } else {
                $this->info("═══════════════════════════════════════");
                $this->info("✅ BASE DE DATOS LIMPIA");
                $this->info("═══════════════════════════════════════");
                $this->line("No se encontraron calificaciones huérfanas");
                $this->info("═══════════════════════════════════════");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error durante la limpieza: " . $e->getMessage());
            Log::error('Error en limpieza manual de calificaciones: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}