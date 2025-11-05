<?php

namespace App\Http\Controllers\Profesor;

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
            if (Auth::user()->tipo_usuario !== 'profesor') {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar perfil del profesor
     */
    public function index()
    {
        $profesor = Auth::user();

        // Estadísticas o datos básicos si es necesario
        $stats = [
            'total_secciones' => $profesor->secciones()->count() ?? 0,
        ];

        return view('profesor.perfil.index', compact('profesor', 'stats'));
    }

    /**
     * Actualizar información del perfil
     */
    public function update(Request $request)
    {
        $profesor = Auth::user();

        $request->validate([
            'email' => 'required|email|unique:usuarios,email,' . $profesor->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        $profesor->update([
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'fecha_nacimiento' => $request->fecha_nacimiento,
        ]);

        return redirect()->route('perfil.index')
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

        $profesor = Auth::user();

        if (!Hash::check($request->password_actual, $profesor->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual es incorrecta']);
        }

        $profesor->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('perfil.index')
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

        $profesor = Auth::user();

        if ($profesor->foto_perfil && Storage::exists('public/' . $profesor->foto_perfil)) {
            Storage::delete('public/' . $profesor->foto_perfil);
        }

        $path = $request->file('foto')->store('perfiles', 'public');

        $profesor->update([
            'foto_perfil' => $path
        ]);

        return redirect()->route('perfil.index')
            ->with('success', 'Foto de perfil actualizada correctamente');
    }

    /**
     * Eliminar foto de perfil
     */
    public function eliminarFoto()
    {
        $profesor = Auth::user();

        if ($profesor->foto_perfil && Storage::exists('public/' . $profesor->foto_perfil)) {
            Storage::delete('public/' . $profesor->foto_perfil);
        }

        $profesor->update([
            'foto_perfil' => null
        ]);

        return redirect()->route('perfil.index')
            ->with('success', 'Foto de perfil eliminada correctamente');
    }
}
