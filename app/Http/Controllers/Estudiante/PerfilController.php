<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
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
     * Mostrar perfil del estudiante
     */
    public function index()
    {
        $estudiante = Auth::user();
        
        // Obtener estadísticas adicionales
        $stats = [
            'total_inscripciones' => $estudiante->inscripciones()->count(),
            'materias_aprobadas' => $estudiante->inscripciones()->where('esta_aprobado', true)->count(),
            'creditos_acumulados' => $estudiante->inscripciones()
                ->where('esta_aprobado', true)
                ->with('seccion.curso')
                ->get()
                ->sum(function($ins) {
                    return $ins->seccion->curso->creditos ?? 0;
                }),
            'promedio_general' => $estudiante->inscripciones()
                ->whereNotNull('nota_final')
                ->avg('nota_final')
        ];

        return view('estudiante.perfil.index', compact('estudiante', 'stats'));
    }

    /**
     * Actualizar información del perfil
     */
    public function update(Request $request)
    {
        $estudiante = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:usuarios,email,' . $estudiante->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        $estudiante->update([
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'fecha_nacimiento' => $request->fecha_nacimiento,
        ]);

        return redirect()->route('estudiante.perfil.index')
            ->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $estudiante = Auth::user();

        // Verificar contraseña actual
        if (!Hash::check($request->password_actual, $estudiante->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta']);
        }

        // Actualizar contraseña
        $estudiante->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('estudiante.perfil.index')
            ->with('success', 'Contraseña actualizada correctamente');
    }

    /**
     * Subir foto de perfil
     */
    public function subirFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $estudiante = Auth::user();

        // Eliminar foto anterior si existe
        if ($estudiante->foto_perfil && Storage::exists('public/' . $estudiante->foto_perfil)) {
            Storage::delete('public/' . $estudiante->foto_perfil);
        }

        // Guardar nueva foto
        $path = $request->file('foto')->store('perfiles', 'public');

        $estudiante->update([
            'foto_perfil' => $path
        ]);

        return redirect()->route('estudiante.perfil.index')
            ->with('success', 'Foto de perfil actualizada correctamente');
    }

    /**
     * Eliminar foto de perfil
     */
    public function eliminarFoto()
    {
        $estudiante = Auth::user();

        if ($estudiante->foto_perfil && Storage::exists('public/' . $estudiante->foto_perfil)) {
            Storage::delete('public/' . $estudiante->foto_perfil);
        }

        $estudiante->update([
            'foto_perfil' => null
        ]);

        return redirect()->route('estudiante.perfil.index')
            ->with('success', 'Foto de perfil eliminada correctamente');
    }
}
