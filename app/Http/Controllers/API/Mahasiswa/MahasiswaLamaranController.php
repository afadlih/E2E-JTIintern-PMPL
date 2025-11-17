<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\NotificationService; // ✅ TAMBAHAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MahasiswaLamaranController extends Controller
{
    protected $notificationService; // ✅ TAMBAHAN

    public function __construct(NotificationService $notificationService) // ✅ TAMBAHAN
    {
        $this->notificationService = $notificationService;
    }

    // Method untuk get lamaran mahasiswa (pure API)
    public function getLamaranMahasiswa()
    {
        try {
            $user = Auth::user();
            
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

            // ✅ PERBAIKI: Get lamaran history dengan logo yang benar
            $lamaranHistory = DB::table('t_lamaran as l')
                ->join('m_lowongan as low', 'l.id_lowongan', '=', 'low.id_lowongan')
                ->join('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_wilayah as w', 'p.wilayah_id', '=', 'w.wilayah_id') // ✅ FIX: gunakan m_wilayah, bukan m_kota
                ->where('l.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->select([
                    'l.id_lamaran',
                    'l.tanggal_lamaran',
                    'l.auth as status',
                    'low.id_lowongan',
                    'low.judul_lowongan',
                    'low.deskripsi as deskripsi_lowongan',
                    'low.kapasitas',
                    'low.min_ipk',
                    'p.perusahaan_id',
                    'p.nama_perusahaan',
                    'p.logo', // ✅ PERBAIKI: gunakan kolom 'logo', bukan 'logo_perusahaan'
                    'p.alamat_perusahaan',
                    'p.email as perusahaan_email',
                    'p.website',
                    'w.nama_kota'
                ])
                ->orderBy('l.tanggal_lamaran', 'desc')
                ->get();

            // ✅ TRANSFORM data dengan logo URL yang benar
            $lamaranHistoryTransformed = $lamaranHistory->map(function($lamaran) {
                // Generate logo URL yang benar
                $logoUrl = null;
                if ($lamaran->logo && !empty($lamaran->logo)) {
                    if (strpos($lamaran->logo, 'http') === 0) {
                        $logoUrl = $lamaran->logo;
                    } else if (strpos($lamaran->logo, 'storage/') === 0) {
                        $logoUrl = asset($lamaran->logo);
                    } else {
                        $logoUrl = asset('storage/' . $lamaran->logo);
                    }
                }

                return (object) [
                    'id_lamaran' => $lamaran->id_lamaran,
                    'tanggal_lamaran' => $lamaran->tanggal_lamaran,
                    'status' => $lamaran->status,
                    'id_lowongan' => $lamaran->id_lowongan,
                    'judul_lowongan' => $lamaran->judul_lowongan,
                    'deskripsi_lowongan' => $lamaran->deskripsi_lowongan,
                    'kapasitas' => $lamaran->kapasitas,
                    'min_ipk' => $lamaran->min_ipk,
                    'perusahaan_id' => $lamaran->perusahaan_id,
                    'nama_perusahaan' => $lamaran->nama_perusahaan,
                    'logo_perusahaan' => $lamaran->logo, // ✅ Keep original path
                    'logo_url' => $logoUrl, // ✅ Add full URL
                    'alamat_perusahaan' => $lamaran->alamat_perusahaan,
                    'perusahaan_email' => $lamaran->perusahaan_email,
                    'website' => $lamaran->website,
                    'nama_kota' => $lamaran->nama_kota
                ];
            });

            // Calculate statistics
            $statistik = [
                'total' => $lamaranHistoryTransformed->count(),
                'menunggu' => $lamaranHistoryTransformed->where('status', 'menunggu')->count(),
                'diterima' => $lamaranHistoryTransformed->where('status', 'diterima')->count(),
                'ditolak' => $lamaranHistoryTransformed->where('status', 'ditolak')->count(),
            ];

            // ✅ LOG untuk debugging
            Log::info('Lamaran history loaded:', [
                'total' => $statistik['total'],
                'sample_logo' => $lamaranHistoryTransformed->first()->logo_url ?? 'No logo'
            ]);

            return response()->json([
                'success' => true,
                'lamaranHistory' => $lamaranHistoryTransformed->toArray(),
                'statistik' => $statistik
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting lamaran data: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ TAMBAHKAN: Method untuk reload data (untuk AJAX refresh)
    public function reloadLamaranData()
    {
        try {
            $user = Auth::user();
            
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

            // Check for active magang
            $activeMagang = DB::table('m_magang as m')
                ->join('m_lowongan as l', 'm.id_lowongan', '=', 'l.id_lowongan')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_wilayah as w', 'p.wilayah_id', '=', 'w.wilayah_id')
                ->leftJoin('m_dosen as d', 'm.id_dosen', '=', 'd.id_dosen')
                ->where('m.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('m.status', 'aktif')
                ->select([
                    'm.*',
                    'l.judul_lowongan',
                    'p.nama_perusahaan',
                    'p.logo', // ✅ PERBAIKI: gunakan 'logo'
                    'w.nama_kota',
                    'd.nama as nama_pembimbing',
                    'd.nip as nip_pembimbing'
                ])
                ->first();

            $magangInfo = null;
            if ($activeMagang) {
                // Calculate magang progress
                $startDate = new \DateTime($activeMagang->tanggal_mulai);
                $endDate = new \DateTime($activeMagang->tanggal_selesai);
                $currentDate = new \DateTime();
                
                $totalDays = $startDate->diff($endDate)->days;
                $passedDays = $startDate->diff($currentDate)->days;
                $remainingDays = max(0, $endDate->diff($currentDate)->days);
                
                if ($currentDate > $endDate) {
                    $passedDays = $totalDays;
                    $remainingDays = 0;
                }
                
                $progress = $totalDays > 0 ? min(100, round(($passedDays / $totalDays) * 100, 1)) : 0;

                // ✅ PERBAIKI: Generate logo URL untuk magang aktif
                $logoUrl = null;
                if ($activeMagang->logo && !empty($activeMagang->logo)) {
                    if (strpos($activeMagang->logo, 'http') === 0) {
                        $logoUrl = $activeMagang->logo;
                    } else if (strpos($activeMagang->logo, 'storage/') === 0) {
                        $logoUrl = asset($activeMagang->logo);
                    } else {
                        $logoUrl = asset('storage/' . $activeMagang->logo);
                    }
                }

                $magangInfo = [
                    'data' => (object) [
                        'id_magang' => $activeMagang->id_magang,
                        'judul_lowongan' => $activeMagang->judul_lowongan,
                        'nama_perusahaan' => $activeMagang->nama_perusahaan,
                        'nama_kota' => $activeMagang->nama_kota,
                        'logo_perusahaan' => $activeMagang->logo, // ✅ Keep original
                        'logo_url' => $logoUrl, // ✅ Add full URL
                        'nama_pembimbing' => $activeMagang->nama_pembimbing,
                        'nip_pembimbing' => $activeMagang->nip_pembimbing,
                        'tanggal_mulai' => $activeMagang->tanggal_mulai,
                        'tanggal_selesai' => $activeMagang->tanggal_selesai
                    ],
                    'progress' => $progress,
                    'lewat' => $passedDays,
                    'sisaHari' => $remainingDays
                ];
            }

            // Get lamaran history only if no active magang
            $lamaranHistoryData = [];
            $showLamaranHistory = !$activeMagang;

            if ($showLamaranHistory) {
                $lamaranHistory = $this->getLamaranMahasiswa();
                $lamaranResponse = $lamaranHistory->getData();
                
                if ($lamaranResponse->success) {
                    $lamaranHistoryData = $lamaranResponse->lamaranHistory;
                    $statistik = $lamaranResponse->statistik;
                }
            } else {
                // Default statistik untuk magang aktif
                $statistik = [
                    'total' => 0,
                    'menunggu' => 0,
                    'diterima' => 1, // karena ada magang aktif
                    'ditolak' => 0
                ];
            }

            return response()->json([
                'success' => true,
                'magangInfo' => $magangInfo,
                'lamaranHistory' => $lamaranHistoryData,
                'statistik' => $statistik,
                'showLamaranHistory' => $showLamaranHistory
            ]);

        } catch (\Exception $e) {
            Log::error('Error reloading lamaran data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat ulang data: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ TAMBAHAN BARU - Method untuk submit lamaran dengan notifikasi
    public function submitLamaran(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            
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

            // Validate request
            $request->validate([
                'id_lowongan' => 'required|exists:m_lowongan,id_lowongan',
                'id_dosen' => 'nullable|exists:m_dosen,id_dosen'
            ]);

            $id_lowongan = $request->id_lowongan;
            $id_dosen = $request->id_dosen;

            // Check if already applied for this lowongan
            $existingLamaran = DB::table('t_lamaran')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_lowongan', $id_lowongan)
                ->exists();

            if ($existingLamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melamar untuk lowongan ini'
                ], 400);
            }

            // Check if mahasiswa already has an active magang
            $activeMagang = DB::table('m_magang')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('status', 'aktif')
                ->exists();

            if ($activeMagang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memiliki magang aktif'
                ], 400);
            }

            // Get lowongan details untuk notifikasi
            $lowongan = DB::table('m_lowongan as l')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('l.id_lowongan', $id_lowongan)
                ->select('l.*', 'p.nama_perusahaan', 'p.email as perusahaan_email')
                ->first();

            if (!$lowongan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lowongan tidak ditemukan'
                ], 404);
            }

            // Insert lamaran
            $lamaran_id = DB::table('t_lamaran')->insertGetId([
                'id_lowongan' => $id_lowongan,
                'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                'id_dosen' => $id_dosen,
                'tanggal_lamaran' => now(),
                'auth' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // ✅ TRIGGER NOTIFIKASI - Konfirmasi lamaran berhasil dikirim
            try {
                $this->notificationService->createNotification(
                    $user->id_user,
                    'Lamaran Berhasil Dikirim! ✅',
                    "Lamaran Anda untuk posisi {$lowongan->judul_lowongan} di {$lowongan->nama_perusahaan} telah berhasil dikirim. Tim akan meninjau lamaran Anda dan memberikan kabar dalam 1-3 hari kerja.",
                    'lamaran',
                    'success',
                    false,
                    [
                        'lamaran_id' => $lamaran_id,
                        'lowongan_id' => $id_lowongan,
                        'perusahaan' => $lowongan->nama_perusahaan,
                        'posisi' => $lowongan->judul_lowongan,
                        'action' => 'submitted'
                    ],
                    14 // 2 minggu
                );

                // ✅ BONUS: Notifikasi reminder untuk melengkapi profile (jika belum lengkap)
                $profileCompletion = $this->checkProfileCompletion($mahasiswa);
                if ($profileCompletion < 80) {
                    $this->notificationService->createNotification(
                        $user->id_user,
                        'Tips: Lengkapi Profile Anda 📝',
                        "Profile Anda {$profileCompletion}% lengkap. Lengkapi profile untuk meningkatkan peluang diterima! Tambahkan foto, skills, dan pengalaman Anda.",
                        'sistem',
                        'info',
                        false,
                        ['completion' => $profileCompletion],
                        7
                    );
                }

                Log::info('Application submission notification sent', [
                    'user_id' => $user->id_user,
                    'lamaran_id' => $lamaran_id,
                    'lowongan_id' => $id_lowongan
                ]);

            } catch (\Exception $notifError) {
                Log::error('Error sending application notification: ' . $notifError->getMessage());
                // Don't rollback transaction just because notification failed
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lamaran berhasil dikirim! Notifikasi telah dikirim ke email Anda.',
                'data' => [
                    'lamaran_id' => $lamaran_id,
                    'lowongan' => $lowongan->judul_lowongan,
                    'perusahaan' => $lowongan->nama_perusahaan,
                    'status' => 'menunggu'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ENHANCED - Method cancelLamaran dengan notifikasi
    public function cancelLamaran($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

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

            // Verify lamaran belongs to mahasiswa dan get data untuk notifikasi
            $lamaran = DB::table('t_lamaran as l')
                ->join('m_lowongan as low', 'l.id_lowongan', '=', 'low.id_lowongan')
                ->join('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('l.id_lamaran', $id)
                ->where('l.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->select('l.*', 'low.judul_lowongan', 'p.nama_perusahaan')
                ->first();

            if (!$lamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lamaran tidak ditemukan atau bukan milik Anda'
                ], 404);
            }

            // Check if status is still pending
            if ($lamaran->auth !== 'menunggu') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lamaran tidak dapat dibatalkan karena sudah diproses'
                ], 400);
            }

            // Delete lamaran
            $deleted = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->delete();

            if ($deleted) {
                // ✅ TRIGGER NOTIFIKASI - Konfirmasi pembatalan
                try {
                    $this->notificationService->createNotification(
                        $user->id_user,
                        'Lamaran Dibatalkan ❌',
                        "Lamaran Anda untuk posisi {$lamaran->judul_lowongan} di {$lamaran->nama_perusahaan} telah berhasil dibatalkan. Anda dapat melamar lowongan lain yang tersedia.",
                        'lamaran',
                        'info',
                        false,
                        [
                            'lamaran_id' => $id,
                            'perusahaan' => $lamaran->nama_perusahaan,
                            'posisi' => $lamaran->judul_lowongan,
                            'action' => 'cancelled'
                        ],
                        7
                    );

                    Log::info('Application cancellation notification sent', [
                        'user_id' => $user->id_user,
                        'lamaran_id' => $id
                    ]);

                } catch (\Exception $notifError) {
                    Log::error('Error sending cancellation notification: ' . $notifError->getMessage());
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Lamaran berhasil dibatalkan'
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membatalkan lamaran'
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error canceling application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ HELPER METHOD - Check profile completion
    private function checkProfileCompletion($mahasiswa)
    {
        $completion = 0;
        $totalFields = 10; // Total field yang harus diisi

        // Basic info (40%)
        if (!empty($mahasiswa->nim)) $completion += 4;
        if (!empty($mahasiswa->alamat)) $completion += 4;
        if (!empty($mahasiswa->no_hp)) $completion += 4;
        if (!empty($mahasiswa->ipk)) $completion += 4;

        // Photo (10%)
        if (!empty($mahasiswa->foto)) $completion += 10;

        // Skills (20%)
        $skillsCount = DB::table('t_skill_mahasiswa')
            ->where('user_id', $mahasiswa->id_user)
            ->count();
        if ($skillsCount > 0) $completion += 20;

        // Documents (20%)
        $documentsCount = DB::table('m_dokumen')
            ->where('id_user', $mahasiswa->id_user)
            ->count();
        if ($documentsCount > 0) $completion += 20;

        // Minat (10%)
        $minatCount = DB::table('t_mahasiswa_minat')
            ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->count();
        if ($minatCount > 0) $completion += 10;

        return min(100, $completion);
    }

    // ✅ TAMBAHAN - Method untuk mendapatkan rekomendasi lowongan
    public function getRecommendedLowongan()
    {
        try {
            $user = Auth::user();
            
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

            // Get minat mahasiswa
            $minatIds = DB::table('t_mahasiswa_minat')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->pluck('id_minat')
                ->toArray();

            // Get recommended lowongan based on minat
            $recommendedLowongan = DB::table('m_lowongan as l')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_kota as k', 'p.id_kota', '=', 'k.id_kota')
                ->whereIn('l.id_minat', $minatIds)
                ->where('l.status', 'aktif')
                ->where('l.kuota_tersedia', '>', 0)
                ->whereNotExists(function($query) use ($mahasiswa) {
                    $query->select(DB::raw(1))
                          ->from('t_lamaran')
                          ->whereRaw('t_lamaran.id_lowongan = l.id_lowongan')
                          ->where('t_lamaran.id_mahasiswa', $mahasiswa->id_mahasiswa);
                })
                ->select([
                    'l.*',
                    'p.nama_perusahaan',
                    'p.logo_perusahaan',
                    'k.nama_kota'
                ])
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $recommendedLowongan
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recommended lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat rekomendasi: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ TAMBAHKAN method baru untuk detail lamaran
    public function getDetailLamaran($id)
    {
        try {
            $user = Auth::user();
            
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

            // ✅ PERBAIKI: GET DETAIL LAMARAN dengan nama tabel yang benar
            $lamaran = DB::table('t_lamaran as l')
                ->join('m_lowongan as low', 'l.id_lowongan', '=', 'low.id_lowongan')
                ->join('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_wilayah as w', 'p.wilayah_id', '=', 'w.wilayah_id')
                ->leftJoin('m_jenis as jl', 'low.jenis_id', '=', 'jl.jenis_id') // ✅ FIX: m_jenis bukan m_jenis_lowongan
                ->leftJoin('m_periode as per', 'low.periode_id', '=', 'per.periode_id') // ✅ TAMBAH: periode info
                ->where('l.id_lamaran', $id)
                ->where('l.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->select([
                    // Lamaran info
                    'l.id_lamaran',
                    'l.tanggal_lamaran',
                    'l.auth as status',
                    
                    // Lowongan details
                    'low.id_lowongan',
                    'low.judul_lowongan',
                    'low.deskripsi as deskripsi_lowongan',
                    'low.kapasitas',
                    'low.min_ipk',
                    'low.created_at as lowongan_posted',
                    
                    // Perusahaan details
                    'p.perusahaan_id',
                    'p.nama_perusahaan',
                    'p.logo',
                    'p.alamat_perusahaan',
                    'p.email as perusahaan_email',
                    'p.website',
                    'p.deskripsi as deskripsi_perusahaan',
                    
                    // Location & Type
                    'w.nama_kota',
                    'jl.nama_jenis',
                    'per.waktu as periode_waktu'
                ])
                ->first();

            if (!$lamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lamaran tidak ditemukan atau bukan milik Anda'
                ], 404);
            }

            // ✅ GENERATE LOGO URL yang benar
            $logoUrl = null;
            if ($lamaran->logo && !empty($lamaran->logo)) {
                if (strpos($lamaran->logo, 'http') === 0) {
                    $logoUrl = $lamaran->logo;
                } else if (strpos($lamaran->logo, 'storage/') === 0) {
                    $logoUrl = asset($lamaran->logo);
                } else {
                    $logoUrl = asset('storage/' . $lamaran->logo);
                }
            }

            // ✅ TRANSFORM data untuk response
            $detailLamaran = [
                'id_lamaran' => $lamaran->id_lamaran,
                'tanggal_lamaran' => $lamaran->tanggal_lamaran,
                'status' => $lamaran->status,
                
                // Lowongan details
                'id_lowongan' => $lamaran->id_lowongan,
                'judul_lowongan' => $lamaran->judul_lowongan,
                'deskripsi_lowongan' => $lamaran->deskripsi_lowongan,
                'kapasitas' => $lamaran->kapasitas,
                'min_ipk' => $lamaran->min_ipk,
                'lowongan_posted' => $lamaran->lowongan_posted,
                'nama_jenis' => $lamaran->nama_jenis,
                'periode_waktu' => $lamaran->periode_waktu,
                
                // Perusahaan details
                'perusahaan_id' => $lamaran->perusahaan_id,
                'nama_perusahaan' => $lamaran->nama_perusahaan,
                'logo_perusahaan' => $lamaran->logo,
                'logo_url' => $logoUrl,
                'alamat_perusahaan' => $lamaran->alamat_perusahaan,
                'perusahaan_email' => $lamaran->perusahaan_email,
                'website' => $lamaran->website,
                'deskripsi_perusahaan' => $lamaran->deskripsi_perusahaan,
                'nama_kota' => $lamaran->nama_kota
            ];

            // ✅ LOG untuk debugging
            Log::info('Detail lamaran loaded:', [
                'lamaran_id' => $id,
                'user_id' => $user->id_user,
                'perusahaan' => $lamaran->nama_perusahaan,
                'has_logo' => !empty($logoUrl)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Detail lamaran berhasil dimuat',
                'data' => $detailLamaran
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting detail lamaran: ' . $e->getMessage(), [
                'lamaran_id' => $id,
                'user_id' => Auth::user()->id_user ?? 'unknown',
                'stack' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail lamaran: ' . $e->getMessage()
            ], 500);
        }
    }
}