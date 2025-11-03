<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Models\Evaluacion;
use App\Models\Calificacion;

class PerfilController extends Controller
{
    public function index()
    {
        $profesor = auth()->user();
        
        // Obtener estadísticas
        $stats = [
            'secciones' => [
                'value' => $profesor->secciones()->count(),
                'label' => 'Secciones Asignadas',
                'icon' => 'fa-chalkboard',
                'color' => 'bg-primary'
            ],
            'estudiantes' => [
                'value' => $profesor->secciones()
                    ->withCount('inscripciones')
                    ->get()
                    ->sum('inscripciones_count'),
                'label' => 'Estudiantes Totales',
                'icon' => 'fa-user-graduate',
                'color' => 'bg-success'
            ],
            'evaluaciones' => [
                'value' => $profesor->secciones()
                    ->with(['evaluaciones' => function($query) {
                        $query->where('estado', 'activo');
                    }])
                    ->get()
                    ->pluck('evaluaciones')
                    ->flatten()
                    ->count(),
                'label' => 'Evaluaciones Activas',
                'icon' => 'fa-file-alt',
                'color' => 'bg-warning'
            ],
            'pendientes' => [
                'value' => $this->calcularCalificacionesPendientes($profesor),
                'label' => 'Por Calificar',
                'icon' => 'fa-star',
                'color' => 'bg-info'
            ]
        ];
        
        // Cambiar nombre de variable para que coincida con la vista
        $usuario = $profesor;
        
        return view('profesor.perfil.index', compact('usuario', 'stats'));
    }
    
    private function calcularCalificacionesPendientes($profesor)
    {
        // Obtener todas las evaluaciones activas de las secciones del profesor
        $evaluaciones = Evaluacion::whereHas('seccion', function($query) use ($profesor) {
            $query->where('profesor_id', $profesor->id);
        })->where('estado', 'activo')->get();
        
        $pendientes = 0;
        foreach ($evaluaciones as $evaluacion) {
            // Contar inscripciones activas sin calificación o con calificación null
            $totalEstudiantes = $evaluacion->seccion->inscripciones()
                ->where('estado', 'inscrito')
                ->count();
            
            $calificados = Calificacion::where('evaluacion_id', $evaluacion->id)
                ->whereNotNull('nota')
                ->count();
            
            $pendientes += ($totalEstudiantes - $calificados);
        }
        
        return max(0, $pendientes); // Asegurar que nunca sea negativo
    }
    
    public function update(Request $request)
    {
        $profesor = auth()->user();
        
        $validated = $request->validate([
            'email' => 'required|email|unique:usuarios,email,' . $profesor->id,
            'telefono' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date|before:today',
            'direccion' => 'nullable|string|max:500',
        ], [
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Debe ingresar un email válido',
            'email.unique' => 'Este email ya está registrado',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'direccion.max' => 'La dirección no puede exceder los 500 caracteres'
        ]);
        
        try {
            $profesor->update($validated);
            
            return redirect()->route('profesor.perfil.index')
                ->with('success', '✓ Perfil actualizado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('profesor.perfil.index')
                ->with('error', 'Error al actualizar el perfil: ' . $e->getMessage());
        }
    }
    
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password_actual.required' => 'Debe ingresar su contraseña actual',
            'password.required' => 'La nueva contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres'
        ]);
        
        $profesor = auth()->user();
        
        if (!Hash::check($request->password_actual, $profesor->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta']);
        }
        
        try {
            $profesor->update([
                'password' => Hash::make($request->password)
            ]);
            
            return redirect()->route('profesor.perfil.index')
                ->with('success', '✓ Contraseña actualizada correctamente');
        } catch (\Exception $e) {
            return redirect()->route('profesor.perfil.index')
                ->with('error', 'Error al cambiar la contraseña: ' . $e->getMessage());
        }
    }
    
    public function subirFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'foto.required' => 'Debe seleccionar una imagen',
            'foto.image' => 'El archivo debe ser una imagen',
            'foto.mimes' => 'Solo se permiten imágenes JPG, JPEG o PNG',
            'foto.max' => 'La imagen no puede pesar más de 2MB'
        ]);
        
        $profesor = auth()->user();
        
        try {
            // Eliminar foto anterior si existe
            if ($profesor->foto_perfil) {
                Storage::disk('public')->delete($profesor->foto_perfil);
            }
            
            // Guardar nueva foto con nombre único
            $filename = 'perfil_' . $profesor->id . '_' . time() . '.' . $request->file('foto')->getClientOriginalExtension();
            $path = $request->file('foto')->storeAs('fotos_perfil', $filename, 'public');
            
            $profesor->update([
                'foto_perfil' => $path
            ]);
            
            return redirect()->route('profesor.perfil.index')
                ->with('success', '✓ Foto de perfil actualizada correctamente');
        } catch (\Exception $e) {
            return redirect()->route('profesor.perfil.index')
                ->with('error', 'Error al subir la foto: ' . $e->getMessage());
        }
    }
    
    public function eliminarFoto()
    {
        $profesor = auth()->user();
        
        try {
            if ($profesor->foto_perfil) {
                Storage::disk('public')->delete($profesor->foto_perfil);
                $profesor->update(['foto_perfil' => null]);
                
                return redirect()->route('profesor.perfil.index')
                    ->with('success', '✓ Foto de perfil eliminada correctamente');
            }
            
            return redirect()->route('profesor.perfil.index')
                ->with('error', 'No hay foto de perfil para eliminar');
        } catch (\Exception $e) {
            return redirect()->route('profesor.perfil.index')
                ->with('error', 'Error al eliminar la foto: ' . $e->getMessage());
        }
    }
}