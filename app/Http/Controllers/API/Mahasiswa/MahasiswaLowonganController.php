<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\NotificationService; // ✅ TAMBAHAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MahasiswaLowonganController extends Controller
{
    protected $notificationService; // ✅ TAMBAHAN

    public function __construct(NotificationService $notificationService) // ✅ TAMBAHAN
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        try {
            // Parameter filter dari request
            $perusahaan_id = request('perusahaan_id');
            $skill_id = request('skill_id');

            // Log filter untuk debugging
            Log::info('Filter applied:', [
                'perusahaan_id' => $perusahaan_id,
                'skill_id' => $skill_id
            ]);

                  // Ambil semua periode_id aktif dari t_periode
        $periodeAktifIds = DB::table('t_periode')->pluck('periode_id')->toArray();

        // Query dasar
        $query = DB::table('m_lowongan as l')
            ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
            ->join('m_wilayah as w', 'p.wilayah_id', '=', 'w.wilayah_id')
            ->join('m_periode as pr', 'l.periode_id', '=', 'pr.periode_id')
            // ✅ Tambahkan filter periode aktif
            ->whereIn('l.periode_id', $periodeAktifIds);


            // Filter berdasarkan perusahaan jika ada
            if ($perusahaan_id) {
                $query->where('p.perusahaan_id', $perusahaan_id);
            }

            // Filter berdasarkan skill jika ada
            if ($skill_id) {
                $query->whereExists(function ($query) use ($skill_id) {
                    $query->select(DB::raw(1))
                        ->from('t_skill_lowongan as sl')
                        ->whereRaw('sl.id_lowongan = l.id_lowongan')
                        ->where('sl.id_skill', $skill_id);
                });
            }

            // ✅ PERBAIKI: Ambil data lowongan dengan logo
            $lowongan = $query->select(
                'l.id_lowongan',
                'l.judul_lowongan',
                'l.deskripsi',
                'l.kapasitas',
                'l.min_ipk', // ← TAMBAHKAN jika ada
                'p.nama_perusahaan',
                'p.perusahaan_id',
                'p.logo', // ← TAMBAHKAN logo
                'p.email',
                'p.website',
                'p.contact_person',
                'w.nama_kota',
                'w.wilayah_id',
                'pr.waktu',
                'pr.periode_id'
            )->get();

            // Transformasi data untuk response sesuai format yang diharapkan frontend
            $lowonganData = $lowongan->map(function ($item) {
                // Get skills for this lowongan
                $skills = DB::table('t_skill_lowongan as sl')
                    ->join('m_skill as s', 'sl.id_skill', '=', 's.skill_id')
                    ->where('sl.id_lowongan', $item->id_lowongan)
                    ->select('s.skill_id', 's.nama as nama_skill')
                    ->get();

                // Konversi objek stdClass ke array asosiatif
                $skillsArray = [];
                foreach ($skills as $skill) {
                    $skillsArray[] = [
                        'skill_id' => $skill->skill_id,
                        'nama_skill' => $skill->nama_skill
                    ];
                }

                // ✅ PERBAIKI: Generate logo URL yang benar
                $logoUrl = null;
                if ($item->logo && !empty($item->logo)) {
                    if (strpos($item->logo, 'http') === 0) {
                        // Jika sudah URL lengkap
                        $logoUrl = $item->logo;
                    } else if (strpos($item->logo, 'storage/') === 0) {
                        // Jika sudah ada prefix storage/
                        $logoUrl = asset($item->logo);
                    } else {
                        // Jika hanya path relatif
                        $logoUrl = asset('storage/' . $item->logo);
                    }
                }

                // Format data sesuai struktur yang diharapkan oleh frontend
                return [
                    'id_lowongan' => $item->id_lowongan,
                    'judul_lowongan' => $item->judul_lowongan,
                    'deskripsi' => $item->deskripsi,
                    'kapasitas' => $item->kapasitas,
                    'min_ipk' => $item->min_ipk ?? null, // ← TAMBAHKAN jika ada
                    'perusahaan' => [
                        'perusahaan_id' => $item->perusahaan_id,
                        'nama_perusahaan' => $item->nama_perusahaan,
                        'nama_kota' => $item->nama_kota,
                        'logo' => $item->logo, // ← Path asli untuk debugging
                        'logo_url' => $logoUrl, // ← URL lengkap untuk display
                        'email' => $item->email,
                        'website' => $item->website,
                        'contact_person' => $item->contact_person
                    ],
                    'wilayah' => [
                        'wilayah_id' => $item->wilayah_id,
                        'nama_kota' => $item->nama_kota
                    ],
                    'skills' => $skillsArray,
                    'periode' => [
                        'periode_id' => $item->periode_id,
                        'waktu' => $item->waktu
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $lowonganData
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data lowongan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show detailed internship opportunity with complete company information
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            // Ambil data lowongan dengan join ke perusahaan, wilayah, dan periode
            $lowongan = DB::table('m_lowongan as l')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->join('m_wilayah as w', 'p.wilayah_id', '=', 'w.wilayah_id')
                ->join('m_periode as pr', 'l.periode_id', '=', 'pr.periode_id')
                ->where('l.id_lowongan', $id)
                ->select(
                    'l.id_lowongan',
                    'l.judul_lowongan',
                    'l.deskripsi',
                    'l.kapasitas',
                    'l.min_ipk', // ← TAMBAHKAN jika ada
                    'p.perusahaan_id',
                    'p.nama_perusahaan',
                    'p.logo', // ← TAMBAHKAN logo
                    'p.email',
                    'p.website',
                    'p.contact_person',
                    'p.alamat_perusahaan',
                    'p.deskripsi as perusahaan_deskripsi',
                    'w.nama_kota',
                    'w.wilayah_id',
                    'pr.waktu',
                    'pr.periode_id'
                )
                ->first();

            if (!$lowongan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lowongan tidak ditemukan'
                ], 404);
            }

            // Ambil skills untuk lowongan ini
            $skills = DB::table('t_skill_lowongan as sl')
                ->join('m_skill as s', 'sl.id_skill', '=', 's.skill_id')
                ->where('sl.id_lowongan', $id)
                ->select('s.skill_id', 's.nama as nama_skill')
                ->get();

            // Konversi collection ke array biasa
            $skillsArray = [];
            foreach ($skills as $skill) {
                $skillsArray[] = [
                    'skill_id' => $skill->skill_id,
                    'nama_skill' => $skill->nama_skill
                ];
            }

            // ✅ PERBAIKI: Generate logo URL yang benar
            $logoUrl = null;
            if ($lowongan->logo && !empty($lowongan->logo)) {
                if (strpos($lowongan->logo, 'http') === 0) {
                    $logoUrl = $lowongan->logo;
                } else if (strpos($lowongan->logo, 'storage/') === 0) {
                    $logoUrl = asset($lowongan->logo);
                } else {
                    $logoUrl = asset('storage/' . $lowongan->logo);
                }
            }

            // ✅ PERBAIKI: Siapkan data perusahaan dengan logo
            $perusahaanData = [
                'perusahaan_id' => $lowongan->perusahaan_id,
                'nama_perusahaan' => $lowongan->nama_perusahaan,
                'nama_kota' => $lowongan->nama_kota,
                'logo' => $lowongan->logo, // ← Path asli
                'logo_url' => $logoUrl, // ← URL lengkap
                'email' => $lowongan->email,
                'website' => $lowongan->website,
                'contact_person' => $lowongan->contact_person,
                'alamat_perusahaan' => $lowongan->alamat_perusahaan,
                'deskripsi' => $lowongan->perusahaan_deskripsi,
                // Fallback untuk backward compatibility
                'telp' => $lowongan->contact_person
            ];

            // Get current user's application status if authenticated
            $applicationStatus = null;
            if (Auth::check()) {
                $user = Auth::user();
                $mahasiswa = DB::table('m_mahasiswa')
                    ->where('id_user', $user->id_user)
                    ->first();

                if ($mahasiswa) {
                    $lamaran = DB::table('t_lamaran')
                        ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                        ->where('id_lowongan', $id)
                        ->select('id_lamaran', 'auth as status', 'tanggal_lamaran')
                        ->first();

                    if ($lamaran) {
                        $applicationStatus = [
                            'id_lamaran' => $lamaran->id_lamaran,
                            'status' => $lamaran->status,
                            'tanggal_lamaran' => $lamaran->tanggal_lamaran,
                            'can_cancel' => $lamaran->status === 'menunggu'
                        ];
                    }
                }
            }

            // Format data untuk response
            $result = [
                'id_lowongan' => $lowongan->id_lowongan,
                'judul_lowongan' => $lowongan->judul_lowongan,
                'deskripsi' => $lowongan->deskripsi,
                'kapasitas' => $lowongan->kapasitas,
                'min_ipk' => $lowongan->min_ipk ?? null,
                'perusahaan' => $perusahaanData,
                'wilayah' => [
                    'wilayah_id' => $lowongan->wilayah_id,
                    'nama_kota' => $lowongan->nama_kota
                ],
                'skills' => $skillsArray,
                'periode' => [
                    'periode_id' => $lowongan->periode_id,
                    'waktu' => $lowongan->waktu
                ],
                'application_status' => $applicationStatus // Tambahkan status lamaran
            ];

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing lowongan detail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail lowongan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function applyLowongan(Request $request, $lowongan_id)
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

            // Check if already applied
            $existingLamaran = DB::table('t_lamaran')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_lowongan', $lowongan_id)
                ->exists();

            if ($existingLamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melamar untuk lowongan ini'
                ], 400);
            }

            // Check active magang
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
                ->where('l.id_lowongan', $lowongan_id)
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
                'id_lowongan' => $lowongan_id,
                'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                'id_dosen' => $request->input('id_dosen'), // Optional dosen
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
                        'lowongan_id' => $lowongan_id,
                        'perusahaan' => $lowongan->nama_perusahaan,
                        'posisi' => $lowongan->judul_lowongan,
                        'action' => 'submitted'
                    ],
                    14 // 2 minggu
                );

                // ✅ BONUS: Check profile completion dan beri tips
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

                Log::info('Application notification sent', [
                    'user_id' => $user->id_user,
                    'lamaran_id' => $lamaran_id,
                    'lowongan_id' => $lowongan_id
                ]);

            } catch (\Exception $notifError) {
                Log::error('Error sending application notification: ' . $notifError->getMessage());
                // Don't rollback transaction just because notification failed
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lamaran berhasil dikirim! Notifikasi telah dikirim.',
                'data' => [
                    'lamaran_id' => $lamaran_id,
                    'lowongan' => $lowongan->judul_lowongan,
                    'perusahaan' => $lowongan->nama_perusahaan,
                    'status' => 'menunggu'
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error applying for lowongan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ENHANCED - Method cancelApplication dengan notifikasi
    public function cancelApplication($id)
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

            // Get lamaran data untuk notifikasi SEBELUM dihapus
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

            // Check if still pending
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

                    Log::info('Cancellation notification sent', [
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

    /**
     * Get applications for currently logged in user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserApplications()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Temukan id_mahasiswa dari tabel m_mahasiswa berdasarkan id_user
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }

            // Log untuk debugging
            Log::info('Mahasiswa ditemukan:', [
                'id_user' => $user->id_user,
                'id_mahasiswa' => $mahasiswa->id_mahasiswa
            ]);

            // Gunakan id_mahasiswa untuk mencari lamaran
            $applications = DB::table('t_lamaran')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->select('id_lowongan', 'auth as status', 'created_at as tanggal_lamaran')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user applications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // Menambahkan method baru untuk cek status magang aktif mahasiswa
    public function checkActiveInternship()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }
            
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();
                
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data mahasiswa tidak ditemukan'
                ], 404);
            }
            
            // Cek apakah mahasiswa memiliki magang aktif
            $activeMagang = DB::table('m_magang')
                ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('m_magang.status', 'aktif')
                ->select('m_magang.*', 'm_lowongan.id_lowongan', 'm_lowongan.judul_lowongan')
                ->first();
                
            return response()->json([
                'success' => true,
                'has_active_internship' => $activeMagang ? true : false,
                'active_internship' => $activeMagang
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking active internship: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memeriksa status magang: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ HELPER METHOD - Check profile completion
    private function checkProfileCompletion($mahasiswa)
    {
        $completion = 0;

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

    // ✅ TAMBAH method baru di MahasiswaLowonganController.php

    public function applyWithDocuments(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $lowongan_id = $request->input('lowongan_id');
            
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

            // Validate CV exists
            if (!$mahasiswa->cv) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV tidak ditemukan'
                ], 400);
            }

            // Check if already applied
            $existingLamaran = DB::table('t_lamaran')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_lowongan', $lowongan_id)
                ->exists();

            if ($existingLamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melamar untuk lowongan ini'
                ], 400);
            }

            // Get lowongan details
            $lowongan = DB::table('m_lowongan as l')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('l.id_lowongan', $lowongan_id)
                ->select('l.*', 'p.nama_perusahaan')
                ->first();

            if (!$lowongan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lowongan tidak ditemukan'
                ], 404);
            }

            // Insert lamaran
            $lamaran_id = DB::table('t_lamaran')->insertGetId([
                'id_lowongan' => $lowongan_id,
                'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                'tanggal_lamaran' => now(),
                'auth' => 'menunggu',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Add notification
            try {
                $this->notificationService->createNotification(
                    $user->id_user,
                    'Lamaran Berhasil Dikirim! ✅',
                    "Lamaran Anda untuk posisi {$lowongan->judul_lowongan} di {$lowongan->nama_perusahaan} telah berhasil dikirim dengan CV yang terlampir.",
                    'lamaran',
                    'success',
                    false,
                    [
                        'lamaran_id' => $lamaran_id,
                        'lowongan_id' => $lowongan_id,
                        'perusahaan' => $lowongan->nama_perusahaan,
                        'posisi' => $lowongan->judul_lowongan
                    ],
                    14
                );
            } catch (\Exception $notifError) {
                Log::error('Error sending notification: ' . $notifError->getMessage());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lamaran berhasil dikirim!',
                'data' => [
                    'lamaran_id' => $lamaran_id,
                    'lowongan' => $lowongan->judul_lowongan,
                    'perusahaan' => $lowongan->nama_perusahaan
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error applying with documents: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has applied to a specific internship position
     * 
     * @param int $lowongan_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkApplicationStatus($lowongan_id)
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

            // Check if already applied
            $lamaran = DB::table('t_lamaran')
                ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                ->where('id_lowongan', $lowongan_id)
                ->select('id_lamaran', 'auth as status', 'tanggal_lamaran')
                ->first();
            
            $hasApplied = !is_null($lamaran);
            
            return response()->json([
                'success' => true,
                'has_applied' => $hasApplied,
                'application_data' => $hasApplied ? [
                    'id_lamaran' => $lamaran->id_lamaran,
                    'status' => $lamaran->status,
                    'tanggal_lamaran' => $lamaran->tanggal_lamaran,
                    'can_cancel' => $lamaran->status === 'menunggu' // Hanya bisa dibatalkan jika status masih menunggu
                ] : null
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking application status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailableDocuments()
{
    try {
        $user = auth()->user();
        $mahasiswa = DB::table('m_mahasiswa')
            ->where('id_user', $user->id_user)
            ->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data mahasiswa tidak ditemukan'
            ], 404);
        }

        // Log untuk debugging
        Log::info('Checking CV for mahasiswa:', [
            'mahasiswa_id' => $mahasiswa->id_mahasiswa,
            'cv_path' => $mahasiswa->cv,
            'has_cv' => !empty($mahasiswa->cv)
        ]);

        $documents = [];
        
        // Check if CV exists and add to documents array
        if (!empty($mahasiswa->cv)) {
            $documents[] = [
                'id' => 'cv',
                'type' => 'CV', 
                'name' => 'Curriculum Vitae',
                'url' => asset('storage/' . $mahasiswa->cv),
                'uploaded_at' => $mahasiswa->cv_updated_at ?? null
            ];
        }

        return response()->json([
            'success' => true,
            'has_cv' => !empty($mahasiswa->cv),
            'documents' => $documents
        ]);

    } catch (\Exception $e) {
        Log::error('Error getting available documents: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Gagal memuat dokumen yang tersedia'
        ], 500);
    }
}
}