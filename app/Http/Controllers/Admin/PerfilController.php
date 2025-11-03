<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class PerfilController extends Controller
{
    public function index()
    {
        $admin = auth()->user();
        
        // Obtener estadísticas del sistema
        $stats = [
            'total_usuarios' => \App\Models\Usuario::count(),
            'total_estudiantes' => \App\Models\Usuario::where('tipo_usuario', 'estudiante')->count(),
            'total_profesores' => \App\Models\Usuario::where('tipo_usuario', 'profesor')->count(),
            'total_cursos' => \App\Models\Curso::count(),
        ];
        
        return view('admin.perfil.index', compact('admin', 'stats'));
    }
    
    public function update(Request $request)
    {
        $admin = auth()->user();
        
        $validated = $request->validate([
            'email' => 'required|email|unique:usuarios,email,' . $admin->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
        ]);
        
        $admin->update($validated);
        
        return redirect()->route('admin.perfil.index')
            ->with('success', 'Perfil actualizado correctamente');
    }
    
    public function cambiarPassword(Request $request)
    {
        $request->validate([
            'password_actual' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        
        $admin = auth()->user();
        
        if (!Hash::check($request->password_actual, $admin->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta']);
        }
        
        $admin->update([
            'password' => Hash::make($request->password)
        ]);
        
        return redirect()->route('admin.perfil.index')
            ->with('success', 'Contraseña actualizada correctamente');
    }
    
    public function subirFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        $admin = auth()->user();
        
        if ($admin->foto_perfil) {
            Storage::disk('public')->delete($admin->foto_perfil);
        }
        
        $path = $request->file('foto')->store('fotos_perfil', 'public');
        
        $admin->update([
            'foto_perfil' => $path
        ]);
        
        return redirect()->route('admin.perfil.index')
            ->with('success', 'Foto de perfil actualizada correctamente');
    }
    
    public function eliminarFoto()
    {
        $admin = auth()->user();
        
        if ($admin->foto_perfil) {
            Storage::disk('public')->delete($admin->foto_perfil);
            $admin->update(['foto_perfil' => null]);
        }
        
        return redirect()->route('admin.perfil.index')
            ->with('success', 'Foto de perfil eliminada correctamente');
    }
}