<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Seccion;
use App\Models\Inscripcion;
use App\Models\Calificacion;
use App\Models\Asistencia;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function index()
    {
        return view('admin.reportes.index');
    }
    
    public function estudiantes(Request $request)
    {
        $query = Usuario::estudiantes()->with(['inscripciones.seccion.curso']);
        
        if ($request->filled('carrera')) {
            $query->whereHas('inscripciones.seccion.curso', function($q) use ($request) {
                $q->where('carrera', $request->carrera);
            });
        }
        
        if ($request->filled('periodo_id')) {
            $query->whereHas('inscripciones.seccion', function($q) use ($request) {
                $q->where('periodo_id', $request->periodo_id);
            });
        }
        
        $estudiantes = $query->get();
        
        $reporteEstudiantes = $estudiantes->map(function($estudiante) {
            $inscripciones = $estudiante->inscripciones()->where('estado', '!=', 'retirado')->get();
            
            return [
                'estudiante' => $estudiante,
                'total_materias' => $inscripciones->count(),
                'materias_aprobadas' => $inscripciones->where('esta_aprobado', true)->count(),
                'materias_reprobadas' => $inscripciones->where('esta_aprobado', false)->count(),
                'promedio_general' => $inscripciones->whereNotNull('nota_final')->avg('nota_final'),
                'horas_semanales_aprobadas' => $inscripciones->where('esta_aprobado', true)
                                                    ->sum(function($i) { 
                                                        return $i->seccion->curso->horas_semanales; 
                                                    })
            ];
        });
        
        $carreras = Curso::distinct()->pluck('carrera')->filter()->sort();
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get(['id', 'nombre']);
        
        return view('admin.reportes.estudiantes', compact('reporteEstudiantes', 'carreras', 'periodos'));
    }
    
    public function calificaciones(Request $request)
    {
        $seccionId = $request->get('seccion_id');
        $periodoId = $request->get('periodo_id');
        $carrera = $request->get('carrera');
        
        $query = Calificacion::with(['evaluacion.seccion.curso', 'estudiante']);
        
        if ($seccionId) {
            $query->whereHas('evaluacion', function($q) use ($seccionId) {
                $q->where('seccion_id', $seccionId);
            });
        }
        
        if ($periodoId) {
            $query->whereHas('evaluacion.seccion', function($q) use ($periodoId) {
                $q->where('periodo_id', $periodoId);
            });
        }
        
        if ($carrera) {
            $query->whereHas('evaluacion.seccion.curso', function($q) use ($carrera) {
                $q->where('carrera', $carrera);
            });
        }
        
        $calificaciones = $query->whereNotNull('nota')->get();
        
        $estadisticas = [
            'total_calificaciones' => $calificaciones->count(),
            'promedio_general' => $calificaciones->avg('nota'),
            'nota_maxima' => $calificaciones->max('nota'),
            'nota_minima' => $calificaciones->min('nota'),
            'aprobados' => $calificaciones->where('esta_aprobada', true)->count(),
            'reprobados' => $calificaciones->where('esta_aprobada', false)->count(),
            'distribucion_notas' => [
                '90-100' => $calificaciones->whereBetween('nota', [90, 100])->count(),
                '80-89' => $calificaciones->whereBetween('nota', [80, 89])->count(),
                '70-79' => $calificaciones->whereBetween('nota', [70, 79])->count(),
                '60-69' => $calificaciones->whereBetween('nota', [60, 69])->count(),
                '0-59' => $calificaciones->where('nota', '<', 60)->count(),
            ]
        ];
        
        $secciones = Seccion::with('curso')->get(['id', 'codigo_seccion', 'curso_id']);
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get(['id', 'nombre']);
        $carreras = Curso::distinct()->pluck('carrera')->filter()->sort();
        
        return view('admin.reportes.calificaciones', compact(
            'calificaciones', 'estadisticas', 'secciones', 'periodos', 'carreras'
        ));
    }
    
    public function asistencias(Request $request)
    {
        $seccionId = $request->get('seccion_id');
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());
        
        $query = Asistencia::with(['inscripcion.estudiante', 'inscripcion.seccion.curso']);
        
        if ($seccionId) {
            $query->whereHas('inscripcion', function($q) use ($seccionId) {
                $q->where('seccion_id', $seccionId);
            });
        }
        
        $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        
        $asistencias = $query->get();
        
        $estadisticas = [
            'total_registros' => $asistencias->count(),
            'presentes' => $asistencias->where('estado', 'presente')->count(),
            'ausentes' => $asistencias->where('estado', 'ausente')->count(),
            'tardanzas' => $asistencias->where('estado', 'tardanza')->count(),
            'justificadas' => $asistencias->where('estado', 'justificada')->count(),
        ];
        
        $estadisticas['porcentaje_asistencia'] = $estadisticas['total_registros'] > 0 ? 
            round((($estadisticas['presentes'] + $estadisticas['tardanzas']) / $estadisticas['total_registros']) * 100, 2) : 0;
        
        $reportePorEstudiante = $asistencias->groupBy('inscripcion.estudiante.id')
                                          ->map(function($asistenciasEstudiante) {
                                              $estudiante = $asistenciasEstudiante->first()->inscripcion->estudiante;
                                              $total = $asistenciasEstudiante->count();
                                              $presentes = $asistenciasEstudiante->where('estado', 'presente')->count();
                                              $tardanzas = $asistenciasEstudiante->where('estado', 'tardanza')->count();
                                              
                                              return [
                                                  'estudiante' => $estudiante,
                                                  'total_dias' => $total,
                                                  'presentes' => $presentes,
                                                  'ausentes' => $asistenciasEstudiante->where('estado', 'ausente')->count(),
                                                  'tardanzas' => $tardanzas,
                                                  'justificadas' => $asistenciasEstudiante->where('estado', 'justificada')->count(),
                                                  'porcentaje' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 2) : 0
                                              ];
                                          });
        
        $secciones = Seccion::with('curso')->activas()->get(['id', 'codigo_seccion', 'curso_id']);
        
        return view('admin.reportes.asistencias', compact(
            'asistencias', 'estadisticas', 'reportePorEstudiante', 'secciones', 'fechaInicio', 'fechaFin'
        ));
    }
    
    public function rendimientoAcademico(Request $request)
{
    $periodoId = $request->get('periodo_id');
    $carrera = $request->get('carrera');
    
    $query = Inscripcion::with(['estudiante', 'seccion.curso'])
                       ->where('estado', '!=', 'retirado');
    
    if ($periodoId) {
        $query->whereHas('seccion', function($q) use ($periodoId) {
            $q->where('periodo_id', $periodoId);
        });
    }
    
    if ($carrera) {
        $query->whereHas('seccion.curso', function($q) use ($carrera) {
            $q->where('carrera', $carrera);
        });
    }
    
    $inscripciones = $query->get();
    
    $rendimientoPorMateria = $inscripciones->groupBy('seccion.curso.codigo_curso')
                                         ->map(function($inscripcionesMateria) {
                                             $curso = $inscripcionesMateria->first()->seccion->curso;
                                             $total = $inscripcionesMateria->count();
                                             $aprobados = $inscripcionesMateria->where('esta_aprobado', true)->count();
                                             
                                             // Calcular promedio solo con notas válidas
                                             $notasValidas = $inscripcionesMateria
                                                 ->whereNotNull('nota_final')
                                                 ->filter(function($i) {
                                                     return is_numeric($i->nota_final) && $i->nota_final > 0;
                                                 })
                                                 ->pluck('nota_final');
                                             
                                             $promedio = $notasValidas->isNotEmpty() 
                                                 ? (float) $notasValidas->avg() 
                                                 : null;
                                             
                                             // Asegurar que el porcentaje sea numérico
                                             $porcentajeAprobacion = $total > 0 
                                                 ? (float) round(($aprobados / $total) * 100, 2) 
                                                 : 0.0;
                                             
                                             return [
                                                 'curso' => $curso,
                                                 'total_estudiantes' => (int) $total,
                                                 'aprobados' => (int) $aprobados,
                                                 'reprobados' => (int) $inscripcionesMateria->where('esta_aprobado', false)->count(),
                                                 'porcentaje_aprobacion' => $porcentajeAprobacion,
                                                 'promedio' => $promedio ? round($promedio, 2) : null
                                             ];
                                         });
    
    $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get(['id', 'nombre']);
    $carreras = Curso::distinct()->pluck('carrera')->filter()->sort();
    
    return view('admin.reportes.rendimiento', compact('rendimientoPorMateria', 'periodos', 'carreras'));
}
    
    /**
     * ========================================
     * EXPORTACIÓN A PDF
     * ========================================
     */
    public function exportarPDF(Request $request)
    {
        $tipo = $request->get('tipo');
        
        try {
            $pdf = null;
            $nombreArchivo = '';
            
            switch ($tipo) {
                case 'estudiantes':
                    $pdf = $this->generarPDFEstudiantes($request);
                    $nombreArchivo = 'Reporte_Estudiantes';
                    break;
                case 'calificaciones':
                    $pdf = $this->generarPDFCalificaciones($request);
                    $nombreArchivo = 'Reporte_Calificaciones';
                    break;
                case 'asistencias':
                    $pdf = $this->generarPDFAsistencias($request);
                    $nombreArchivo = 'Reporte_Asistencias';
                    break;
                case 'rendimiento':
                    $pdf = $this->generarPDFRendimiento($request);
                    $nombreArchivo = 'Reporte_Rendimiento_Academico';
                    break;
                default:
                    return redirect()->back()->with('error', 'Tipo de reporte no válido.');
            }
            
            $nombreArchivo .= '_' . date('Y-m-d_His') . '.pdf';
            
            return $pdf->download($nombreArchivo);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
    
    private function generarPDFEstudiantes($request)
    {
        $query = Usuario::estudiantes()->with(['inscripciones.seccion.curso']);
        
        if ($request->filled('carrera')) {
            $query->whereHas('inscripciones.seccion.curso', function($q) use ($request) {
                $q->where('carrera', $request->carrera);
            });
        }
        
        $estudiantes = $query->get();
        
        $reporteEstudiantes = $estudiantes->map(function($estudiante) {
            $inscripciones = $estudiante->inscripciones()->where('estado', '!=', 'retirado')->get();
            $totalMaterias = $inscripciones->count();
            $aprobadas = $inscripciones->where('esta_aprobado', true)->count();
            $promedio = $inscripciones->whereNotNull('nota_final')->avg('nota_final');
            $horasSemanales = $inscripciones->where('esta_aprobado', true)
                                           ->sum(function($i) { 
                                               return $i->seccion->curso->horas_semanales; 
                                           });
            
            $porcentaje = $totalMaterias > 0 ? ($aprobadas / $totalMaterias) * 100 : 0;
            $estado = $porcentaje >= 80 ? 'Excelente' : ($porcentaje >= 60 ? 'Bueno' : ($porcentaje >= 40 ? 'Regular' : 'Crítico'));
            
            return [
                'nombre' => $estudiante->name,
                'email' => $estudiante->email,
                'carrera' => $estudiante->carrera ?? 'N/A',
                'total_materias' => $totalMaterias,
                'aprobadas' => $aprobadas,
                'reprobadas' => $inscripciones->where('esta_aprobado', false)->count(),
                'horas_semanales' => $horasSemanales,
                'promedio' => $promedio ? number_format($promedio, 2) : 'N/A',
                'estado' => $estado,
                'porcentaje' => $porcentaje
            ];
        });
        
        $pdf = PDF::loadView('admin.reportes.pdf.estudiantes', [
            'reporteEstudiantes' => $reporteEstudiantes,
            'filtros' => $request->all()
        ]);
        
        return $pdf->setPaper('a4', 'landscape');
    }
    
    private function generarPDFCalificaciones($request)
    {
        $query = Calificacion::with(['evaluacion.seccion.curso', 'estudiante']);
        
        if ($request->filled('seccion_id')) {
            $query->whereHas('evaluacion', function($q) use ($request) {
                $q->where('seccion_id', $request->seccion_id);
            });
        }
        
        $calificaciones = $query->whereNotNull('nota')->get();
        
        $estadisticas = [
            'total_calificaciones' => $calificaciones->count(),
            'promedio_general' => $calificaciones->avg('nota'),
            'nota_maxima' => $calificaciones->max('nota'),
            'nota_minima' => $calificaciones->min('nota'),
            'aprobados' => $calificaciones->where('esta_aprobada', true)->count(),
            'reprobados' => $calificaciones->where('esta_aprobada', false)->count(),
        ];
        
        $pdf = PDF::loadView('admin.reportes.pdf.calificaciones', [
            'calificaciones' => $calificaciones,
            'estadisticas' => $estadisticas
        ]);
        
        return $pdf->setPaper('a4', 'landscape');
    }
    
    private function generarPDFAsistencias($request)
    {
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth());
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth());
        
        $query = Asistencia::with(['inscripcion.estudiante', 'inscripcion.seccion.curso']);
        
        if ($request->filled('seccion_id')) {
            $query->whereHas('inscripcion', function($q) use ($request) {
                $q->where('seccion_id', $request->seccion_id);
            });
        }
        
        $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
        $asistencias = $query->get();
        
        $estadisticas = [
            'total_registros' => $asistencias->count(),
            'presentes' => $asistencias->where('estado', 'presente')->count(),
            'ausentes' => $asistencias->where('estado', 'ausente')->count(),
            'tardanzas' => $asistencias->where('estado', 'tardanza')->count(),
            'justificadas' => $asistencias->where('estado', 'justificada')->count(),
        ];
        
        $estadisticas['porcentaje_asistencia'] = $estadisticas['total_registros'] > 0 ? 
            round((($estadisticas['presentes'] + $estadisticas['tardanzas']) / $estadisticas['total_registros']) * 100, 2) : 0;
        
        $reportePorEstudiante = $asistencias->groupBy('inscripcion.estudiante.id')
                                          ->map(function($asistenciasEstudiante) {
                                              $estudiante = $asistenciasEstudiante->first()->inscripcion->estudiante;
                                              $total = $asistenciasEstudiante->count();
                                              $presentes = $asistenciasEstudiante->where('estado', 'presente')->count();
                                              $tardanzas = $asistenciasEstudiante->where('estado', 'tardanza')->count();
                                              
                                              return [
                                                  'estudiante' => $estudiante->name,
                                                  'email' => $estudiante->email,
                                                  'total_dias' => $total,
                                                  'presentes' => $presentes,
                                                  'ausentes' => $asistenciasEstudiante->where('estado', 'ausente')->count(),
                                                  'tardanzas' => $tardanzas,
                                                  'justificadas' => $asistenciasEstudiante->where('estado', 'justificada')->count(),
                                                  'porcentaje' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 2) : 0
                                              ];
                                          });
        
        $pdf = PDF::loadView('admin.reportes.pdf.asistencias', [
            'reportePorEstudiante' => $reportePorEstudiante,
            'estadisticas' => $estadisticas,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin
        ]);
        
        return $pdf->setPaper('a4', 'landscape');
    }
    
    private function generarPDFRendimiento($request)
    {
        $query = Inscripcion::with(['seccion.curso'])->where('estado', '!=', 'retirado');
        
        if ($request->filled('carrera')) {
            $query->whereHas('seccion.curso', function($q) use ($request) {
                $q->where('carrera', $request->carrera);
            });
        }
        
        $inscripciones = $query->get();
        $agrupado = $inscripciones->groupBy('seccion.curso.codigo_curso');
        
        $rendimientoPorMateria = [];
        foreach ($agrupado as $codigo => $inscMat) {
            $curso = $inscMat->first()->seccion->curso;
            $total = $inscMat->count();
            $aprobados = $inscMat->where('esta_aprobado', true)->count();
            $promedio = $inscMat->whereNotNull('nota_final')->avg('nota_final');
            $porcentaje = $total > 0 ? round(($aprobados / $total) * 100, 2) : 0;
            
            $rendimientoPorMateria[] = [
                'codigo' => $curso->codigo_curso,
                'curso' => $curso,
                'nombre' => $curso->nombre,
                'carrera' => $curso->carrera,
                'nivel' => $curso->nivel ?? 'N/A',
                'horas_semanales' => $curso->horas_semanales,
                'total_estudiantes' => $total,
                'aprobados' => $aprobados,
                'reprobados' => $inscMat->where('esta_aprobado', false)->count(),
                'porcentaje' => $porcentaje,
                'promedio' => $promedio ? number_format($promedio, 2) : 'N/A',
                'estado' => $porcentaje >= 85 ? 'Excelente' : ($porcentaje >= 70 ? 'Bueno' : ($porcentaje >= 60 ? 'Regular' : 'Crítico'))
            ];
        }
        
        $pdf = PDF::loadView('admin.reportes.pdf.rendimiento', [
            'rendimientoPorMateria' => $rendimientoPorMateria
        ]);
        
        return $pdf->setPaper('a4', 'landscape');
    }
    
    /**
     * ========================================
     * EXPORTACIÓN A EXCEL
     * ========================================
     */
    public function exportarExcel(Request $request)
    {
        $tipo = $request->get('tipo');
        
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            switch ($tipo) {
                case 'estudiantes':
                    $this->generarExcelEstudiantes($sheet, $request);
                    $nombreArchivo = 'Reporte_Estudiantes';
                    break;
                case 'calificaciones':
                    $this->generarExcelCalificaciones($sheet, $request);
                    $nombreArchivo = 'Reporte_Calificaciones';
                    break;
                case 'asistencias':
                    $this->generarExcelAsistencias($sheet, $request);
                    $nombreArchivo = 'Reporte_Asistencias';
                    break;
                case 'rendimiento':
                    $this->generarExcelRendimiento($sheet, $request);
                    $nombreArchivo = 'Reporte_Rendimiento_Academico';
                    break;
                default:
                    return redirect()->back()->with('error', 'Tipo de reporte no válido.');
            }
            
            $nombreArchivo .= '_' . date('Y-m-d_His') . '.xlsx';
            
            $writer = new Xlsx($spreadsheet);
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar el archivo: ' . $e->getMessage());
        }
    }
    
    private function generarExcelEstudiantes($sheet, $request)
    {
        $sheet->setCellValue('A1', 'REPORTE DE ESTUDIANTES');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Generado: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:I2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Nombre Completo', 'Email', 'Carrera', 'Total Materias', 'Aprobadas', 'Reprobadas', 'Horas Semanales', 'Promedio', 'Estado'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        
        $sheet->getStyle('A4:I4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0EA5E9']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        
        $query = Usuario::estudiantes()->with(['inscripciones.seccion.curso']);
        
        if ($request->filled('carrera')) {
            $query->whereHas('inscripciones.seccion.curso', function($q) use ($request) {
                $q->where('carrera', $request->carrera);
            });
        }
        
        $estudiantes = $query->get();
        
        $row = 5;
        foreach ($estudiantes as $estudiante) {
            $inscripciones = $estudiante->inscripciones()->where('estado', '!=', 'retirado')->get();
            $totalMaterias = $inscripciones->count();
            $aprobadas = $inscripciones->where('esta_aprobado', true)->count();
            $promedio = $inscripciones->whereNotNull('nota_final')->avg('nota_final');
            $horasSemanales = $inscripciones->where('esta_aprobado', true)
                                           ->sum(function($i) { 
                                               return $i->seccion->curso->horas_semanales; 
                                           });
            
            $porcentaje = $totalMaterias > 0 ? ($aprobadas / $totalMaterias) * 100 : 0;
            $estado = $porcentaje >= 80 ? 'Excelente' : ($porcentaje >= 60 ? 'Bueno' : ($porcentaje >= 40 ? 'Regular' : 'Crítico'));
            
            $sheet->setCellValue('A' . $row, $estudiante->name);
            $sheet->setCellValue('B' . $row, $estudiante->email);
            $sheet->setCellValue('C' . $row, $estudiante->carrera ?? 'N/A');
            $sheet->setCellValue('D' . $row, $totalMaterias);
            $sheet->setCellValue('E' . $row, $aprobadas);
            $sheet->setCellValue('F' . $row, $inscripciones->where('esta_aprobado', false)->count());
            $sheet->setCellValue('G' . $row, $horasSemanales);
            $sheet->setCellValue('H' . $row, $promedio ? number_format($promedio, 2) : 'N/A');
            $sheet->setCellValue('I' . $row, $estado);
            
            $color = $porcentaje >= 80 ? '10B981' : ($porcentaje >= 60 ? '06B6D4' : ($porcentaje >= 40 ? 'F59E0B' : 'EF4444'));
            $sheet->getStyle('I' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]
            ]);
            
            $row++;
        }
        
        $sheet->getStyle('A4:I' . ($row - 1))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]]
        ]);
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    private function generarExcelCalificaciones($sheet, $request)
    {
        $sheet->setCellValue('A1', 'REPORTE DE CALIFICACIONES');
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Generado: ' . date('d/m/Y H:i'));
        $sheet->mergeCells('A2:L2');
        
        $headers = ['Estudiante', 'Email', 'Curso', 'Código', 'Sección', 'Evaluación', 'Tipo', 'Nota', 'Porcentaje', 'Puntos', 'Estado', 'Fecha'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        
        $query = Calificacion::with(['evaluacion.seccion.curso', 'estudiante']);
        
        if ($request->filled('seccion_id')) {
            $query->whereHas('evaluacion', function($q) use ($request) {
                $q->where('seccion_id', $request->seccion_id);
            });
        }
        
        $calificaciones = $query->whereNotNull('nota')->get();
        
        $row = 5;
        foreach ($calificaciones as $calif) {
            $sheet->setCellValue('A' . $row, $calif->estudiante->name);
            $sheet->setCellValue('B' . $row, $calif->estudiante->email);
            $sheet->setCellValue('C' . $row, $calif->evaluacion->seccion->curso->nombre);
            $sheet->setCellValue('D' . $row, $calif->evaluacion->seccion->curso->codigo_curso);
            $sheet->setCellValue('E' . $row, $calif->evaluacion->seccion->codigo_seccion);
            $sheet->setCellValue('F' . $row, $calif->evaluacion->nombre);
            $sheet->setCellValue('G' . $row, ucfirst($calif->evaluacion->tipo));
            $sheet->setCellValue('H' . $row, number_format($calif->nota, 2));
            $sheet->setCellValue('I' . $row, $calif->evaluacion->porcentaje . '%');
            $sheet->setCellValue('J' . $row, number_format($calif->nota * ($calif->evaluacion->porcentaje / 100), 2));
            $sheet->setCellValue('K' . $row, $calif->esta_aprobada ? 'Aprobado' : 'Reprobado');
            $sheet->setCellValue('L' . $row, $calif->created_at->format('d/m/Y'));
            
            $color = $calif->esta_aprobada ? '10B981' : 'EF4444';
            $sheet->getStyle('K' . $row)->applyFromArray([
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]
            ]);
            
            $row++;
        }
        
        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    private function generarExcelAsistencias($sheet, $request)
    {
        $sheet->setCellValue('A1', 'REPORTE DE ASISTENCIAS');
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Estudiante', 'Email', 'Total Días', 'Presentes', 'Ausentes', 'Tardanzas', 'Justificadas', '% Asistencia'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $col++;
        }
        
        $sheet->getStyle('A3:H3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '06B6D4']]
        ]);
        
        $query = Asistencia::with(['inscripcion.estudiante']);
        
        if ($request->filled('seccion_id')) {
            $query->whereHas('inscripcion', function($q) use ($request) {
                $q->where('seccion_id', $request->seccion_id);
            });
        }
        
        $asistencias = $query->get();
        $agrupado = $asistencias->groupBy('inscripcion.estudiante.id');
        
        $row = 4;
        foreach ($agrupado as $estudianteId => $asistenciasEst) {
            $estudiante = $asistenciasEst->first()->inscripcion->estudiante;
            $total = $asistenciasEst->count();
            $presentes = $asistenciasEst->where('estado', 'presente')->count();
            $tardanzas = $asistenciasEst->where('estado', 'tardanza')->count();
            $porcentaje = $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 2) : 0;
            
            $sheet->setCellValue('A' . $row, $estudiante->name);
            $sheet->setCellValue('B' . $row, $estudiante->email);
            $sheet->setCellValue('C' . $row, $total);
            $sheet->setCellValue('D' . $row, $presentes);
            $sheet->setCellValue('E' . $row, $asistenciasEst->where('estado', 'ausente')->count());
            $sheet->setCellValue('F' . $row, $tardanzas);
            $sheet->setCellValue('G' . $row, $asistenciasEst->where('estado', 'justificada')->count());
            $sheet->setCellValue('H' . $row, $porcentaje . '%');
            
            $row++;
        }
        
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
    
    private function generarExcelRendimiento($sheet, $request)
    {
        $sheet->setCellValue('A1', 'REPORTE DE RENDIMIENTO ACADÉMICO');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['Código', 'Materia', 'Carrera', 'Nivel', 'Horas/Sem', 'Estudiantes', 'Aprobados', 'Reprobados', '% Aprobación', 'Promedio', 'Estado'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $col++;
        }
        
        $sheet->getStyle('A3:K3')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']]
        ]);
        
        $query = Inscripcion::with(['seccion.curso'])->where('estado', '!=', 'retirado');
        
        if ($request->filled('carrera')) {
            $query->whereHas('seccion.curso', function($q) use ($request) {
                $q->where('carrera', $request->carrera);
            });
        }
        
        $inscripciones = $query->get();
        $agrupado = $inscripciones->groupBy('seccion.curso.codigo_curso');
        
        $row = 4;
        foreach ($agrupado as $codigo => $inscMat) {
            $curso = $inscMat->first()->seccion->curso;
            $total = $inscMat->count();
            $aprobados = $inscMat->where('esta_aprobado', true)->count();
            $promedio = $inscMat->whereNotNull('nota_final')->avg('nota_final');
            $porcentaje = $total > 0 ? round(($aprobados / $total) * 100, 2) : 0;
            $estado = $porcentaje >= 85 ? 'Excelente' : ($porcentaje >= 70 ? 'Bueno' : ($porcentaje >= 60 ? 'Regular' : 'Crítico'));
            
            $sheet->setCellValue('A' . $row, $curso->codigo_curso);
            $sheet->setCellValue('B' . $row, $curso->nombre);
            $sheet->setCellValue('C' . $row, $curso->carrera);
            $sheet->setCellValue('D' . $row, $curso->nivel ?? 'N/A');
            $sheet->setCellValue('E' . $row, $curso->horas_semanales);
            $sheet->setCellValue('F' . $row, $total);
            $sheet->setCellValue('G' . $row, $aprobados);
            $sheet->setCellValue('H' . $row, $inscMat->where('esta_aprobado', false)->count());
            $sheet->setCellValue('I' . $row, $porcentaje . '%');
            $sheet->setCellValue('J' . $row, $promedio ? number_format($promedio, 2) : 'N/A');
            $sheet->setCellValue('K' . $row, $estado);
            
            $row++;
        }
        
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}