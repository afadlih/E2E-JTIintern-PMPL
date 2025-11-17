<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles  Comma-separated roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        // BAGIAN PENTING: Selalu izinkan superadmin mengakses semua route
        $userRole = trim(strtolower(Auth::user()->role));
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        // Parse comma-separated roles untuk pengguna non-superadmin
        $allowedRoles = explode(',', $roles);
        $normalizedAllowedRoles = array_map(function ($role) {
            return trim(strtolower($role));
        }, $allowedRoles);

        Log::info('Role middleware check:', [
            'id_user' => Auth::user()->id_user,
            'user_role' => Auth::user()->role,
            'user_role_normalized' => $userRole,
            'required_roles' => $normalizedAllowedRoles
        ]);

        // Periksa apakah role user ada dalam daftar yang diizinkan
        if (in_array($userRole, $normalizedAllowedRoles)) {
            return $next($request);
        }

        // Return JSON response for API calls, fallback to redirect for web routes
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized role access'
            ], 403);
        }

        return redirect()->route('unauthorized');
    }
}
