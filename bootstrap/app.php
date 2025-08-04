<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ REGISTER CUSTOM MIDDLEWARE ALIASES
        $middleware->alias([
            'check.active' => \App\Http\Middleware\CheckActiveUser::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // ✅ GLOBAL MIDDLEWARE (Optional)
        // $middleware->append(\App\Http\Middleware\CheckActiveUser::class);

        // ✅ RATE LIMITING FOR AUTH ROUTES
        $middleware->throttleApi();
        
        // ✅ WEB MIDDLEWARE GROUP CUSTOMIZATION
        $middleware->web(append: [
            // Add any global web middleware here
        ]);

        // ✅ GUEST MIDDLEWARE REDIRECT
        $middleware->redirectGuestsTo('/login');
        
        // ✅ AUTHENTICATED USER REDIRECT
        $middleware->redirectUsersTo(function () {
            $user = auth()->user();
            if (!$user) return '/login';
            
            return match($user->role_name) {
                'Owner' => '/dashboard-owner',
                'Gudang' => '/dashboard-gudang', 
                'Kasir' => '/dashboard-kasir',
                default => '/dashboard'
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ✅ CUSTOM EXCEPTION HANDLING FOR AUTH
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated. Please login.',
                    'redirect' => route('login')
                ], 401);
            }
            
            return redirect()->route('login')->with([
                'alert_type' => 'warning',
                'alert_title' => 'Session Expired',
                'alert_message' => 'Please login to continue.'
            ]);
        });

        // ✅ RATE LIMIT EXCEPTION HANDLING
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many attempts. Please slow down.',
                    'retry_after' => $e->getHeaders()['Retry-After'] ?? 60
                ], 429);
            }
            
            return back()->withErrors([
                'email' => 'Too many login attempts. Please try again in ' . ($e->getHeaders()['Retry-After'] ?? 60) . ' seconds.'
            ]);
        });
    })
    ->create();