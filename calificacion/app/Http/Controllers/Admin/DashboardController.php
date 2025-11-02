<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Seccion;
use App\Models\Inscripcion;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas generales
        $totalEstudiantes = Usuario::estudiantes()->activos()->count();
        $totalProfesores = Usuario::profesores()->activos()->count();
        $totalCursos = Curso::activos()->count();
        
        // Período actual
        $periodoActual = PeriodoAcademico::where('estado', 'activo')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->first();
        
        // ✅ CORRECCIÓN: Usar periodo_academico_id en lugar de periodo_id
        $seccionesActuales = $periodoActual 
            ? Seccion::where('periodo_academico_id', $periodoActual->id)->count() 
            : 0;
        
        // Inscripciones del período actual
        $inscripcionesActuales = $periodoActual ? 
            Inscripcion::whereHas('seccion', function($query) use ($periodoActual) {
                // ✅ CORRECCIÓN: Usar periodo_academico_id
                $query->where('periodo_academico_id', $periodoActual->id);
            })->where('estado', 'inscrito')->count() : 0;
        
        // Estudiantes por carrera
        $estudiantesPorCarrera = Inscripcion::join('secciones', 'inscripciones.seccion_id', '=', 'secciones.id')
            ->join('cursos', 'secciones.curso_id', '=', 'cursos.id')
            ->where('inscripciones.estado', 'inscrito')
            ->selectRaw('cursos.carrera, COUNT(DISTINCT inscripciones.estudiante_id) as total')
            ->groupBy('cursos.carrera')
            ->get();
        
        // Últimas inscripciones (5 más recientes)
        $ultimasInscripciones = Inscripcion::with(['estudiante', 'seccion.curso', 'seccion.periodo'])
            ->orderBy('fecha_inscripcion', 'desc')
            ->limit(5)
            ->get();
        
        // Secciones con más estudiantes
        $seccionesPopulares = Seccion::withCount(['inscripciones' => function($query) {
                $query->where('estado', 'inscrito');
            }])
            ->with(['curso', 'periodo'])
            ->orderBy('inscripciones_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalEstudiantes',
            'totalProfesores', 
            'totalCursos',
            'seccionesActuales',
            'periodoActual',
            'inscripcionesActuales',
            'estudiantesPorCarrera',
            'ultimasInscripciones',
            'seccionesPopulares'
        ));
    }
    
    public function estadisticas(Request $request)
    {
        $periodo = $request->get('periodo');
        $carrera = $request->get('carrera');
        
        $query = Inscripcion::query();
        
        if ($periodo) {
            $query->whereHas('seccion', function($q) use ($periodo) {
                // ✅ CORRECCIÓN: Usar periodo_academico_id
                $q->where('periodo_academico_id', $periodo);
            });
        }
        
        if ($carrera) {
            $query->whereHas('seccion.curso', function($q) use ($carrera) {
                $q->where('carrera', $carrera);
            });
        }
        
        $inscripciones = $query->with(['estudiante', 'seccion.curso'])->get();
        
        $estadisticas = [
            'total_inscripciones' => $inscripciones->count(),
            'estudiantes_unicos' => $inscripciones->unique('estudiante_id')->count(),
            'aprobados' => $inscripciones->where('estado', 'completado')->where('nota_final', '>=', 70)->count(),
            'reprobados' => $inscripciones->where('estado', 'completado')->where('nota_final', '<', 70)->count(),
            'activos' => $inscripciones->where('estado', 'inscrito')->count(),
            'retirados' => $inscripciones->where('estado', 'retirado')->count(),
        ];
        
        return response()->json($estadisticas);
    }
}