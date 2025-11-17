<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Magang;
use App\Services\KapasitasLowonganService;
use App\Services\NotificationService; // ✅ TAMBAHAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MagangController extends Controller
{
    protected $kapasitasService;
    protected $notificationService; // ✅ TAMBAHAN

    public function __construct(
        KapasitasLowonganService $kapasitasService,
        NotificationService $notificationService // ✅ TAMBAHAN
    ) {
        $this->kapasitasService = $kapasitasService;
        $this->notificationService = $notificationService; // ✅ TAMBAHAN
    }

    // ✅ TIDAK BERUBAH - Method index tetap sama
    public function index(Request $request)
    {
        try {
            // Query from t_lamaran table for pending applications
            $lamaran = DB::table('t_lamaran')
                ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->select(
                    't_lamaran.id_lamaran as id',
                    'm_user.name',
                    'm_mahasiswa.nim',
                    'm_user.email',
                    'm_lowongan.judul_lowongan',
                    'm_perusahaan.nama_perusahaan',
                    't_lamaran.auth',
                    DB::raw("'menunggu' as status")
                )
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'mahasiswa' => [
                            'name' => $item->name ?? 'Tidak Diketahui',
                            'nim' => $item->nim ?? 'Tidak Diketahui',
                            'email' => $item->email ?? 'Tidak Diketahui',
                        ],
                        'judul_lowongan' => $item->judul_lowongan ?? 'Tidak Diketahui',
                        'perusahaan' => [
                            'nama_perusahaan' => $item->nama_perusahaan ?? 'Tidak Diketahui',
                        ],
                        'status' => $item->status ?? 'menunggu',
                        'auth' => $item->auth ?? 'menunggu',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $lamaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching data lamaran: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data permintaan: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ REPLACE: Method show di MagangController.php yang lebih dinamis
    public function show($id)
    {
        try {
            // ✅ ENHANCED: Query dengan JOIN yang lebih lengkap sesuai struktur database
            $lamaran = DB::table('t_lamaran as l')
                ->where('l.id_lamaran', $id)
                // Mahasiswa & User
                ->join('m_mahasiswa as mhs', 'l.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                // Kelas & Prodi (untuk mendapat prodi yang benar)
                ->leftJoin('m_kelas as k', 'mhs.id_kelas', '=', 'k.id_kelas')
                ->leftJoin('m_prodi as pr', 'k.kode_prodi', '=', 'pr.kode_prodi')
                // Wilayah mahasiswa
                ->leftJoin('m_wilayah as w_mhs', 'mhs.wilayah_id', '=', 'w_mhs.wilayah_id')
                // Lowongan & Periode
                ->join('m_lowongan as low', 'l.id_lowongan', '=', 'low.id_lowongan')
                ->leftJoin('m_periode as per', 'low.periode_id', '=', 'per.periode_id')
                ->leftJoin('m_jenis as jen', 'low.jenis_id', '=', 'jen.jenis_id')
                // Perusahaan & Wilayah perusahaan
                ->join('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->leftJoin('m_wilayah as w_per', 'p.wilayah_id', '=', 'w_per.wilayah_id')
                // Dosen pembimbing (jika ada)
                ->leftJoin('m_dosen as d', 'l.id_dosen', '=', 'd.id_dosen')
                ->leftJoin('m_user as u_dosen', 'd.user_id', '=', 'u_dosen.id_user')
                ->select([
                    // Data lamaran
                    'l.id_lamaran',
                    'l.id_mahasiswa', 
                    'l.id_lowongan',
                    'l.id_dosen',
                    'l.tanggal_lamaran',
                    'l.auth',
                    
                    // Data mahasiswa lengkap
                    'mhs.nim',
                    'mhs.id_user',
                    'mhs.alamat as alamat_mahasiswa',
                    'mhs.ipk',
                    'mhs.telp',
                    'mhs.cv',
                    'u.name as nama_mahasiswa',
                    'u.email as email_mahasiswa',
                    
                    // Data kelas & prodi
                    'k.nama_kelas',
                    'pr.nama_prodi',
                    'pr.kode_prodi',
                    'w_mhs.nama_kota as kota_mahasiswa',
                    
                    // Data lowongan lengkap
                    'low.judul_lowongan',
                    'low.deskripsi as deskripsi_lowongan',
                    'low.kapasitas',
                    'low.min_ipk',
                    
                    // Data periode
                    'per.waktu as periode_waktu',
                    'per.tgl_mulai as periode_mulai',
                    'per.tgl_selesai as periode_selesai',
                    
                    // Data jenis magang
                    'jen.nama_jenis',
                    
                    // Data perusahaan lengkap
                    'p.nama_perusahaan',
                    'p.alamat_perusahaan', 
                    'p.contact_person',
                    'p.email as email_perusahaan',
                    'p.instagram',
                    'p.website',
                    'p.deskripsi as deskripsi_perusahaan',
                    'p.logo',
                    'p.gmaps',
                    'w_per.nama_kota as kota_perusahaan',
                    
                    // Data dosen pembimbing
                    'd.nip',
                    'u_dosen.name as nama_dosen',
                    'u_dosen.email as email_dosen'
                ])
                ->first();

            if (!$lamaran) {
                return response()->json([
                    'success' => false,
                    'message' => "Data lamaran dengan ID {$id} tidak ditemukan."
                ], 404);
            }

            // ✅ ENHANCED: Ambil skills mahasiswa yang dinamis
            $skills = [];
            if ($lamaran->id_user) {
                try {
                    $skills = DB::table('t_skill_mahasiswa as sm')
                        ->join('m_skill as s', 'sm.skill_id', '=', 's.skill_id')
                        ->where('sm.user_id', $lamaran->id_user)
                        ->select([
                            's.nama as nama_skill',
                            'sm.lama_skill'
                        ])
                        ->get()
                        ->map(function($skill) {
                            return [
                                'nama_skill' => $skill->nama_skill,
                                'lama_skill' => $skill->lama_skill ? $skill->lama_skill . ' tahun' : 'Belum ditentukan'
                            ];
                        })
                        ->toArray();
                } catch (\Exception $skillError) {
                    Log::warning('Could not fetch skills: ' . $skillError->getMessage());
                    $skills = [];
                }
            }

            // ✅ ENHANCED: Ambil dokumen mahasiswa yang dinamis
            $dokumen = [];
            if ($lamaran->id_mahasiswa) {
                try {
                    // Get CV from m_mahasiswa table
                    $cv = DB::table('m_mahasiswa')
                        ->where('id_mahasiswa', $lamaran->id_mahasiswa)
                        ->select('cv', 'cv_updated_at')
                        ->first();
                    
                    if ($cv && $cv->cv) {
                        // Calculate file size if available
                        $filePath = storage_path('app/public/' . $cv->cv);
                        $fileSize = 'Unknown';
                        
                        if (file_exists($filePath)) {
                            $bytes = filesize($filePath);
                            $fileSize = $this->formatFileSize($bytes);
                        }
                        
                        $dokumen[] = [
                            'file_type' => 'CV',
                            'file_name' => basename($cv->cv),
                            'description' => 'Curriculum Vitae (CV)',
                            'upload_date' => $cv->cv_updated_at ? \Carbon\Carbon::parse($cv->cv_updated_at)->format('d M Y') : 'Tidak diketahui',
                            'file_size' => $fileSize,
                            'file_url' => asset('storage/' . $cv->cv)
                        ];
                    } else {
                        Log::warning('No CV found for mahasiswa', ['id_mahasiswa' => $lamaran->id_mahasiswa]);
                    }
                } catch (\Exception $cvError) {
                    Log::warning('Could not fetch CV: ' . $cvError->getMessage());
                }
            }

            // ✅ ENHANCED: Ambil minat mahasiswa (jika ada)
            $minat = [];
            if ($lamaran->id_mahasiswa) {
                try {
                    $minat = DB::table('t_minat_mahasiswa as mm')
                        ->join('m_minat as m', 'mm.minat_id', '=', 'm.minat_id')
                        ->where('mm.mahasiswa_id', $lamaran->id_mahasiswa)
                        ->pluck('m.nama_minat')
                        ->toArray();
                } catch (\Exception $minatError) {
                    Log::warning('Could not fetch minat: ' . $minatError->getMessage());
                    $minat = [];
                }
            }

            // ✅ ENHANCED: Ambil skills yang dibutuhkan lowongan
            $skillsLowongan = [];
            try {
                $skillsLowongan = DB::table('t_skill_lowongan as sl')
                    ->join('m_skill as s', 'sl.id_skill', '=', 's.skill_id')
                    ->where('sl.id_lowongan', $lamaran->id_lowongan)
                    ->pluck('s.nama')
                    ->toArray();
            } catch (\Exception $skillLowError) {
                Log::warning('Could not fetch lowongan skills: ' . $skillLowError->getMessage());
                $skillsLowongan = [];
            }

            // ✅ ENHANCED: Cek informasi dosen pembimbing yang lengkap
            $dosenPembimbing = null;
            if ($lamaran->id_dosen && $lamaran->nama_dosen) {
                $dosenPembimbing = [
                    'assigned' => true,
                    'nama' => $lamaran->nama_dosen,
                    'nip' => $lamaran->nip,
                    'email' => $lamaran->email_dosen
                ];
            } else {
                $dosenPembimbing = [
                    'assigned' => false,
                    'nama' => null,
                    'nip' => null,
                    'email' => null
                ];
            }

            // ✅ ENHANCED: Handle rejection notes dari tabel notifikasi
            $catatanPenolakan = null;
            $tanggalDitolak = null;

            if ($lamaran->auth === 'ditolak') {
                try {
                    // Cari notifikasi penolakan
                    $notifikasi = DB::table('m_notifikasi')
                        ->where('id_user', $lamaran->id_user)
                        ->where('kategori', 'lamaran')
                        ->where('jenis', 'danger')
                        ->whereRaw("JSON_EXTRACT(data_terkait, '$.lamaran_id') = ?", [$id])
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($notifikasi && $notifikasi->data_terkait) {
                        $notifData = json_decode($notifikasi->data_terkait, true);
                        $catatanPenolakan = $notifData['catatan_penolakan'] ?? null;
                        $tanggalDitolak = $notifData['tanggal_ditolak'] ?? null;
                        
                        if ($tanggalDitolak) {
                            $tanggalDitolak = \Carbon\Carbon::parse($tanggalDitolak)->format('d M Y');
                        }
                    }
                } catch (\Exception $notifError) {
                    Log::warning('Could not retrieve rejection details: ' . $notifError->getMessage());
                }
            }

            // ✅ ENHANCED: Format response data yang dinamis dan lengkap
            $formattedData = [
                'id' => $lamaran->id_lamaran,
                'tanggal_lamaran' => \Carbon\Carbon::parse($lamaran->tanggal_lamaran)->format('d M Y'),
                'status' => $this->mapStatusAuth($lamaran->auth),
                'auth' => $lamaran->auth,
                
                // ✅ DYNAMIC: Data mahasiswa lengkap
                'mahasiswa' => [
                    'name' => $lamaran->nama_mahasiswa,
                    'nim' => $lamaran->nim,
                    'email' => $lamaran->email_mahasiswa,
                    'prodi' => $lamaran->nama_prodi ?? 'Teknologi Informasi',
                    'kelas' => $lamaran->nama_kelas ?? 'Tidak diketahui',
                    'ipk' => $lamaran->ipk ? number_format($lamaran->ipk, 2) : 'Belum diisi',
                    'alamat' => $lamaran->alamat_mahasiswa ?? 'Belum diisi',
                    'telp' => $lamaran->telp ?? 'Belum diisi',
                    'kota' => $lamaran->kota_mahasiswa ?? 'Belum diisi',
                    'skills' => $skills,
                    'minat' => $minat
                ],

                // ✅ DYNAMIC: Data lowongan lengkap
                'lowongan' => [
                    'judul_lowongan' => $lamaran->judul_lowongan,
                    'deskripsi' => $lamaran->deskripsi_lowongan ?? 'Tidak ada deskripsi',
                    'kapasitas' => $lamaran->kapasitas,
                    'min_ipk' => $lamaran->min_ipk ? number_format($lamaran->min_ipk, 2) : 'Tidak ditentukan',
                    'jenis_magang' => $lamaran->nama_jenis ?? 'Tidak ditentukan',
                    'skills_required' => $skillsLowongan,
                    'periode' => [
                        'waktu' => $lamaran->periode_waktu ?? 'Tidak ditentukan',
                        'tanggal_mulai' => $lamaran->periode_mulai ? \Carbon\Carbon::parse($lamaran->periode_mulai)->format('d M Y') : 'Tidak ditentukan',
                        'tanggal_selesai' => $lamaran->periode_selesai ? \Carbon\Carbon::parse($lamaran->periode_selesai)->format('d M Y') : 'Tidak ditentukan'
                    ]
                ],

                // ✅ DYNAMIC: Data perusahaan lengkap
                'perusahaan' => [
                    'nama_perusahaan' => $lamaran->nama_perusahaan,
                    'alamat_perusahaan' => $lamaran->alamat_perusahaan ?? 'Tidak diketahui',
                    'kota' => $lamaran->kota_perusahaan ?? 'Tidak diketahui',
                    'contact_person' => $lamaran->contact_person,
                    'email' => $lamaran->email_perusahaan,
                    'instagram' => $lamaran->instagram ? '@' . str_replace('@', '', $lamaran->instagram) : 'Tidak ada',
                    'website' => $lamaran->website ?? 'Tidak ada',
                    'deskripsi' => $lamaran->deskripsi_perusahaan ?? 'Tidak ada deskripsi',
                    'logo' => $lamaran->logo ? asset('storage/' . $lamaran->logo) : null,
                    'gmaps' => $lamaran->gmaps ?? null
                ],

                // ✅ DYNAMIC: Data dokumen
                'dokumen' => $dokumen,

                // ✅ DYNAMIC: Data dosen pembimbing
                'dosen_pembimbing' => $dosenPembimbing,

                // ✅ DYNAMIC: Data penolakan (jika ada)
                'catatan' => $catatanPenolakan,
                'tanggal_ditolak' => $tanggalDitolak
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching detail lamaran: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail lamaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ HELPER: Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * ✅ HELPER: Map status auth ke status yang readable
     */
    private function mapStatusAuth($auth)
    {
        switch (strtolower($auth)) {
            case 'diterima':
                return 'Diterima';
            case 'ditolak':
                return 'Ditolak';
            case 'menunggu':
            default:
                return 'Menunggu Persetujuan';
        }
    }

    // ✅ ENHANCED - Method accept dengan notifikasi
    public function accept(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            
            // Get lamaran data
            $lamaran = DB::table('t_lamaran as l')
                ->join('m_lowongan as low', 'l.id_lowongan', '=', 'low.id_lowongan')
                ->join('m_mahasiswa as m', 'l.id_mahasiswa', '=', 'm.id_mahasiswa')
                ->join('m_perusahaan as per', 'low.perusahaan_id', '=', 'per.perusahaan_id')
                ->where('l.id_lamaran', $id)
                ->select(
                    'l.*',
                    'low.judul_lowongan',
                    'per.nama_perusahaan',
                    'm.id_user'
                )
                ->first();

            if (!$lamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lamaran tidak ditemukan.'
                ], 404);
            }

            // Use dates from request instead of periode
            $startDate = \Carbon\Carbon::parse($request->tgl_mulai);
            $endDate = \Carbon\Carbon::parse($request->tgl_selesai);
            $durasiHari = $startDate->diffInDays($endDate);

            // Insert into m_magang table with requested dates
            $magang_id = DB::table('m_magang')->insertGetId([
                'id_lowongan' => $lamaran->id_lowongan,
                'id_mahasiswa' => $lamaran->id_mahasiswa,
                'id_dosen' => $lamaran->id_dosen,
                'status' => 'aktif',
                'tgl_mulai' => $startDate->format('Y-m-d'),
                'tgl_selesai' => $endDate->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Delete other applications
            DB::table('t_lamaran')
                ->where('id_mahasiswa', $lamaran->id_mahasiswa)
                ->delete();

            // Update capacity if service exists
            if (isset($this->kapasitasService)) {
                $capacityUpdated = $this->kapasitasService->decrementKapasitas($lamaran->id_lowongan);
                if (!$capacityUpdated) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memperbarui kapasitas tersedia.'
                    ], 500);
                }
            }

            // Send notifications
            if (isset($this->notificationService)) {
                try {
                    $this->notificationService->lamaranDiterima(
                        $lamaran->id_user,
                        $lamaran->nama_perusahaan,
                        $lamaran->judul_lowongan,
                        $id
                    );

                    // Send schedule notification
                    $this->notificationService->createNotification(
                        $lamaran->id_user,
                        'Jadwal Magang Telah Ditetapkan 📅',
                        "Magang Anda di {$lamaran->nama_perusahaan} dijadwalkan mulai " . 
                        $startDate->format('d M Y') . " sampai " . $endDate->format('d M Y'),
                        'magang',
                        'success'
                    );
                } catch (\Exception $notifError) {
                    Log::error('Error sending notification: ' . $notifError->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permintaan magang berhasil diterima.',
                'magang_id' => $magang_id,
                'schedule' => [
                    'tgl_mulai' => $startDate->format('d M Y'),
                    'tgl_selesai' => $endDate->format('d M Y'),
                    'durasi_hari' => $durasiHari
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error accepting magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menerima permintaan magang: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ ENHANCED - Method reject dengan notifikasi
    public function reject(Request $request, $id)
    {
        try {
            // Validasi request
            $request->validate([
                'catatan' => 'nullable|string|max:1000'
            ]);

            // Cari data lamaran
            $lamaran = DB::table('t_lamaran')->where('id_lamaran', $id)->first();
            
            if (!$lamaran) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data lamaran tidak ditemukan'
                ], 404);
            }

            // Validasi status
            if ($lamaran->auth === 'ditolak') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lamaran sudah ditolak sebelumnya'
                ], 400);
            }

            if ($lamaran->auth === 'diterima') {
                return response()->json([
                    'success' => false,
                    'message' => 'Lamaran yang sudah diterima tidak dapat ditolak'
                ], 400);
            }

            // Update status
            $updated = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->update([
                    'auth' => 'ditolak',
                    'updated_at' => now()
                ]);

            if (!$updated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status lamaran'
                ], 500);
            }

            // Ambil data mahasiswa untuk notifikasi
            $mahasiswaData = DB::table('t_lamaran as l')
                ->join('m_mahasiswa as mhs', 'l.id_mahasiswa', '=', 'mhs.id_mahasiswa')
                ->join('m_user as u', 'mhs.id_user', '=', 'u.id_user')
                ->join('m_lowongan as low', 'l.id_lowongan', '=', 'low.id_lowongan')
                ->join('m_perusahaan as p', 'low.perusahaan_id', '=', 'p.perusahaan_id')
                ->where('l.id_lamaran', $id)
                ->select([
                    'u.id_user',
                    'u.name as nama_mahasiswa',
                    'u.email',
                    'low.judul_lowongan',
                    'p.nama_perusahaan'
                ])
                ->first();

            if ($mahasiswaData) {
                try {
                    // ✅ IMPROVED: Simpan notifikasi dengan fallback untuk nama tabel
                    $notifikasiData = [
                        'lamaran_id' => $id,
                        'lowongan_title' => $mahasiswaData->judul_lowongan,
                        'perusahaan' => $mahasiswaData->nama_perusahaan,
                        'catatan_penolakan' => $request->catatan,
                        'tanggal_ditolak' => now()->toDateString(),
                        'action_url' => '/mahasiswa/lowongan',
                        'type' => 'rejection'
                    ];

                    // ✅ TRY: Coba beberapa kemungkinan nama tabel notifikasi
                    $possibleTables = ['notifications', 't_notifikasi', 'm_notifikasi', 't_notification'];
                    $notificationSaved = false;

                    foreach ($possibleTables as $tableName) {
                        try {
                            $tableExists = DB::select("SHOW TABLES LIKE '{$tableName}'");
                            
                            if (!empty($tableExists)) {
                                if ($tableName === 'notifications') {
                                    // Laravel default notifications table structure
                                    DB::table($tableName)->insert([
                                        'id' => \Illuminate\Support\Str::uuid(),
                                        'type' => 'App\Notifications\LamaranRejected',
                                        'notifiable_type' => 'App\Models\User',
                                        'notifiable_id' => $mahasiswaData->id_user,
                                        'data' => json_encode($notifikasiData),
                                        'read_at' => null,
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ]);
                                } else {
                                    // Custom notification table structure
                                    $insertData = [
                                        'user_id' => $mahasiswaData->id_user,
                                        'data' => json_encode($notifikasiData),
                                        'created_at' => now(),
                                        'updated_at' => now()
                                    ];

                                    // Try different column names
                                    if ($tableName === 't_notifikasi') {
                                        $insertData['judul'] = 'Lamaran Magang Ditolak ❌';
                                        $insertData['pesan'] = "Maaf, lamaran Anda untuk posisi '{$mahasiswaData->judul_lowongan}' di {$mahasiswaData->nama_perusahaan} telah ditolak." . 
                                                              ($request->catatan ? " Catatan: {$request->catatan}" : ' Jangan menyerah, masih banyak kesempatan lainnya!');
                                        $insertData['tipe'] = 'lamaran_rejected';
                                        $insertData['dibaca'] = 0;
                                        $insertData['tanggal_kirim'] = now();
                                        $insertData['tanggal_kadaluarsa'] = now()->addDays(7);
                                    } else {
                                        $insertData['title'] = 'Lamaran Magang Ditolak ❌';
                                        $insertData['message'] = "Maaf, lamaran Anda untuk posisi '{$mahasiswaData->judul_lowongan}' di {$mahasiswaData->nama_perusahaan} telah ditolak." . 
                                                               ($request->catatan ? " Catatan: {$request->catatan}" : ' Jangan menyerah, masih banyak kesempatan lainnya!');
                                        $insertData['type'] = 'lamaran_rejected';
                                        $insertData['is_read'] = 0;
                                    }

                                    DB::table($tableName)->insert($insertData);
                                }
                                
                                $notificationSaved = true;
                                Log::info("Notification saved to table: {$tableName}", [
                                    'lamaran_id' => $id,
                                    'user_id' => $mahasiswaData->id_user
                                ]);
                                break;
                            }
                        } catch (\Exception $tableError) {
                            Log::debug("Failed to save notification to {$tableName}: " . $tableError->getMessage());
                            continue;
                        }
                    }

                    if (!$notificationSaved) {
                        Log::warning('Could not save notification - no suitable table found');
                    }

                } catch (\Exception $notifError) {
                    Log::error('Error saving rejection notification: ' . $notifError->getMessage());
                    // Jangan gagalkan proses utama
                }

                // Log aktivitas
                Log::info('Lamaran request rejected', [
                    'lamaran_id' => $id,
                    'mahasiswa' => $mahasiswaData->nama_mahasiswa,
                    'lowongan' => $mahasiswaData->judul_lowongan,
                    'perusahaan' => $mahasiswaData->nama_perusahaan,
                    'catatan' => $request->catatan,
                    'rejected_by' => auth()->user()->name ?? 'System'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lamaran berhasil ditolak',
                'data' => [
                    'id_lamaran' => $id,
                    'auth' => 'ditolak',
                    'catatan' => $request->catatan,
                    'tanggal_ditolak' => now()->toDateString()
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error rejecting lamaran request: ' . $e->getMessage(), [
                'lamaran_id' => $id,
                'user_id' => auth()->user()->id ?? 'unknown',
                'stack' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    // ✅ TIDAK BERUBAH - Method lainnya tetap sama
    public function getAvailable()
    {
        try {
            Log::info('Fetching available applications from t_lamaran');
            
            $availableApplications = DB::table('t_lamaran')
                ->join('m_mahasiswa', 't_lamaran.id_mahasiswa', '=', 'm_mahasiswa.id_mahasiswa')
                ->join('m_user', 'm_mahasiswa.id_user', '=', 'm_user.id_user')
                ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->whereNull('t_lamaran.id_dosen')
                ->where('t_lamaran.auth', 'menunggu')
                ->select(
                    't_lamaran.id_lamaran as id_magang',
                    't_lamaran.id_mahasiswa',
                    't_lamaran.id_lowongan',
                    'm_user.name',
                    'm_mahasiswa.nim',
                    'm_lowongan.judul_lowongan',
                    'm_perusahaan.nama_perusahaan'
                )
                ->get();
                
            Log::info('Found ' . $availableApplications->count() . ' available applications');

            return response()->json([
                'success' => true,
                'data' => $availableApplications
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching available applications: ' . $e->getMessage());
            Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load available applications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignPendingMagang(Request $request)
    {
        try {
            $request->validate([
                'magang_id' => 'required|exists:m_magang,id_magang',
                'dosen_id' => 'required|exists:m_dosen,id_dosen',
            ]);

            $magang = Magang::findOrFail($request->magang_id);
            $magang->id_dosen = $request->dosen_id;
            $magang->save();

            return response()->json([
                'success' => true,
                'message' => 'Dosen berhasil ditugaskan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning dosen to pending magang: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan dosen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkDosen($id)
    {
        try {
            $lamaran = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->first();

            return response()->json([
                'success' => true,
                'has_dosen' => !empty($lamaran->id_dosen)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa data dosen pembimbing'
            ], 500);
        }
    }

    public function assignDosen(Request $request, $id)
    {
        try {
            Log::info('Assigning dosen to lamaran', ['lamaran_id' => $id, 'dosen_id' => $request->dosen_id]);
            
            $lamaran = DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->first();

            if (!$lamaran) {
                Log::error('Lamaran not found', ['id' => $id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Data lamaran tidak ditemukan'
                ], 404);
            }
            
            DB::table('t_lamaran')
                ->where('id_lamaran', $id)
                ->update([
                    'id_dosen' => $request->dosen_id,
                    'updated_at' => now()
                ]);
                
            Log::info('Dosen assigned successfully', ['lamaran_id' => $id, 'dosen_id' => $request->dosen_id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Dosen berhasil ditugaskan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error assigning dosen', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menugaskan dosen: ' . $e->getMessage()
            ], 500);
        }
    }
}
