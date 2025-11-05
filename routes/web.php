<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AsistenciaController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema de Calificaciones
|--------------------------------------------------------------------------
*/

// Ruta principal - Redireccionar al login o dashboard según autenticación
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->tipo_usuario === 'administrador') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->tipo_usuario === 'profesor') {
            return redirect()->route('profesor.dashboard');
        } else {
            return redirect()->route('estudiante.dashboard');
        }
    }
    return redirect()->route('login');
});

// Rutas de autenticación básicas
Route::get('login', function() {
    return view('auth.login-modern');
})->name('login');

Route::post('login', function(Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();

        // Redirigir según el tipo de usuario
        $user = Auth::user();
        if ($user->tipo_usuario === 'administrador') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->tipo_usuario === 'profesor') {
            return redirect()->route('profesor.dashboard');
        } else {
            return redirect()->route('estudiante.dashboard');
        }
    }

    return back()->withErrors([
        'email' => 'Las credenciales no coinciden con nuestros registros.'
    ]);
})->middleware('throttle:5,1')->name('login.post');

Route::post('logout', function() {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS DEL ADMINISTRADOR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/estadisticas', [App\Http\Controllers\Admin\DashboardController::class, 'estadisticas'])->name('estadisticas');
    
    // Usuarios
    Route::resource('usuarios', App\Http\Controllers\Admin\UsuarioController::class);
    Route::get('usuarios/estudiantes/lista', [App\Http\Controllers\Admin\UsuarioController::class, 'estudiantes'])->name('usuarios.estudiantes');
    Route::get('usuarios/profesores/lista', [App\Http\Controllers\Admin\UsuarioController::class, 'profesores'])->name('usuarios.profesores');
    Route::post('usuarios/{usuario}/cambiar-estado', [App\Http\Controllers\Admin\UsuarioController::class, 'cambiarEstado'])->name('usuarios.cambiar-estado');
    
    // Cursos
    Route::resource('cursos', App\Http\Controllers\Admin\CursoController::class);
    Route::post('cursos/{curso}/cambiar-estado', [App\Http\Controllers\Admin\CursoController::class, 'cambiarEstado'])->name('cursos.cambiar-estado');
    Route::post('cursos/{curso}/duplicar', [App\Http\Controllers\Admin\CursoController::class, 'duplicar'])->name('cursos.duplicar');
    Route::get('cursos/carrera/{carrera}', [App\Http\Controllers\Admin\CursoController::class, 'porCarrera'])->name('cursos.por-carrera');
    Route::get('cursos/estadisticas/general', [App\Http\Controllers\Admin\CursoController::class, 'estadisticas'])->name('cursos.estadisticas');
    
    // Secciones
    Route::resource('secciones', App\Http\Controllers\Admin\SeccionController::class);
    Route::post('secciones/{seccion}/cambiar-estado', [App\Http\Controllers\Admin\SeccionController::class, 'cambiarEstado'])->name('secciones.cambiar-estado');
    
    // Horarios de Secciones
    Route::prefix('secciones/{seccion}/horarios')->name('horarios.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\HorarioController::class, 'index'])->name('index');
        Route::get('/crear', [App\Http\Controllers\Admin\HorarioController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\HorarioController::class, 'store'])->name('store');
        Route::get('/{horario}/editar', [App\Http\Controllers\Admin\HorarioController::class, 'edit'])->name('edit');
        Route::put('/{horario}', [App\Http\Controllers\Admin\HorarioController::class, 'update'])->name('update');
        Route::delete('/{horario}', [App\Http\Controllers\Admin\HorarioController::class, 'destroy'])->name('destroy');
    });
    
    // Períodos Académicos
    Route::resource('periodos', App\Http\Controllers\Admin\PeriodoAcademicoController::class);
    Route::post('periodos/{periodo}/cambiar-estado', [App\Http\Controllers\Admin\PeriodoAcademicoController::class, 'cambiarEstado'])->name('periodos.cambiar-estado');
    Route::get('periodos/vigente/actual', [App\Http\Controllers\Admin\PeriodoAcademicoController::class, 'vigente'])->name('periodos.vigente');
    
    // ==========================================
// INSCRIPCIONES - ORDEN CORRECTO ✅
// ==========================================

// 🔍 1. RUTAS DE BÚSQUEDA (más específicas primero)
Route::get('inscripciones/search-estudiantes', [App\Http\Controllers\Admin\InscripcionController::class, 'searchEstudiantes'])
    ->name('inscripciones.search-estudiantes');

Route::get('inscripciones/search-secciones', [App\Http\Controllers\Admin\InscripcionController::class, 'searchSecciones'])
    ->name('inscripciones.search-secciones');

// 📊 2. RUTAS MASIVAS (antes del resource)
Route::get('inscripciones/masiva', [App\Http\Controllers\Admin\InscripcionController::class, 'createMasiva'])
    ->name('inscripciones.create-masiva');

Route::post('inscripciones/masiva', [App\Http\Controllers\Admin\InscripcionController::class, 'storeMasiva'])
    ->name('inscripciones.store-masiva');

// 📝 3. RESOURCE CRUD (después de las rutas específicas)
Route::resource('inscripciones', App\Http\Controllers\Admin\InscripcionController::class)
    ->parameters(['inscripciones' => 'inscripcion']);

// ⚙️ 4. ACCIONES ADICIONALES (después del resource)
Route::post('inscripciones/{inscripcion}/cambiar-estado', [App\Http\Controllers\Admin\InscripcionController::class, 'cambiarEstado'])
    ->name('inscripciones.cambiar-estado');

Route::post('inscripciones/{inscripcion}/retirar', [App\Http\Controllers\Admin\InscripcionController::class, 'retirar'])
    ->name('inscripciones.retirar');

Route::post('inscripciones/{inscripcion}/completar', [App\Http\Controllers\Admin\InscripcionController::class, 'completar'])
    ->name('inscripciones.completar');
    
    // ==========================================
    // FIN INSCRIPCIONES
    // ==========================================
    
    // Tipos de Evaluación
    Route::resource('tipos-evaluacion', App\Http\Controllers\Admin\TipoEvaluacionController::class);
    
    // Evaluaciones (Admin) - Rutas especiales ANTES del resource
    Route::post('evaluaciones/{evaluacion}/cambiar-estado', [App\Http\Controllers\Admin\EvaluacionController::class, 'cambiarEstado'])->name('evaluaciones.cambiar-estado');
    Route::resource('evaluaciones', App\Http\Controllers\Admin\EvaluacionController::class);
    
    // Calificaciones - Rutas especiales ANTES del resource
    Route::get('calificaciones/masiva', [App\Http\Controllers\Admin\CalificacionController::class, 'masiva'])->name('calificaciones.masiva');
    Route::post('calificaciones/masiva/procesar', [App\Http\Controllers\Admin\CalificacionController::class, 'procesarMasiva'])->name('calificaciones.procesarMasiva');
    Route::get('calificaciones/reporte', [App\Http\Controllers\Admin\CalificacionController::class, 'reporte'])->name('calificaciones.reporte');
    Route::get('calificaciones/pendientes/lista', [App\Http\Controllers\Admin\CalificacionController::class, 'pendientes'])->name('calificaciones.pendientes');
    
    // Calificaciones - CRUD Resource
    Route::post('calificaciones/limpiar-huerfanas', [App\Http\Controllers\Admin\CalificacionController::class, 'limpiarHuerfanas'])->name('calificaciones.limpiar-huerfanas');
    Route::resource('calificaciones', App\Http\Controllers\Admin\CalificacionController::class)
    ->parameters(['calificaciones' => 'calificacion']);
    
    // Calificaciones - Acciones adicionales
    Route::post('calificaciones/{calificacion}/cambiar-estado', [App\Http\Controllers\Admin\CalificacionController::class, 'cambiarEstado'])->name('calificaciones.cambiar-estado');
    Route::post('calificaciones/{calificacion}/revisar', [App\Http\Controllers\Admin\CalificacionController::class, 'revisar'])->name('calificaciones.revisar');
    
    // Rutas de Asistencias
    Route::prefix('asistencias')->name('asistencias.')->group(function () {
    Route::get('/', [AsistenciaController::class, 'index'])->name('index');
    Route::get('/crear', [AsistenciaController::class, 'create'])->name('create');
    Route::post('/', [AsistenciaController::class, 'store'])->name('store');
    Route::get('/{asistencia}', [AsistenciaController::class, 'show'])->name('show');
    Route::get('/{asistencia}/editar', [AsistenciaController::class, 'edit'])->name('edit');
    Route::put('/{asistencia}', [AsistenciaController::class, 'update'])->name('update');
    Route::delete('/{asistencia}', [AsistenciaController::class, 'destroy'])->name('destroy');
    Route::get('/reporte/general', [AsistenciaController::class, 'reporte'])->name('reporte');
});
    // Configuraciones - Las rutas especiales PRIMERO antes del resource
    Route::get('configuraciones/sistema', [App\Http\Controllers\Admin\ConfiguracionController::class, 'sistema'])->name('configuraciones.sistema');
    Route::put('configuraciones/sistema/actualizar', [App\Http\Controllers\Admin\ConfiguracionController::class, 'actualizarSistema'])->name('configuraciones.sistema.actualizar');
    
    // CRUD de Configuraciones
    Route::resource('configuraciones', App\Http\Controllers\Admin\ConfiguracionController::class)
    ->parameters(['configuraciones' => 'configuracion']);
    
    // Reportes
    Route::get('reportes', [App\Http\Controllers\Admin\ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/estudiantes', [App\Http\Controllers\Admin\ReporteController::class, 'estudiantes'])->name('reportes.estudiantes');
    Route::get('reportes/calificaciones', [App\Http\Controllers\Admin\ReporteController::class, 'calificaciones'])->name('reportes.calificaciones');
    Route::get('reportes/asistencias', [App\Http\Controllers\Admin\ReporteController::class, 'asistencias'])->name('reportes.asistencias');
    Route::get('reportes/rendimiento', [App\Http\Controllers\Admin\ReporteController::class, 'rendimientoAcademico'])->name('reportes.rendimiento');
    Route::get('reportes/exportar-pdf', [App\Http\Controllers\Admin\ReporteController::class, 'exportarPDF'])->name('reportes.exportar-pdf');
    Route::get('reportes/exportar-excel', [App\Http\Controllers\Admin\ReporteController::class, 'exportarExcel'])->name('reportes.exportar-excel');
});
/*
|--------------------------------------------------------------------------
| RUTAS DEL PROFESOR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:profesor'])->prefix('profesor')->name('profesor.')->group(function () {
    
    // Dashboard
    Route::get('/', [App\Http\Controllers\Profesor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/graficos', [App\Http\Controllers\Profesor\DashboardController::class, 'getDatosGraficos'])->name('dashboard.graficos');
    
    // Secciones
    Route::get('secciones', [App\Http\Controllers\Profesor\SeccionController::class, 'index'])->name('secciones.index');
    Route::get('secciones/{seccion}', [App\Http\Controllers\Profesor\SeccionController::class, 'show'])->name('secciones.show');
    Route::put('secciones/{seccion}', [App\Http\Controllers\Profesor\SeccionController::class, 'update'])->name('secciones.update');
    Route::get('secciones/{seccion}/estudiantes', [App\Http\Controllers\Profesor\SeccionController::class, 'estudiantes'])->name('secciones.estudiantes');
    Route::get('secciones/{seccion}/buscar-estudiantes', [App\Http\Controllers\Profesor\SeccionController::class, 'buscarEstudiantes'])->name('secciones.buscar-estudiantes');
    Route::post('secciones/{seccion}/agregar-estudiante', [App\Http\Controllers\Profesor\SeccionController::class, 'agregarEstudiante'])->name('secciones.agregar-estudiante');
    Route::delete('secciones/{seccion}/remover-estudiante/{estudiante}', [App\Http\Controllers\Profesor\SeccionController::class, 'removerEstudiante'])->name('secciones.remover-estudiante');
    Route::get('secciones/{seccion}/exportar-estudiantes-pdf', [App\Http\Controllers\Profesor\SeccionController::class, 'exportarEstudiantesPDF'])->name('secciones.exportar-estudiantes-pdf');
    
    // Evaluaciones
    Route::post('evaluaciones/{evaluacion}/cambiar-estado', [App\Http\Controllers\Profesor\EvaluacionController::class, 'cambiarEstado'])->name('evaluaciones.cambiar-estado');
    Route::post('evaluaciones/{evaluacion}/duplicar', [App\Http\Controllers\Profesor\EvaluacionController::class, 'duplicar'])->name('evaluaciones.duplicar');
    Route::get('evaluaciones/{evaluacion}/estadisticas', [App\Http\Controllers\Profesor\EvaluacionController::class, 'estadisticas'])->name('evaluaciones.estadisticas');
    Route::resource('evaluaciones', App\Http\Controllers\Profesor\EvaluacionController::class);
    
    // Calificaciones
    Route::get('evaluaciones/{evaluacion}/calificaciones', [App\Http\Controllers\Profesor\CalificacionController::class, 'index'])->name('calificaciones.index');
    Route::get('calificaciones/{calificacion}/editar', [App\Http\Controllers\Profesor\CalificacionController::class, 'edit'])->name('calificaciones.edit');
    Route::put('calificaciones/{calificacion}', [App\Http\Controllers\Profesor\CalificacionController::class, 'update'])->name('calificaciones.update');
    Route::post('evaluaciones/{evaluacion}/calificaciones/lote', [App\Http\Controllers\Profesor\CalificacionController::class, 'calificarLote'])->name('calificaciones.lote');
    
    // Asistencias
    Route::get('asistencias', [App\Http\Controllers\Profesor\AsistenciaController::class, 'listarSecciones'])->name('asistencias.index');
    Route::get('asistencias/{seccion}', [App\Http\Controllers\Profesor\AsistenciaController::class, 'index'])->name('asistencias.show');
    Route::get('asistencias/{seccion}/tomar/{fecha?}', [App\Http\Controllers\Profesor\AsistenciaController::class, 'tomarAsistencia'])->name('asistencias.tomar');
    Route::post('asistencias/{seccion}/registrar', [App\Http\Controllers\Profesor\AsistenciaController::class, 'registrar'])->name('asistencias.registrar');
    Route::get('asistencias/{seccion}/reporte-estudiante/{estudiante}', [App\Http\Controllers\Profesor\AsistenciaController::class, 'reporteEstudiante'])->name('asistencias.reporte-estudiante');

    // Perfil
    Route::get('perfil', [App\Http\Controllers\Profesor\PerfilController::class, 'index'])->name('perfil.index');
    Route::put('perfil', [App\Http\Controllers\Profesor\PerfilController::class, 'update'])->name('perfil.update');
    Route::put('perfil/password', [App\Http\Controllers\Profesor\PerfilController::class, 'cambiarPassword'])->name('perfil.cambiar-password');
    Route::post('perfil/foto', [App\Http\Controllers\Profesor\PerfilController::class, 'subirFoto'])->name('perfil.subir-foto');
    Route::delete('perfil/foto', [App\Http\Controllers\Profesor\PerfilController::class, 'eliminarFoto'])->name('perfil.eliminar-foto');

    // Horario
    Route::get('horario', [App\Http\Controllers\Profesor\HorarioController::class, 'index'])->name('horario.index');
    Route::get('horario/pdf', [App\Http\Controllers\Profesor\HorarioController::class, 'descargarPDF'])->name('horario.pdf');

    // Reportes
    Route::get('reportes', [App\Http\Controllers\Profesor\ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/calificaciones', [App\Http\Controllers\Profesor\ReporteController::class, 'calificaciones'])->name('reportes.calificaciones');
    Route::get('reportes/asistencias', [App\Http\Controllers\Profesor\ReporteController::class, 'asistencias'])->name('reportes.asistencias');
    Route::get('reportes/rendimiento', [App\Http\Controllers\Profesor\ReporteController::class, 'rendimiento'])->name('reportes.rendimiento');
    Route::get('reportes/evaluaciones', [App\Http\Controllers\Profesor\ReporteController::class, 'evaluaciones'])->name('reportes.evaluaciones');
    Route::get('reportes/exportar-pdf', [App\Http\Controllers\Profesor\ReporteController::class, 'exportarPDF'])->name('reportes.exportar-pdf');
    Route::get('reportes/exportar-excel', [App\Http\Controllers\Profesor\ReporteController::class, 'exportarExcel'])->name('reportes.exportar-excel');

    // Configuraciones
    Route::get('configuraciones', [App\Http\Controllers\Profesor\ConfiguracionController::class, 'index'])->name('configuraciones.index');
    Route::put('configuraciones/notificaciones', [App\Http\Controllers\Profesor\ConfiguracionController::class, 'actualizarNotificaciones'])->name('configuraciones.notificaciones');
    Route::put('configuraciones/visualizacion', [App\Http\Controllers\Profesor\ConfiguracionController::class, 'actualizarVisualizacion'])->name('configuraciones.visualizacion');

    // Mensajes (Placeholder)
    Route::get('mensajes', function() {
        return view('profesor.mensajes.index');
    })->name('mensajes.index');
});

/*
|--------------------------------------------------------------------------
| RUTAS DEL ESTUDIANTE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:estudiante'])->prefix('estudiante')->name('estudiante.')->group(function () {
    
    // Dashboard
    Route::get('/', [App\Http\Controllers\Estudiante\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/graficos', [App\Http\Controllers\Estudiante\DashboardController::class, 'getDatosGraficos'])->name('dashboard.graficos');
    
    // Inscripciones - Rutas específicas PRIMERO
    Route::get('inscripciones', [App\Http\Controllers\Estudiante\InscripcionController::class, 'index'])->name('inscripciones.index');
    Route::get('inscripciones/disponibles/lista', [App\Http\Controllers\Estudiante\InscripcionController::class, 'disponibles'])->name('inscripciones.disponibles');
    Route::get('inscripciones/historial/academico', [App\Http\Controllers\Estudiante\InscripcionController::class, 'historial'])->name('inscripciones.historial');
    Route::post('inscripciones/{seccionId}/inscribirse', [App\Http\Controllers\Estudiante\InscripcionController::class, 'inscribirse'])->name('inscripciones.inscribirse');
    Route::get('inscripciones/{inscripcionId}', [App\Http\Controllers\Estudiante\InscripcionController::class, 'show'])->name('inscripciones.show');
    
    // Calificaciones - Rutas específicas PRIMERO
    Route::get('calificaciones', [App\Http\Controllers\Estudiante\CalificacionController::class, 'index'])->name('calificaciones.index');
    Route::get('calificaciones/progreso', [App\Http\Controllers\Estudiante\CalificacionController::class, 'progreso'])->name('calificaciones.progreso');
    Route::get('calificaciones/comparar/{inscripcionId}', [App\Http\Controllers\Estudiante\CalificacionController::class, 'compararRendimiento'])->name('calificaciones.comparar');
    Route::get('calificaciones/exportar/{inscripcionId}', [App\Http\Controllers\Estudiante\CalificacionController::class, 'exportar'])->name('calificaciones.exportar');
    Route::get('calificaciones/materia/{inscripcionId}', [App\Http\Controllers\Estudiante\CalificacionController::class, 'porMateria'])->name('calificaciones.materia');
    Route::get('calificaciones/{calificacionId}', [App\Http\Controllers\Estudiante\CalificacionController::class, 'show'])->name('calificaciones.show');
    
    // Asistencias
    Route::get('asistencias', [App\Http\Controllers\Estudiante\AsistenciaController::class, 'index'])->name('asistencias.index');
    Route::get('asistencias/reporte', [App\Http\Controllers\Estudiante\AsistenciaController::class, 'reporte'])->name('asistencias.reporte');
    Route::get('asistencias/calendario/{inscripcionId}', [App\Http\Controllers\Estudiante\AsistenciaController::class, 'calendario'])->name('asistencias.calendario');
    Route::get('asistencias/exportar/{inscripcionId?}', [App\Http\Controllers\Estudiante\AsistenciaController::class, 'exportar'])->name('asistencias.exportar');
    Route::get('asistencias/graficos/{inscripcionId}', [App\Http\Controllers\Estudiante\AsistenciaController::class, 'getDatosGraficos'])->name('asistencias.graficos');
    Route::get('asistencias/{asistenciaId}/detalle', [App\Http\Controllers\Estudiante\AsistenciaController::class, 'show'])->name('asistencias.show');
    Route::get('asistencias/materia/{inscripcionId}', [App\Http\Controllers\Estudiante\AsistenciaController::class, 'porMateria'])->name('asistencias.materia');
    
    // Horario
    Route::get('horario', [App\Http\Controllers\Estudiante\HorarioController::class, 'index'])->name('horario.index');
    Route::get('horario/pdf', [App\Http\Controllers\Estudiante\HorarioController::class, 'descargarPDF'])->name('horario.pdf');
    
    // Perfil
    Route::get('perfil', [App\Http\Controllers\Estudiante\PerfilController::class, 'index'])->name('perfil.index');
    Route::put('perfil', [App\Http\Controllers\Estudiante\PerfilController::class, 'update'])->name('perfil.update');
    Route::put('perfil/password', [App\Http\Controllers\Estudiante\PerfilController::class, 'cambiarPassword'])->name('perfil.cambiar-password');
    Route::post('perfil/foto', [App\Http\Controllers\Estudiante\PerfilController::class, 'subirFoto'])->name('perfil.subir-foto');
    Route::delete('perfil/foto', [App\Http\Controllers\Estudiante\PerfilController::class, 'eliminarFoto'])->name('perfil.eliminar-foto');
    
    // Tareas (Placeholder para futuro desarrollo)
    Route::get('tareas', function() {
        return view('estudiante.tareas.index');
    })->name('tareas.index');
    
    // Mensajes (Placeholder para futuro desarrollo)
    Route::get('mensajes', function() {
        return view('estudiante.mensajes.index');
    })->name('mensajes.index');
});

/*
|--------------------------------------------------------------------------
| NOTIFICACIONES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('notificaciones', function() {
        $usuario = \App\Models\Usuario::find(auth()->id());
        $notificaciones = $usuario->notificaciones()->orderBy('created_at', 'desc')->get();
        return view('notificaciones.index', compact('notificaciones'));
    })->name('notificaciones.index');
});