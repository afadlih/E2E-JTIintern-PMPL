<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userData = DB::table('m_user')
            ->where('id_user', $user->id_user)
            ->first();
        
        // Fix: Add proper join untuk mendapatkan nama_kota
        $mahasiswa = DB::table('m_mahasiswa')
            ->leftJoin('m_wilayah', 'm_mahasiswa.wilayah_id', '=', 'm_wilayah.wilayah_id')
            ->where('m_mahasiswa.id_user', $user->id_user)
            ->select(
                'm_mahasiswa.*',
                'm_wilayah.nama_kota'
            )
            ->first();
        
        $activePeriod = DB::table('m_periode')
            ->where('is_active', 1)
            ->first();
        
        // Check profile completion
        $profileCompletion = $this->checkProfileCompletion($user->id_user);
        
        // Ambil data magang aktif jika ada
        $magangAktif = null;
        if ($mahasiswa) {
            // ✅ UPDATE: Query tanpa hardcoded date
            $magangAktif = DB::table('m_magang')
                ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->leftJoin('m_wilayah as wilayah_perusahaan', 'm_perusahaan.wilayah_id', '=', 'wilayah_perusahaan.wilayah_id')
                ->leftJoin('m_dosen', 'm_magang.id_dosen', '=', 'm_dosen.id_dosen')
                ->leftJoin('m_user as user_dosen', 'm_dosen.user_id', '=', 'user_dosen.id_user')
                ->select(
                    'm_magang.*', 
                    'm_lowongan.judul_lowongan',
                    'm_perusahaan.nama_perusahaan',
                    'm_perusahaan.logo as logo_perusahaan',
                    'wilayah_perusahaan.nama_kota',
                    'user_dosen.name as nama_pembimbing',
                    'm_dosen.nip as nip_pembimbing'
                    // ✅ GUNAKAN: Kolom tgl_mulai dan tgl_selesai dari database langsung
                    // Tidak perlu hardcode lagi
                )
                ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('m_magang.status', 'aktif')
                ->first();

            Log::info('Magang aktif query result:', [
                'data' => $magangAktif,
                'tgl_mulai' => $magangAktif->tgl_mulai ?? 'NULL',
                'tgl_selesai' => $magangAktif->tgl_selesai ?? 'NULL'
            ]);
        }
        
        // ✅ PERBAIKAN: Hitung progress menggunakan kolom database yang sebenarnya
        $magangInfo = null;
        if ($magangAktif) {
            if ($magangAktif->tgl_mulai && $magangAktif->tgl_selesai) {
                $tanggalMulai = \Carbon\Carbon::parse($magangAktif->tgl_mulai);
                $tanggalSelesai = \Carbon\Carbon::parse($magangAktif->tgl_selesai);
                $hariIni = \Carbon\Carbon::now();
                
                $totalDurasi = $tanggalMulai->diffInDays($tanggalSelesai);
                
                // ✅ PERBAIKI: Perhitungan yang benar berdasarkan status tanggal
                if ($hariIni->isBefore($tanggalMulai)) {
                    // Belum mulai
                    $lewat = 0;
                    $sisaHari = $tanggalMulai->diffInDays($hariIni); // Hari menuju mulai
                    $progress = 0;
                    $statusText = 'Akan dimulai dalam ' . $sisaHari . ' hari';
                } elseif ($hariIni->isAfter($tanggalSelesai)) {
                    // Sudah selesai
                    $lewat = $totalDurasi;
                    $sisaHari = 0;
                    $progress = 100;
                    $statusText = 'Magang telah selesai';
                } else {
                    // Sedang berlangsung
                    $lewat = $tanggalMulai->diffInDays($hariIni);
                    $sisaHari = $hariIni->diffInDays($tanggalSelesai);
                    $progress = $totalDurasi > 0 ? min(100, max(0, ($lewat / $totalDurasi) * 100)) : 0;
                    $statusText = 'Sedang berlangsung';
                }
                
                $magangInfo = [
                    'data' => $magangAktif,
                    'totalDurasi' => $totalDurasi,
                    'lewat' => $lewat,
                    'sisaHari' => $sisaHari,
                    'progress' => round($progress),
                    'status_progress' => $hariIni->isBefore($tanggalMulai) ? 'belum_mulai' : 
                                       ($hariIni->isAfter($tanggalSelesai) ? 'selesai' : 'berlangsung'),
                    'status_text' => $statusText,
                    'tgl_mulai_formatted' => $tanggalMulai->format('d M Y'),
                    'tgl_selesai_formatted' => $tanggalSelesai->format('d M Y'),
                    'tgl_mulai_full' => $tanggalMulai->format('d F Y'),
                    'tgl_selesai_full' => $tanggalSelesai->format('d F Y')
                ];
            } else {
                // ✅ FALLBACK: Jika tanggal belum diset
                $magangInfo = [
                    'data' => $magangAktif,
                    'totalDurasi' => 0,
                    'lewat' => 0,
                    'sisaHari' => 0,
                    'progress' => 0,
                    'status_progress' => 'belum_terjadwal',
                    'status_text' => 'Jadwal magang belum ditentukan',
                    'tgl_mulai_formatted' => 'Belum ditentukan',
                    'tgl_selesai_formatted' => 'Belum ditentukan',
                    'message' => 'Tanggal magang belum ditentukan oleh admin'
                ];
            }

            Log::info('Magang info calculated:', [
                'magangInfo' => $magangInfo,
                'calculation_source' => $magangAktif->tgl_mulai ? 'database_dates' : 'fallback'
            ]);
        }
        
        return view('pages.mahasiswa.dashboard', [
            'userData' => $userData,
            'mahasiswa' => $mahasiswa,
            'activePeriod' => $activePeriod,
            'magangInfo' => $magangInfo,
            'profileCompletion' => $profileCompletion
        ]);
    }
    
    /**
     * Check if student profile is complete
     */
    private function checkProfileCompletion($userId)
    {
        Log::info('=== DEBUGGING PROFILE COMPLETION ===');
        Log::info('Checking profile completion for user_id: ' . $userId);
        
        $completion = [
            'is_complete' => true,
            'missing' => [],
            'details' => [],
            'completion_percentage' => 100
        ];
        
        $totalChecks = 3;
        $completedChecks = 0;
        
        try {
            // Get mahasiswa data
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $userId)
                ->first();
                
            if (!$mahasiswa) {
                Log::error('❌ Mahasiswa not found for user_id: ' . $userId);
                return [
                    'is_complete' => false,
                    'missing' => ['skill', 'minat', 'wilayah'],
                    'details' => [
                        'skill' => [
                            'label' => 'Keahlian/Skill',
                            'description' => 'Tambahkan keahlian yang Anda kuasai untuk mendapatkan rekomendasi lowongan yang sesuai',
                            'icon' => 'bi-tools'
                        ],
                        'minat' => [
                            'label' => 'Minat Bidang Kerja',
                            'description' => 'Pilih bidang kerja yang Anda minati',
                            'icon' => 'bi-heart'
                        ],
                        'wilayah' => [
                            'label' => 'Preferensi Lokasi',
                            'description' => 'Tentukan lokasi yang Anda inginkan untuk magang',
                            'icon' => 'bi-geo-alt'
                        ]
                    ],
                    'completion_percentage' => 0,
                    'completed_items' => 0,
                    'total_items' => 3
                ];
            }
            
            Log::info('✅ Mahasiswa found:', ['mahasiswa_id' => $mahasiswa->id_mahasiswa, 'wilayah_id' => $mahasiswa->wilayah_id]);
            
            // 1. Check skills dari table t_skill_mahasiswa
            $skillCount = DB::table('t_skill_mahasiswa')
                ->where('user_id', $userId)
                ->count();
                
            Log::info('📊 Skills count: ' . $skillCount);
            
            if ($skillCount > 0) {
                $completedChecks++;
                Log::info('✅ Skills check PASSED');
            } else {
                $completion['missing'][] = 'skill';
                $completion['details']['skill'] = [
                    'label' => 'Keahlian/Skill',
                    'description' => 'Tambahkan keahlian yang Anda kuasai untuk mendapatkan rekomendasi lowongan yang sesuai',
                    'icon' => 'bi-tools'
                ];
                Log::info('❌ Skills check FAILED - No skills found');
            }
            
            // 2. Check interests dari table t_minat_mahasiswa 
            $interestCount = DB::table('t_minat_mahasiswa')
                ->where('mahasiswa_id', $mahasiswa->id_mahasiswa)
                ->count();
                
            Log::info('📊 Interests count: ' . $interestCount);
            
            if ($interestCount > 0) {
                $completedChecks++;
                Log::info('✅ Interests check PASSED');
            } else {
                $completion['missing'][] = 'minat';
                $completion['details']['minat'] = [
                    'label' => 'Minat Bidang Kerja',
                    'description' => 'Pilih bidang kerja yang Anda minati',
                    'icon' => 'bi-heart'
                ];
                Log::info('❌ Interests check FAILED - No interests found');
            }
            
            // 3. Check location preference
            Log::info('📍 Wilayah ID: ' . ($mahasiswa->wilayah_id ?? 'NULL'));
            
            if ($mahasiswa->wilayah_id) {
                $completedChecks++;
                Log::info('✅ Location check PASSED');
            } else {
                $completion['missing'][] = 'wilayah';
                $completion['details']['wilayah'] = [
                    'label' => 'Preferensi Lokasi',
                    'description' => 'Tentukan lokasi yang Anda inginkan untuk magang',
                    'icon' => 'bi-geo-alt'
                ];
                Log::info('❌ Location check FAILED - No location preference');
            }
            
            // UNTUK TESTING: FORCE INCOMPLETE JIKA USER ID = 3
            if ($userId == 3) {
                Log::info('🧪 FORCING INCOMPLETE FOR USER ID 3 (TESTING)');
                return [
                    'is_complete' => false,
                    'missing' => ['minat', 'wilayah'],
                    'details' => [
                        'minat' => [
                            'label' => 'Minat Bidang Kerja',
                            'description' => 'Pilih bidang kerja yang Anda minati untuk mendapatkan rekomendasi yang sesuai',
                            'icon' => 'bi-heart'
                        ],
                        'wilayah' => [
                            'label' => 'Preferensi Lokasi',
                            'description' => 'Tentukan lokasi yang Anda inginkan untuk magang',
                            'icon' => 'bi-geo-alt'
                        ]
                    ],
                    'completion_percentage' => 33, // 1/3 = 33% (hanya skill yang ada)
                    'completed_items' => 1,
                    'total_items' => 3
                ];
            }
            
            // Calculate completion
            $completion['completion_percentage'] = round(($completedChecks / $totalChecks) * 100);
            $completion['is_complete'] = $completedChecks === $totalChecks;
            $completion['completed_items'] = $completedChecks;
            $completion['total_items'] = $totalChecks;
            
            Log::info('=== FINAL RESULT ===');
            Log::info('Completed checks: ' . $completedChecks . '/' . $totalChecks);
            Log::info('Is complete: ' . ($completion['is_complete'] ? 'YES' : 'NO'));
            Log::info('Missing items: [' . implode(', ', $completion['missing']) . ']');
            Log::info('Completion percentage: ' . $completion['completion_percentage'] . '%');
            Log::info('========================');
            
            return $completion;
            
        } catch (\Exception $e) {
            Log::error('💥 Error in checkProfileCompletion: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // RETURN INCOMPLETE ON ERROR
            return [
                'is_complete' => false,
                'missing' => ['skill', 'minat', 'wilayah'],
                'details' => [
                    'skill' => [
                        'label' => 'Keahlian/Skill',
                        'description' => 'Tambahkan keahlian yang Anda kuasai',
                        'icon' => 'bi-tools'
                    ],
                    'minat' => [
                        'label' => 'Minat Bidang Kerja', 
                        'description' => 'Pilih bidang kerja yang Anda minati',
                        'icon' => 'bi-heart'
                    ],
                    'wilayah' => [
                        'label' => 'Preferensi Lokasi',
                        'description' => 'Tentukan lokasi yang Anda inginkan untuk magang',
                        'icon' => 'bi-geo-alt'
                    ]
                ],
                'completion_percentage' => 0,
                'completed_items' => 0,
                'total_items' => 3,
                'error' => true
            ];
        }
    }
    
    /**
     * API endpoint to get profile completion status
     */
    public function getProfileCompletion()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            $profileCompletion = $this->checkProfileCompletion($user->id_user);
            
            return response()->json([
                'success' => true,
                'data' => $profileCompletion
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getProfileCompletion API: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to check profile completion',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
