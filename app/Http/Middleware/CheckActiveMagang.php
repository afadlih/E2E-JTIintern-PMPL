<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckActiveMagang
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
        // Ambil data user yang sedang login
        $user = auth()->user();

        if (!$user) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            return redirect('login');
        }

        // Ambil data mahasiswa
        $mahasiswa = DB::table('m_mahasiswa')
            ->where('id_user', $user->id_user)
            ->first();

        if (!$mahasiswa) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }
            
            return redirect()->route('mahasiswa.dashboard')->with('error', 'Data mahasiswa tidak ditemukan');
        }

        // Ambil data magang aktif
        $activeMagang = DB::table('m_magang')
            ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('status', 'aktif')
            ->first();

        if (!$activeMagang) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum memiliki magang aktif. Silakan daftar dan menunggu konfirmasi.'
                ], 403);
            }
            
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Anda belum memiliki magang aktif. Silakan daftar dan menunggu konfirmasi.');
        }

        // User memiliki magang aktif, lanjutkan request
        return $next($request);
    }
}