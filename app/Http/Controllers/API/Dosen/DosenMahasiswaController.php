<?php

namespace App\Http\Controllers\API\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DosenMahasiswaController extends Controller
{
    protected $dosen;
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $this->dosen = Dosen::where('user_id', $user->id_user)->first();
            
            if (!$this->dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }
            
            return $next($request);
        });
    }

    public function index()
    {
        // Get the authenticated user's dosen data
        $user = auth()->user();
        
        if ($user->role !== 'dosen') {
            return redirect()->route('unauthorized')->with('error', 'Anda tidak memiliki akses sebagai dosen');
        }
        
        $dosen = DB::table('m_dosen')
            ->where('user_id', $user->id_user)
            ->first();
        
        if (!$dosen) {
            // Log this issue for admin awareness
            Log::warning("User with ID {$user->id_user} has dosen role but no dosen record");
        }
        
        return view('pages.dosen.dosenMahasiswa', [
            'title' => 'Data Mahasiswa',
            'dosen_id' => $dosen ? $dosen->id_dosen : null,
        ]);
    }

    public function getMahasiswaBimbingan($id_dosen, Request $request)
    {
        try {
            $search = $request->get('search');
            $status = $request->get('status');
            $perusahaan = $request->get('perusahaan');
            $periode = $request->get('periode'); // Add this line
            $perPage = 6; // Set to 6 items per page
            $page = $request->get('page', 1); // Get the requested page, default to 1

            $mahasiswaQuery = DB::table('m_magang as mg')
                ->join('m_mahasiswa as mhs', 'mg.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_kelas as k', 'mhs.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_lowongan as l', 'mg.id_lowongan', '=', 'l.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_periode as pr', 'l.periode_id', '=', 'pr.periode_id') // Add this line
                ->where('mg.id_dosen', $id_dosen)
                ->when($search, function ($query) use ($search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('u.name', 'LIKE', "%{$search}%")
                          ->orWhere('mhs.nim', 'LIKE', "%{$search}%")
                          ->orWhere('k.nama_kelas', 'LIKE', "%{$search}%")
                          ->orWhere('p.nama_perusahaan', 'LIKE', "%{$search}%");
                    });
                })
                ->when($status, function ($query) use ($status) {
                    return $query->where('mg.status', $status);
                })
                ->when($perusahaan, function ($query) use ($perusahaan) {
                    return $query->where('p.perusahaan_id', $perusahaan);
                })
                ->when($periode, function ($query) use ($periode) { // Add this block
                    return $query->where('pr.periode_id', $periode);
                })
                ->select(
                    'mhs.id_mahasiswa',
                    'u.name',
                    'mhs.nim', 
                    'k.nama_kelas',
                    'mg.status',
                    'p.nama_perusahaan',
                    'l.judul_lowongan',
                    'pr.waktu as periode', // Add this line
                    'mg.id_magang' // <-- Tambahkan ini!
                );

            // Get total count for pagination
            $total = $mahasiswaQuery->count();
            
            // Apply pagination
            $mahasiswa = $mahasiswaQuery
                ->skip(($page - 1) * $perPage)
                ->take($perPage)
                ->get();

            // Create pagination metadata
            $meta = [
                'current_page' => (int)$page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
                'from' => ($page - 1) * $perPage + 1,
                'to' => min($page * $perPage, $total)
            ];

            return response()->json([
                'success' => true,
                'data' => $mahasiswa,
                'meta' => $meta,
                'message' => 'Data mahasiswa bimbingan berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaBimbingan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add new method to get companies list
    public function getPerusahaanList()
    {
        try {
            $perusahaan = DB::table('m_perusahaan')
                ->select('perusahaan_id', 'nama_perusahaan')
                ->orderBy('nama_perusahaan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $perusahaan,
                'message' => 'Data perusahaan berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPerusahaanList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add new method to get periode list
    public function getPeriodeList()
    {
        try {
            $periode = DB::table('m_periode')
                ->select('periode_id', 'waktu')
                ->orderBy('waktu', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $periode,
                'message' => 'Data periode berhasil diambil'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getPeriodeList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update checkEvaluationStatus method
    public function checkEvaluationStatus($magangId)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // Check if this magang belongs to this dosen
            $magangBelongsToDosen = DB::table('m_magang')
                ->where('id_magang', $magangId)
                ->where('id_dosen', $dosen->id_dosen)
                ->exists();

            if (!$magangBelongsToDosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Magang tidak ditemukan atau bukan tanggung jawab Anda'
                ], 403);
            }

            // Check if evaluation exists for this magang
            $evaluation = DB::table('t_evaluasi')
                ->where('id_magang', $magangId)
                ->first();

            // Check if dosen needs to evaluate (nilai_dosen is NULL)
            $needsDosenEvaluation = false;
            $grade = null;
            
            if ($evaluation) {
                $needsDosenEvaluation = is_null($evaluation->nilai_dosen) || is_null($evaluation->catatan_dosen);
                
                // If evaluation is complete, return the grade
                if (!is_null($evaluation->nilai_dosen) && !is_null($evaluation->nilai_perusahaan)) {
                    $grade = $evaluation->grade;
                }
            }

            return response()->json([
                'success' => true,
                'has_evaluation' => $evaluation ? true : false,
                'needs_dosen_evaluation' => $needsDosenEvaluation,
                'grade' => $grade,
                'magang_id' => $magangId
            ]);

        } catch (\Exception $e) {
            Log::error('Error checking evaluation status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status evaluasi'
            ], 500);
        }
    }

    // ✅ ADD: Method untuk mendapatkan info mahasiswa
    public function getMahasiswaInfo($id_mahasiswa)
    {
        try {
            Log::info("Attempting to get info for mahasiswa ID: {$id_mahasiswa}");

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $dosen = Dosen::where('user_id', $user->id_user)->first();
            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // ✅ PERBAIKI: Verify mahasiswa belongs to this dosen dengan join yang lebih aman
            $mahasiswa = DB::table('m_magang as mg')
                ->join('m_mahasiswa as mhs', 'mg.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_kelas as k', 'mhs.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_lowongan as l', 'mg.id_lowongan', '=', 'l.id_lowongan')
                ->leftJoin('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('mg.id_dosen', $dosen->id_dosen)
                ->where('mhs.id_mahasiswa', $id_mahasiswa)
                ->select(
                    'mhs.id_mahasiswa',
                    'u.name',
                    'mhs.nim',
                    'k.nama_kelas',
                    'mg.status',
                    DB::raw('COALESCE(p.nama_perusahaan, "Belum ada perusahaan") as nama_perusahaan'),
                    DB::raw('COALESCE(l.judul_lowongan, "Belum ada posisi") as judul_lowongan')
                )
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mahasiswa tidak ditemukan atau bukan bimbingan Anda'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $mahasiswa
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaInfo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat informasi mahasiswa: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ADD: Method untuk mendapatkan logbook mahasiswa
    public function getMahasiswaLogbook($id_mahasiswa, Request $request)
    {
        try {
            $id_magang = $request->get('id_magang');
            
            Log::info("Getting logbook - mahasiswa_id: {$id_mahasiswa}, magang_id: {$id_magang}");

            if (!$id_magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Magang diperlukan'
                ], 400);
            }

            // Verify magang exists and belongs to the correct dosen and mahasiswa
            $magang = DB::table('m_magang')
                ->where('id_magang', $id_magang)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->where('id_dosen', $this->dosen->id_dosen)
                ->first();

            if (!$magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data magang tidak ditemukan atau tidak memiliki akses'
                ], 403);
            }

            // PERBAIKAN: Gunakan query yang sederhana dan spesifik
            $logbook = DB::table('t_log')
                ->where('id_magang', '=', $id_magang)  // Filter hanya berdasarkan id_magang
                ->select(
                    'id_log as id',
                    'tanggal',
                    'log_aktivitas as deskripsi',
                    'foto',
                    'created_at',
                    'id_magang'  // Tambahkan ini untuk debug
                )
                ->orderBy('tanggal', 'desc')
                ->get();

            // Debug log untuk membantu troubleshooting
            Log::info("Query result for magang_id {$id_magang}: found {$logbook->count()} logs");
            foreach ($logbook as $log) {
                Log::info("Log ID: {$log->id}, Magang ID: {$log->id_magang}");
            }

            if ($logbook->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Belum ada data logbook'
                ]);
            }

            // Process and group the logs
            $monthGroups = [];
            foreach ($logbook as $entry) {
                $date = \Carbon\Carbon::parse($entry->tanggal);
                $monthKey = $date->format('F Y');
                
                if (!isset($monthGroups[$monthKey])) {
                    $monthGroups[$monthKey] = [];
                }

                // Process photo if exists
                $photoPath = null;
                $hasFoto = false;
                if ($entry->foto && !empty($entry->foto)) {
                    $fullPath = storage_path('app/public/' . $entry->foto);
                    if (file_exists($fullPath)) {
                        $photoPath = asset('storage/' . $entry->foto);
                        $hasFoto = true;
                    }
                }

                $monthGroups[$monthKey][] = [
                    'id' => $entry->id,
                    'id_mahasiswa' => $id_mahasiswa,
                    'tanggal' => $entry->tanggal,
                    'tanggal_formatted' => $date->format('d M Y'),
                    'tanggal_hari' => $date->isoFormat('dddd'),
                    'deskripsi' => $entry->deskripsi ?? '',
                    'foto' => $photoPath,
                    'has_foto' => $hasFoto,
                    'time_ago' => $date->diffForHumans(),
                    'created_at' => $entry->created_at
                ];
            }

            // Convert to array format for frontend
            $groupedData = [];
            foreach ($monthGroups as $month => $entries) {
                $groupedData[] = [
                    'month' => $month,
                    'entries' => $entries
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $groupedData,
                'message' => 'Data logbook berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaLogbook: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data logbook: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMahasiswaEvaluasi($id_mahasiswa, Request $request)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            $magang_id = $request->get('magang_id');
            
            if (!$magang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Magang tidak ditemukan'
                ], 400);
            }

            // Verify magang belongs to this dosen and mahasiswa
            $magang = DB::table('m_magang')
                ->where('id_magang', $magang_id)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->where('id_dosen', $dosen->id_dosen)
                ->first();

            if (!$magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data magang tidak ditemukan atau bukan tanggung jawab Anda'
                ], 403);
            }

            // Check if evaluation already exists
            $existingEvaluasi = DB::table('t_evaluasi')
                ->where('id_magang', $magang_id)
                ->first();

            $evaluasiData = [
                'id_mahasiswa' => $id_mahasiswa,
                'id_magang' => $magang_id,
                'nilai_akhir' => $existingEvaluasi->nilai_dosen ?? '',
                'catatan_evaluasi' => $existingEvaluasi->catatan_dosen ?? '',
                'nilai_perusahaan' => $existingEvaluasi->nilai_perusahaan ?? null,
                'file_penilaian_perusahaan' => $existingEvaluasi->file_penilaian_perusahaan ?? null,
                'is_existing' => $existingEvaluasi ? true : false,
                'needs_dosen_input' => $existingEvaluasi ? 
                    (is_null($existingEvaluasi->nilai_dosen) || is_null($existingEvaluasi->catatan_dosen)) : 
                    false
            ];

            return response()->json([
                'success' => true,
                'data' => $evaluasiData,
                'message' => 'Data evaluasi berhasil diambil'
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getMahasiswaEvaluasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeMahasiswaEvaluasi($id_mahasiswa, Request $request)
    {
        try {
            $user = Auth::user();
            $dosen = Dosen::where('user_id', $user->id_user)->first();

            if (!$dosen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data dosen tidak ditemukan'
                ], 404);
            }

            // Validate request
            $request->validate([
                'nilai_dosen' => 'required|numeric|min:0|max:100',
                'catatan_dosen' => 'required|string'
            ]);

            $magang_id = $request->get('magang_id');
            
            if (!$magang_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Magang tidak ditemukan'
                ], 400);
            }

            // Verify magang belongs to this dosen and mahasiswa
            $magang = DB::table('m_magang')
                ->where('id_magang', $magang_id)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->where('id_dosen', $dosen->id_dosen)
                ->first();

            if (!$magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data magang tidak ditemukan atau bukan tanggung jawab Anda'
                ], 403);
            }

            DB::beginTransaction();

            // Check if evaluation already exists
            $existingEvaluasi = DB::table('t_evaluasi')
                ->where('id_magang', $magang_id)
                ->first();

            // Calculate final grade: nilai_akhir = ½ (nilai_perusahaan + nilai_dosen)
            $nilai_dosen = (float)$request->nilai_dosen;
            $nilai_perusahaan = $existingEvaluasi ? (float)$existingEvaluasi->nilai_perusahaan : 0;
            $nilai_akhir = ($nilai_perusahaan + $nilai_dosen) / 2;

            // Determine letter grade based on nilai_akhir
            $grade = $this->calculateGrade($nilai_akhir);

            if ($existingEvaluasi) {
                // Update existing evaluation
                DB::table('t_evaluasi')
                    ->where('id_evaluasi', $existingEvaluasi->id_evaluasi)
                    ->update([
                        'nilai_dosen' => $nilai_dosen,
                        'catatan_dosen' => $request->catatan_dosen,
                        'nilai_akhir' => $nilai_akhir,
                        'grade' => $grade,
                        'updated_at' => now()
                    ]);
                
                $message = 'Evaluasi berhasil diperbarui';
            } else {
                // Create new evaluation if it doesn't exist yet
                DB::table('t_evaluasi')->insert([
                    'id_magang' => $magang_id,
                    'nilai_dosen' => $nilai_dosen,
                    'catatan_dosen' => $request->catatan_evaluasi,
                    'nilai_akhir' => $nilai_akhir,
                    'grade' => $grade,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                
                $message = 'Evaluasi berhasil disimpan';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'nilai_dosen' => $nilai_dosen,
                    'nilai_perusahaan' => $nilai_perusahaan,
                    'nilai_akhir' => $nilai_akhir,
                    'grade' => $grade
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in storeMahasiswaEvaluasi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan evaluasi: ' . $e->getMessage()
            ], 500);
        }
    }


    private function calculateGrade($nilai_akhir)
    {
        if ($nilai_akhir >= 81 && $nilai_akhir <= 100) {
            return 'A'; // Sangat Baik
        } elseif ($nilai_akhir >= 74 && $nilai_akhir < 81) {
            return 'B+'; // Lebih dari Baik
        } elseif ($nilai_akhir >= 66 && $nilai_akhir < 74) {
            return 'B'; // Baik
        } elseif ($nilai_akhir >= 61 && $nilai_akhir < 66) {
            return 'C+'; // Lebih dari Cukup
        } elseif ($nilai_akhir >= 51 && $nilai_akhir < 61) {
            return 'C'; // Cukup
        } elseif ($nilai_akhir >= 40 && $nilai_akhir <= 50) {
            return 'D'; // Kurang
        } else {
            return 'E'; // Default for scores below 40
        }
    }
}