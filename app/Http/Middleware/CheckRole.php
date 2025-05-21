<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        // Verificar que el usuario esté autenticado
        if (!Auth::check()) {
            return redirect('login');
        }

        // Si no se especificó un rol, continuar
        if ($role === null) {
            return $next($request);
        }
 /** @var \App\Models\User $user */
        // Verificar si el usuario tiene el rol requerido
        $user = Auth::user();
        
        if ($role === 'administrador' && $user->isAdmin()) {
            return $next($request);
        }
        
        if ($role === 'doctor' && $user->isDoctor()) {
            return $next($request);
        }
        
        if ($role === 'paciente' && $user->isPaciente()) {
            return $next($request);
        }

        // Si el usuario no tiene el rol requerido, redireccionar a la página de inicio
        // o mostrar un mensaje de error
        return redirect()->route('home')->with('error', 'No tiene permisos para acceder a esta página');
    }
}