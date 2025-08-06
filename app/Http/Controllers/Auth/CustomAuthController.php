<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class CustomAuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.custom-login');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Rate limiting
        $key = Str::transliterate(Str::lower($request->email) . '|' . $request->ip());
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ])->onlyInput('email');
        }

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key);
            
            Log::warning('Failed login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        // ✅ CHECK IF USER IS ACTIVE
        if (!$user->is_active) {
            Log::warning('Inactive user login attempt', [
                'email' => $request->email,
                'user_id' => $user->id,
                'ip' => $request->ip()
            ]);

            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
            ])->onlyInput('email')->with([
                'alert_type' => 'warning',
                'alert_title' => 'Akun Dinonaktifkan',
                'alert_message' => 'Akun Anda tidak aktif. Hubungi administrator untuk informasi lebih lanjut.'
            ]);
        }

        // Clear rate limiting on successful login
        RateLimiter::clear($key);

        // Login user
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Log successful login
        Log::info('User logged in successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role_name,
            'ip' => $request->ip()
        ]);

        // Update last login timestamp if you have the column
        $user->update(['last_login_at' => now()]);

        // Return success response for AJAX or redirect for regular form
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil!',
                'redirect' => $this->getRedirectUrl($user)
            ]);
        }

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil!'
            ]);
        }

        return redirect()->route('login')->with([
            'alert_type' => 'success',
            'alert_title' => 'Logout Berhasil',
            'alert_message' => 'Anda telah keluar dari sistem.'
        ]);
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl(User $user): string
    {
        return match($user->role_name) {
            'Owner' => route('owner.dashboard'),
            'Gudang' => route('dashboard.gudang'), 
            'Service Advisor' => route('dashboard.kasir'), // Updated untuk Service Advisor
            'Kasir' => route('dashboard.kasir'), // Backward compatibility
            default => route('dashboard')
        };
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole(User $user)
    {
        
        $redirectUrl = $this->getRedirectUrl($user);
        
        // Display role name yang user-friendly
        $displayRole = $user->role_name === 'Service Advisor' ? 'Service Advisor' : $user->role_name;
        
        return redirect()->intended($redirectUrl)->with([
            'alert_type' => 'success',
            'alert_title' => 'Selamat Datang!',
            'alert_message' => "Halo {$user->name} ({$displayRole}), selamat datang kembali!"
        ]);
    }

    /**
     * Check current user status (for AJAX polling)
     */
    public function checkStatus()
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false,
                'message' => 'Not authenticated'
            ]);
        }

        $user = Auth::user();
        
        // Check if user is still active
        if (!$user->is_active) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            
            return response()->json([
                'authenticated' => false,
                'message' => 'Account deactivated',
                'redirect' => route('login')
            ]);
        }

        return response()->json([
            'authenticated' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role_name,
                'is_active' => $user->is_active
            ]
        ]);
    }

    /**
     * Get user info for dashboard
     */
    public function getUserInfo()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role_name,
                'initials' => $user->initials,
                'is_active' => $user->is_active,
                'last_login' => $user->last_login_at?->format('d M Y H:i') ?? 'Belum pernah login'
            ]
        ]);
    }
}