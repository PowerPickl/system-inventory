<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next, $role): Response
    {
        if (Auth::check()) {
            $userRole = Auth::user()->role->nama_role;
            
            // Support backward compatibility: Kasir = Service Advisor
            if (($role === 'Kasir' && $userRole === 'Service Advisor') || 
                ($role === 'Service Advisor' && $userRole === 'Service Advisor') ||
                ($userRole === $role)) {
                return $next($request);
            }
        }

        return redirect('/unauthorized');
    }
}
