<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\API\PeriodeController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeriodeController extends Controller
{
    /**
     * Mendapatkan semua data periode
     */
    public function index()
    {
        try {
            $periodes = Periode::select('m_periode.*')
                ->leftJoin('t_periode as tp', 'm_periode.periode_id', '=', 'tp.periode_id')
                ->selectRaw('CASE WHEN tp.periode_id IS NOT NULL THEN true ELSE false END as is_active')
                ->orderBy('m_periode.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $periodes
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching periode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data periode'
            ], 500);
        }
    }

    /**
     * Mendapatkan detail periode berdasarkan ID
     */
    public function show($id)
    {
        try {
            $periode = DB::table('m_periode')
                ->where('periode_id', $id)
                ->first();

            if (!$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $periode,
                'message' => 'Detail periode berhasil ditemukan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching periode details: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail periode',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan periode baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'waktu' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
        ]);

        try {
            $periode = new \App\Models\Periode();
            $periode->waktu = $request->waktu;
            $periode->tgl_mulai = $request->tgl_mulai;
            $periode->tgl_selesai = $request->tgl_selesai;
            $periode->save();

            return response()->json([
                'success' => true,
                'message' => 'Periode berhasil ditambahkan',
                'data' => $periode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan periode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'waktu' => 'required|string|max:255',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
        ]);

        try {
            $periode = \App\Models\Periode::find($id);

            if (!$periode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan'
                ], 404);
            }

            $periode->waktu = $request->waktu;
            $periode->tgl_mulai = $request->tgl_mulai;
            $periode->tgl_selesai = $request->tgl_selesai;
            $periode->save();

            return response()->json([
                'success' => true,
                'message' => 'Periode berhasil diperbarui',
                'data' => $periode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui periode: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Menghapus periode
     */
    public function destroy($id)
    {
        try {
            // Periksa apakah periode digunakan dalam relasi
            $usedInLowongan = DB::table('m_lowongan')
                ->where('periode_id', $id)
                ->exists();

            if ($usedInLowongan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak dapat dihapus karena masih digunakan dalam data lowongan'
                ], 400);
            }

            $deleted = DB::table('m_periode')
                ->where('periode_id', $id)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Periode tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Periode berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting periode: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus periode: ' . $e->getMessage()
            ], 500);
        }
    }

    public function setActive($id)
    {
        try {
            // Validate if the periode exists
            $periode = Periode::findOrFail($id);

            // Clear any existing active periode by truncating the t_periode table
            DB::table('t_periode')->truncate();

            // Insert the new active periode
            DB::table('t_periode')->insert([
                'periode_id' => $id,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Periode berhasil diaktifkan',
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting active periode: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengaktifkan periode'
            ], 500);
        }
    }
}
