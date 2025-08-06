<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip check for non-authenticated users
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Check if user is active
        if (!$user->is_active) {
            Log::warning('Inactive user accessed protected route', [
                'user_id' => $user->id,
                'email' => $user->email,
                'route' => $request->route()?->getName(),
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);

            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda telah dinonaktifkan oleh administrator.',
                    'redirect' => route('login'),
                    'logout' => true
                ], 401);
            }

            // Handle regular requests
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan oleh administrator.'])
                ->with([
                    'alert_type' => 'error',
                    'alert_title' => 'Akun Dinonaktifkan',
                    'alert_message' => 'Sesi Anda telah berakhir karena akun dinonaktifkan oleh administrator.'
                ]);
        }

        return $next($request);
    }
}