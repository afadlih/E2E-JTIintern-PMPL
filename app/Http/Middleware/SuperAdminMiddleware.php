<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Tambahkan logging lengkap untuk debugging
        Log::info('SuperAdmin middleware check:', [
            'id_user' => Auth::user()->id_user,
            'role' => Auth::user()->role,
            'role_trimmed' => trim(strtolower(Auth::user()->role)), // Untuk mendeteksi whitespace/case issues
        ]);

        // Perbaiki pemeriksaan role menjadi lebih fleksibel
        $userRole = trim(strtolower(Auth::user()->role));
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized: Requires superadmin role'], 403);
    }
}