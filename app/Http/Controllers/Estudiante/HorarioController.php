<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Seccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->tipo_usuario !== 'estudiante') {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar horario semanal del estudiante
     */
    public function index()
    {
        $estudiante = Auth::user();

        // Obtener inscripciones activas con horarios
        $inscripciones = Inscripcion::with(['seccion.curso', 'seccion.profesor', 'seccion.horarios'])
            ->where('estudiante_id', $estudiante->id)
            ->where('estado', 'inscrito')
            ->get();

        // Organizar horarios por día de la semana
        $horarioSemanal = [
            'Lunes' => [],
            'Martes' => [],
            'Miércoles' => [],
            'Jueves' => [],
            'Viernes' => [],
            'Sábado' => [],
        ];

        $horasDisponibles = [];

        foreach ($inscripciones as $inscripcion) {
            foreach ($inscripcion->seccion->horarios as $horario) {
                $dia = $horario->dia_semana;
                
                if (isset($horarioSemanal[$dia])) {
                    $horarioSemanal[$dia][] = [
                        'hora_inicio' => $horario->hora_inicio,
                        'hora_fin' => $horario->hora_fin,
                        'curso' => $inscripcion->seccion->curso->nombre,
                        'codigo' => $inscripcion->seccion->curso->codigo_curso,
                        'profesor' => $inscripcion->seccion->profesor->nombre_completo,
                        'aula' => $horario->aula ?? 'Por asignar',
                        'seccion' => $inscripcion->seccion->codigo_seccion,
                        'color' => $this->generarColor($inscripcion->seccion->curso->codigo_curso)
                    ];

                    // Guardar horas únicas
                    $horasDisponibles[] = $horario->hora_inicio;
                    $horasDisponibles[] = $horario->hora_fin;
                }
            }
        }

        // Ordenar horarios por hora de inicio
        foreach ($horarioSemanal as $dia => $horarios) {
            usort($horarioSemanal[$dia], function($a, $b) {
                return strcmp($a['hora_inicio'], $b['hora_inicio']);
            });
        }

        // Obtener horas únicas y ordenadas
        $horasDisponibles = array_unique($horasDisponibles);
        sort($horasDisponibles);

        // Generar colores para cada inscripción
        $coloresInscripciones = [];
        foreach ($inscripciones as $inscripcion) {
            $coloresInscripciones[$inscripcion->seccion->curso->codigo_curso] = $this->generarColor($inscripcion->seccion->curso->codigo_curso);
        }

        return view('estudiante.horario.index', compact('horarioSemanal', 'inscripciones', 'horasDisponibles', 'coloresInscripciones'));
    }

    /**
     * Generar color único basado en código de curso
     */
    private function generarColor($codigo)
    {
        $colores = [
            '#0ea5e9', // azul
            '#10b981', // verde
            '#f59e0b', // naranja
            '#ef4444', // rojo
            '#8b5cf6', // morado
            '#ec4899', // rosa
            '#06b6d4', // cyan
            '#14b8a6', // teal
        ];

        $index = abs(crc32($codigo)) % count($colores);
        return $colores[$index];
    }

    /**
     * Descargar horario en PDF
     */
    public function descargarPDF()
    {
        $estudiante = Auth::user();

        $inscripciones = Inscripcion::with(['seccion.curso', 'seccion.profesor', 'seccion.horarios'])
            ->where('estudiante_id', $estudiante->id)
            ->where('estado', 'inscrito')
            ->get();

        // Aquí podrías implementar la generación de PDF
        // Por ahora retornamos una vista simple
        
        return view('estudiante.horario.pdf', compact('inscripciones', 'estudiante'));
    }
}
