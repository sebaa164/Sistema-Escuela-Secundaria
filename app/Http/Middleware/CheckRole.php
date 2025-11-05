<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        $user = Auth::user();

        // Verificar si el usuario tiene uno de los roles permitidos
        if (!in_array($user->tipo_usuario, $roles)) {
            // Log de intento de acceso no autorizado
            \Log::warning('Intento de acceso no autorizado', [
                'user_id' => $user->id,
                'email' => $user->email,
                'rol_actual' => $user->tipo_usuario,
                'roles_requeridos' => $roles,
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
            ]);

            // Redirigir al dashboard correspondiente según su rol
            return $this->redirectToDashboard($user->tipo_usuario);
        }

        return $next($request);
    }

    /**
     * Redirige al usuario a su dashboard correspondiente.
     *
     * @param  string  $tipoUsuario
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectToDashboard($tipoUsuario)
    {
        $dashboardRoutes = [
            'administrador' => 'admin.dashboard',
            'profesor' => 'profesor.dashboard',
            'estudiante' => 'estudiante.dashboard',
        ];

        $route = $dashboardRoutes[$tipoUsuario] ?? 'login';

        return redirect()->route($route)->with('error', 'No tienes permisos para acceder a esta sección.');
    }
}
