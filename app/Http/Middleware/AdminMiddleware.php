<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Cek autentikasi terlebih dahulu
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
        
        // Debug dengan property yang benar
        Log::info('User checking in AdminMiddleware:', [
            'id_user' => auth()->user()->id_user ?? 'none', // GUNAKAN id_user, bukan id
            'role' => auth()->user()->role ?? 'none'
        ]);
        
        // Periksa role dengan cara yang aman
        if (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin') {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
