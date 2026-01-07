<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Si l’utilisateur est déjà connecté, le rediriger vers son tableau de bord.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                // Redirection selon le rôle
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user->role === 'enseignant') {
                    return redirect()->route('enseignant.dashboard');
                } elseif ($user->role === 'eleve') {
                    return redirect()->route('eleve.dashboard');
                }
                
                // Pour les utilisateurs sans rôle spécifique, rediriger vers le tableau de bord général
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}