<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::query();
        
        // Filtros
        if ($request->filled('tipo')) {
            $query->where('tipo_usuario', $request->tipo);
        }
        
        if ($request->filled('estado')) {
            $estadoFiltro = $request->estado;
            $query->where(function($q) use ($estadoFiltro) {
                // Para estudiantes, filtrar por estado_estudiante
                $q->where(function($subQ) use ($estadoFiltro) {
                    $subQ->where('tipo_usuario', 'estudiante')
                         ->where('estado_estudiante', $estadoFiltro);
                })
                // Para otros usuarios, filtrar por estado general
                ->orWhere(function($subQ) use ($estadoFiltro) {
                    $subQ->where('tipo_usuario', '!=', 'estudiante')
                         ->where('estado', $estadoFiltro);
                });
            });
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.usuarios.index', compact('usuarios'));
    }
    
    public function create()
    {
        return view('admin.usuarios.create');
    }
    
    public function store(Request $request)
    {
        // Determinar si es estudiante o profesor
        $esEstudiante = ($request->tipo_usuario === 'estudiante');
        $esProfesor = ($request->tipo_usuario === 'profesor');

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'required|date|before:today',
            'tipo_usuario' => 'required|in:estudiante,profesor,administrador',
            'estado_estudiante' => $esEstudiante ? 'required|in:regular,suspendido,libre,preinscripto' : 'nullable|in:regular,suspendido,libre,preinscripto',
            'estado_profesor' => $esProfesor ? 'required|in:titular,interino,suplente,licencia,jubilado,suspendido' : 'nullable|in:titular,interino,suplente,licencia,jubilado,suspendido',
            'representante_parentesco' => $esEstudiante ? 'required|string|max:50' : 'nullable|string|max:50',
            'representante_nombre' => $esEstudiante ? 'required|string|max:255' : 'nullable|string|max:255',
            'representante_apellido' => $esEstudiante ? 'required|string|max:255' : 'nullable|string|max:255',
            'representante_telefono' => $esEstudiante ? 'required|string|max:20' : 'nullable|string|max:20',
            'representante_email' => 'nullable|email|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nombre.required' => 'Por favor, ingresa el nombre.',
            'apellido.required' => 'Por favor, ingresa el apellido.',
            'email.required' => 'Por favor, ingresa el correo electrónico.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'telefono.required' => 'Por favor, ingresa el número de teléfono.',
            'fecha_nacimiento.required' => 'Por favor, selecciona la fecha de nacimiento.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'tipo_usuario.required' => 'El tipo de usuario es obligatorio.',
            'estado_estudiante.required' => 'Por favor, selecciona el estado académico del estudiante.',
            'estado_profesor.required' => 'Por favor, selecciona el estado del profesor.',
            'representante_parentesco.required' => 'Por favor, selecciona el parentesco del responsable.',
            'representante_nombre.required' => 'Por favor, ingresa el nombre del responsable.',
            'representante_apellido.required' => 'Por favor, ingresa el apellido del responsable.',
            'representante_telefono.required' => 'Por favor, ingresa el teléfono del responsable.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        // Crear tutor si es estudiante y hay datos del representante
        $tutorId = null;
        if ($request->tipo_usuario === 'estudiante' && $request->filled('representante_nombre')) {
            $tutor = Tutor::create([
                'nombre' => $request->representante_nombre,
                'apellido' => $request->representante_apellido,
                'cedula' => $request->representante_cedula ?? null, // Si agregas cédula después
                'telefono' => $request->representante_telefono,
                'email' => $request->representante_email,
                'direccion' => null, // No incluimos dirección por simplicidad
                'parentesco' => $request->representante_parentesco,
            ]);
            $tutorId = $tutor->id;
        }

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'tipo_usuario' => $request->tipo_usuario,
            'estado' => 'activo',
            'estado_estudiante' => $request->estado_estudiante,
            'estado_profesor' => $request->estado_profesor,
            'tutor_id' => $tutorId,
            'password' => Hash::make($request->password),
        ]);

        // Mensaje con las credenciales para que el admin las anote
        $mensaje = 'Usuario creado exitosamente. 📧 Email: ' . $usuario->email . ' | 🔑 Contraseña: ' . $request->password . ' (Anota estas credenciales)';

        return redirect()->route('admin.usuarios.index')
                        ->with('success', $mensaje);
    }
    
    public function show(Usuario $usuario)
    {
        // Cargar relaciones solo si existen los métodos en el modelo
        try {
            // Intenta cargar relaciones de forma segura
            if (method_exists($usuario, 'inscripciones')) {
                $usuario->load('inscripciones');
            }
            if (method_exists($usuario, 'secciones')) {
                $usuario->load('secciones');
            }
            if (method_exists($usuario, 'calificaciones')) {
                $usuario->load('calificaciones');
            }
        } catch (\Exception $e) {
            Log::warning('Error al cargar relaciones del usuario: ' . $e->getMessage());
        }
        
        return view('admin.usuarios.show', compact('usuario'));
    }
    
    public function edit(Usuario $usuario)
    {
        // Cargar tutor si existe
        $tutor = $usuario->tutor;
        return view('admin.usuarios.edit', compact('usuario', 'tutor'));
    }
    
    public function update(Request $request, Usuario $usuario)
    {
        // ✅ NUEVO: Si el usuario original es estudiante, forzar que siga siendo estudiante
        if ($usuario->tipo_usuario === 'estudiante') {
            $request->merge(['tipo_usuario' => 'estudiante']);
        }

        // Determinar si es estudiante (actual o nuevo)
        $esEstudiante = ($request->tipo_usuario === 'estudiante') || ($usuario->tipo_usuario === 'estudiante');

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:usuarios,email,'.$usuario->id,
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'required|date|before:today',
            'tipo_usuario' => 'required|in:estudiante,profesor,administrador',
            'estado' => 'required_if:tipo_usuario,profesor,administrador|in:activo,inactivo,suspendido',
            'estado_estudiante' => $esEstudiante ? 'required|in:regular,suspendido,libre,preinscripto' : 'nullable|in:regular,suspendido,libre,preinscripto',
            'representante_parentesco' => $esEstudiante ? 'required|string|max:50' : 'nullable|string|max:50',
            'representante_nombre' => $esEstudiante ? 'required|string|max:255' : 'nullable|string|max:255',
            'representante_apellido' => $esEstudiante ? 'required|string|max:255' : 'nullable|string|max:255',
            'representante_telefono' => $esEstudiante ? 'required|string|max:20' : 'nullable|string|max:20',
            'representante_email' => 'nullable|email|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'nombre.required' => 'Por favor, ingresa el nombre.',
            'apellido.required' => 'Por favor, ingresa el apellido.',
            'email.required' => 'Por favor, ingresa el correo electrónico.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'telefono.required' => 'Por favor, ingresa el número de teléfono.',
            'fecha_nacimiento.required' => 'Por favor, selecciona la fecha de nacimiento.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'tipo_usuario.required' => 'El tipo de usuario es obligatorio.',
            'estado_estudiante.required' => 'Por favor, selecciona el estado académico del estudiante.',
            'representante_parentesco.required' => 'Por favor, selecciona el parentesco del responsable.',
            'representante_nombre.required' => 'Por favor, ingresa el nombre del responsable.',
            'representante_apellido.required' => 'Por favor, ingresa el apellido del responsable.',
            'representante_telefono.required' => 'Por favor, ingresa el teléfono del responsable.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $data = $request->except(['password', 'password_confirmation', 'representante_parentesco', 'representante_nombre', 'representante_apellido', 'representante_telefono', 'representante_email']);
        
        // Manejar estado según tipo de usuario
        if ($request->tipo_usuario === 'estudiante') {
            $data['estado'] = 'activo'; // Los estudiantes siempre están activos, su estado académico es diferente
        }
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Manejar tutor
        if ($request->tipo_usuario === 'estudiante' && $request->filled('representante_nombre')) {
            if ($usuario->tutor) {
                // Actualizar tutor existente
                $usuario->tutor->update([
                    'nombre' => $request->representante_nombre,
                    'apellido' => $request->representante_apellido,
                    'telefono' => $request->representante_telefono,
                    'email' => $request->representante_email,
                    'parentesco' => $request->representante_parentesco,
                ]);
            } else {
                // Crear nuevo tutor
                $tutor = Tutor::create([
                    'nombre' => $request->representante_nombre,
                    'apellido' => $request->representante_apellido,
                    'telefono' => $request->representante_telefono,
                    'email' => $request->representante_email,
                    'parentesco' => $request->representante_parentesco,
                ]);
                $data['tutor_id'] = $tutor->id;
            }
        } elseif ($request->tipo_usuario !== 'estudiante') {
            // Si ya no es estudiante, quitar tutor
            $data['tutor_id'] = null;
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')
                        ->with('success', 'Usuario actualizado exitosamente.');
    }
    
    public function destroy(Usuario $usuario)
    {
        try {
            // Verificar si el usuario tiene inscripciones o secciones activas
            $tieneInscripciones = false;
            $tieneSecciones = false;
            
            if (method_exists($usuario, 'inscripciones')) {
                $tieneInscripciones = $usuario->inscripciones()->where('estado', 'inscrito')->exists();
            }
            
            if (method_exists($usuario, 'secciones')) {
                $tieneSecciones = $usuario->secciones()->where('estado', 'activo')->exists();
            }
            
            if ($tieneInscripciones || $tieneSecciones) {
                return redirect()->back()
                               ->with('error', 'No se puede eliminar el usuario porque tiene inscripciones o secciones activas.');
            }
            
            $usuario->delete();
            
            return redirect()->route('admin.usuarios.index')
                            ->with('success', 'Usuario eliminado exitosamente.');
                            
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
    
    public function cambiarEstado(Request $request, Usuario $usuario)
    {
        $nuevoEstado = $usuario->estado === 'activo' ? 'inactivo' : 'activo';
        
        $usuario->update([
            'estado' => $nuevoEstado
        ]);
        
        return redirect()->back()->with('success', 'Estado actualizado correctamente');
    }
    
    public function estudiantes(Request $request)
    {
        $query = Usuario::where('tipo_usuario', 'estudiante');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('estado')) {
            $query->where('estado_estudiante', $request->estado);
        }
        
        // Intenta cargar el conteo de inscripciones de forma segura
        try {
            $estudiantes = $query->withCount('inscripciones')
                ->orderBy('apellido')
                ->orderBy('nombre')
                ->paginate(15);
        } catch (\Exception $e) {
            $estudiantes = $query->orderBy('apellido')
                ->orderBy('nombre')
                ->paginate(15);
        }
        
        return view('admin.usuarios.estudiantes', compact('estudiantes'));
    }
    
    public function profesores(Request $request)
    {
        $query = Usuario::where('tipo_usuario', 'profesor');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('apellido', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Profesores usan estado general, no necesita cambios
        
        $totalSecciones = 0;
        
        // Intenta cargar el conteo de secciones de forma segura
        try {
            $totalSecciones = Usuario::where('tipo_usuario', 'profesor')
                ->withCount('secciones')
                ->get()
                ->sum('secciones_count');
                
            $profesores = $query->withCount('secciones')
                ->orderBy('apellido')
                ->orderBy('nombre')
                ->paginate(15);
        } catch (\Exception $e) {
            $profesores = $query->orderBy('apellido')
                ->orderBy('nombre')
                ->paginate(15);
        }
        
        return view('admin.usuarios.profesores', compact('profesores', 'totalSecciones'));
    }
}