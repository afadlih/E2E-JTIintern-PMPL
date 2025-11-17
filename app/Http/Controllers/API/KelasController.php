<?php
// filepath: d:\laragon\www\JTIintern\app\Http\Controllers\API\KelasController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
    {
        try {
            $kelas = DB::table('m_kelas AS k')
                ->leftJoin('m_prodi AS p', 'k.kode_prodi', '=', 'p.kode_prodi')
                ->select('k.id_kelas', 'k.nama_kelas', 'k.kode_prodi', 'k.tahun_masuk', 'p.nama_prodi')
                ->orderBy('k.tahun_masuk', 'desc')
                ->orderBy('k.nama_kelas', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $kelas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_kelas' => 'required|string|max:255',
                'kode_prodi' => 'required|string|max:50',
                'tahun_masuk' => 'required|integer|min:2000|max:' . (date('Y') + 1)
            ]);

            DB::table('m_kelas')->insert([
                'nama_kelas' => $request->nama_kelas,
                'kode_prodi' => $request->kode_prodi,
                'tahun_masuk' => $request->tahun_masuk
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kelas = DB::table('m_kelas')
                ->where('id_kelas', $id)
                ->first();

            if (!$kelas) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $kelas,
                'message' => 'Data kelas berhasil ditemukan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_kelas' => 'required|string|max:255',
                'kode_prodi' => 'required|string|max:50',
                'tahun_masuk' => 'required|integer|min:2000|max:' . (date('Y') + 1)
            ]);

            $affected = DB::table('m_kelas')
                ->where('id_kelas', $id)
                ->update([
                    'nama_kelas' => $request->nama_kelas,
                    'kode_prodi' => $request->kode_prodi,
                    'tahun_masuk' => $request->tahun_masuk
                ]);

            if (!$affected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('m_kelas')
                ->where('id_kelas', $id)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelas tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Kelas berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProdi()
    {
        try {
            $prodi = DB::table('m_prodi')
                ->select('kode_prodi', 'nama_prodi')
                ->orderBy('nama_prodi', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $prodi
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}