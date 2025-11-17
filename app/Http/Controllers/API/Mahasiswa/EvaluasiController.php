<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluasiController extends Controller
{
    /**
     * Get all evaluations for the logged-in student
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Get current logged-in user
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();
                
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }
            
            // Base query for evaluations from t_evaluasi table with correct relationships
            $query = DB::table('t_evaluasi')
                ->join('m_magang', 't_evaluasi.id_magang', '=', 'm_magang.id_magang')
                ->join('m_dosen', 'm_magang.id_dosen', '=', 'm_dosen.id_dosen')
                ->join('m_user as dosen_user', 'm_dosen.user_id', '=', 'dosen_user.id_user')
                ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan') // Fixed: direct join to lowongan
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->select(
                    't_evaluasi.id_evaluasi',
                    't_evaluasi.id_magang',
                    't_evaluasi.nilai_dosen',
                    't_evaluasi.nilai_perusahaan', 
                    't_evaluasi.nilai_akhir',
                    't_evaluasi.grade',
                    't_evaluasi.catatan_dosen',
                    't_evaluasi.file_penilaian_perusahaan',
                    't_evaluasi.created_at as tanggal',
                    'm_dosen.id_dosen',
                    'dosen_user.name as nama_dosen',
                    'm_perusahaan.perusahaan_id',
                    'm_perusahaan.nama_perusahaan',
                    'm_lowongan.judul_lowongan',
                    'm_magang.id_mahasiswa' // Tambahkan id_mahasiswa
                )
                ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->whereNotNull('t_evaluasi.nilai_dosen') // Only show completed evaluations
                ->whereNotNull('t_evaluasi.nilai_perusahaan');
            
            // Apply filters if provided
            if ($request->has('dosen') && $request->dosen) {
                $query->where('m_dosen.id_dosen', $request->dosen);
            }
            
            if ($request->has('perusahaan') && $request->perusahaan) {
                $query->where('m_perusahaan.perusahaan_id', $request->perusahaan);
            }
            
            // Get evaluations
            $evaluasi = $query->orderBy('t_evaluasi.created_at', 'desc')->get();
            
            // Format the data
            $formattedData = $evaluasi->map(function($item) {
                // Get first character of each word for avatar text
                $nameParts = explode(' ', $item->nama_dosen);
                $avatarText = '';
                foreach ($nameParts as $part) {
                    if (!empty($part)) {
                        $avatarText .= strtoupper(substr($part, 0, 1));
                    }
                    // Limit to 2 characters
                    if (strlen($avatarText) >= 2) break;
                }
                
                // If avatarText is still empty, use first 2 characters of name
                if (empty($avatarText)) {
                    $avatarText = strtoupper(substr($item->nama_dosen, 0, 2));
                }
                
                return [
                    'id' => $item->id_evaluasi,
                    'id_mahasiswa' => $item->id_mahasiswa, // Tambahkan ini
                    'id_magang' => $item->id_magang, // Tambahkan ini
                    'score' => $item->nilai_akhir ?: 'Belum dinilai',
                    'grade' => $item->grade ?: '-',
                    'nilai_dosen' => $item->nilai_dosen,
                    'nilai_perusahaan' => $item->nilai_perusahaan,
                    'komentar' => $item->catatan_dosen ?: 'Tidak ada catatan',
                    'tanggal' => $item->tanggal,
                    'judul_lowongan' => $item->judul_lowongan,
                    'dosen' => [
                        'id' => $item->id_dosen,
                        'nama' => $item->nama_dosen,
                        'avatar_text' => $avatarText
                    ],
                    'perusahaan' => [
                        'id' => $item->perusahaan_id,
                        'nama' => $item->nama_perusahaan
                    ]
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching evaluasi: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get filter options for dosen and perusahaan
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilterOptions()
    {
        try {
            // Get current logged-in user
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
            
            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();
                
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }
            
            // Get dosen options - filter by ones who have evaluated the student
            $dosenOptions = DB::table('t_evaluasi')
                ->join('m_magang', 't_evaluasi.id_magang', '=', 'm_magang.id_magang')
                ->join('m_dosen', 'm_magang.id_dosen', '=', 'm_dosen.id_dosen')
                ->join('m_user', 'm_dosen.user_id', '=', 'm_user.id_user')
                ->select('m_dosen.id_dosen', 'm_user.name as nama_dosen')
                ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->whereNotNull('t_evaluasi.nilai_dosen')
                ->distinct()
                ->get();
                
            // Get perusahaan options - filter by magang where student has evaluations
            $perusahaanOptions = DB::table('t_evaluasi')
                ->join('m_magang', 't_evaluasi.id_magang', '=', 'm_magang.id_magang')
                ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan') // Fixed: direct join to lowongan
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->select('m_perusahaan.perusahaan_id', 'm_perusahaan.nama_perusahaan')
                ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->whereNotNull('t_evaluasi.nilai_dosen')
                ->distinct()
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => [
                    'dosen' => $dosenOptions,
                    'perusahaan' => $perusahaanOptions
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching filter options: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat opsi filter: ' . $e->getMessage()
            ], 500);
        }
    }
}