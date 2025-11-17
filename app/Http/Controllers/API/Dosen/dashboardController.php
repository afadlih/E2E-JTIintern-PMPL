<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Magang;
use App\Models\TPeriode;

class DashboardController extends Controller
{
    public function getStats($id_dosen)
    {
        try {
            // Get active period from t_periode
            $activePeriode = TPeriode::with('periode')->first();

            // Get total mahasiswa bimbingan
            $totalMahasiswa = Magang::where('id_dosen', $id_dosen)->count();

            // Get mahasiswa yang sedang magang
            $aktiveMagang = Magang::where('id_dosen', $id_dosen)
                ->where('status', 'aktif')
                ->count();

            // Get mahasiswa yang perlu evaluasi (status selesai tapi belum ada evaluasi)
            $needEvaluasi = DB::table('m_magang as mg')
                ->leftJoin('t_evaluasi as ev', 'mg.id_magang', '=', 'ev.id_magang')
                ->where('mg.id_dosen', $id_dosen)
                ->where('mg.status', 'selesai')  // Changed from 'aktif' to 'selesai'
                ->whereNull('ev.id_evaluasi')    // Check if no evaluation exists
                ->count();

            // Get periode waktu
            $periodeWaktu = null;
            if ($activePeriode && $activePeriode->periode) {
                $periodeWaktu = $activePeriode->periode->waktu;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'periode' => $periodeWaktu,
                    'total_mahasiswa' => $totalMahasiswa,
                    'aktif_magang' => $aktiveMagang,
                    'perlu_evaluasi' => $needEvaluasi
                ],
                'message' => 'Data statistik berhasil diambil'
            ]);
        } catch (\Exception $e) {
            \Log::error('Dashboard Stats Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMahasiswaBimbingan($id_dosen)
    {
        try {
            $mahasiswa = DB::table('m_magang as mg')
                ->join('m_mahasiswa as mhs', 'mg.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_lowongan as l', 'mg.id_lowongan', '=', 'l.id_lowongan')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('mg.id_dosen', $id_dosen)
                ->select(
                    'mhs.id_mahasiswa',
                    'u.name',
                    'mhs.nim',
                    'p.nama_perusahaan',
                    'mg.status',
                    'l.judul_lowongan'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => $mahasiswa,
                'message' => 'Data mahasiswa bimbingan berhasil diambil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
