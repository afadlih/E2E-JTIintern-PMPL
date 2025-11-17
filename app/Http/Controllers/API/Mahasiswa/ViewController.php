<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ViewController extends Controller
{
    public function dashboard()
    {
        // Ambil data periode dari database
        $tPeriode = DB::table('t_periode')->first();

        $activePeriod = null;
        if ($tPeriode && $tPeriode->periode_id) {
            $activePeriod = DB::table('m_periode')
                ->where('periode_id', $tPeriode->periode_id)
                ->first();
        }

        // Ambil data user yang sedang login
        $user = auth()->user();
        $userData = null;

        if ($user) {
            $userData = DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->first();

            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if ($mahasiswa) {
                Log::info('Mahasiswa ditemukan untuk dashboard:', [
                    'user_id' => $user->id_user,
                    'mahasiswa_id' => $mahasiswa->id_mahasiswa
                ]);

                $profileCompletion = $this->checkProfileCompletion($user->id_user);

                // ✅ PERBAIKAN: Query tanpa hardcoded date
                $magangAktif = DB::table('m_magang')
                    ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                    ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                    ->leftJoin('m_dosen', 'm_magang.id_dosen', '=', 'm_dosen.id_dosen')
                    ->leftJoin('m_user as user_dosen', 'm_dosen.user_id', '=', 'user_dosen.id_user')
                    ->select(
                        'm_magang.*',
                        'm_lowongan.judul_lowongan',
                        'm_perusahaan.nama_perusahaan',
                        'm_perusahaan.logo as logo_perusahaan',
                        'user_dosen.name as nama_pembimbing'
                        // ✅ HAPUS: Hardcoded NOW() dan DATE_ADD
                        // DB::raw('NOW() as tanggal_mulai'),
                        // DB::raw('DATE_ADD(NOW(), INTERVAL 3 MONTH) as tanggal_selesai')
                    )
                    ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->where('m_magang.status', 'aktif')
                    ->first();

                Log::info('Query magang aktif (fixed):', [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'result' => $magangAktif,
                    'tgl_mulai_from_db' => $magangAktif->tgl_mulai ?? 'NULL',
                    'tgl_selesai_from_db' => $magangAktif->tgl_selesai ?? 'NULL'
                ]);

                // ✅ PERBAIKAN: Hitung progress dari database
                if ($magangAktif) {
                    if ($magangAktif->tgl_mulai && $magangAktif->tgl_selesai) {
                        $tanggalMulai = \Carbon\Carbon::parse($magangAktif->tgl_mulai);
                        $tanggalSelesai = \Carbon\Carbon::parse($magangAktif->tgl_selesai);
                        $hariIni = \Carbon\Carbon::now();

                        $totalDurasi = $tanggalMulai->diffInDays($tanggalSelesai);

                        if ($hariIni->isBefore($tanggalMulai)) {
                            // Belum mulai
                            $lewat = 0;
                            $sisaHari = $tanggalMulai->diffInDays($hariIni);
                            $progress = 0;
                        } elseif ($hariIni->isAfter($tanggalSelesai)) {
                            // Sudah selesai
                            $lewat = $totalDurasi;
                            $sisaHari = 0;
                            $progress = 100;
                        } else {
                            // Sedang berlangsung
                            $lewat = $tanggalMulai->diffInDays($hariIni);
                            $sisaHari = $hariIni->diffInDays($tanggalSelesai);
                            $progress = $totalDurasi > 0 ? ($lewat / $totalDurasi) * 100 : 0;
                        }

                        $magangInfo = [
                            'data' => $magangAktif,
                            'totalDurasi' => $totalDurasi,
                            'lewat' => $lewat,
                            'sisaHari' => $sisaHari,
                            'progress' => round($progress)
                        ];

                        Log::info('Progress calculated from database dates:', [
                            'tgl_mulai' => $tanggalMulai->format('Y-m-d'),
                            'tgl_selesai' => $tanggalSelesai->format('Y-m-d'),
                            'total_durasi' => $totalDurasi,
                            'lewat' => $lewat,
                            'sisa_hari' => $sisaHari,
                            'progress' => $progress
                        ]);
                    } else {
                        // ✅ FALLBACK: Jika tanggal tidak ada
                        $magangInfo = [
                            'data' => $magangAktif,
                            'totalDurasi' => 0,
                            'lewat' => 0,
                            'sisaHari' => 0,
                            'progress' => 0,
                            'message' => 'Jadwal magang belum ditentukan'
                        ];

                        Log::warning('Magang active but no dates set:', [
                            'magang_id' => $magangAktif->id_magang,
                            'tgl_mulai' => $magangAktif->tgl_mulai,
                            'tgl_selesai' => $magangAktif->tgl_selesai
                        ]);
                    }

                    return view('pages.mahasiswa.dashboard', [
                        'title' => 'Dashboard Mahasiswa',
                        'activePeriod' => $activePeriod,
                        'userData' => $userData,
                        'magangInfo' => $magangInfo,
                        'profileCompletion' => $profileCompletion
                    ]);
                }

                return view('pages.mahasiswa.dashboard', [
                    'title' => 'Dashboard Mahasiswa',
                    'activePeriod' => $activePeriod,
                    'userData' => $userData,
                    'profileCompletion' => $profileCompletion
                ]);
            }
        }

        return view('pages.mahasiswa.dashboard', [
            'title' => 'Dashboard Mahasiswa',
            'activePeriod' => $activePeriod,
            'userData' => $userData,
            'profileCompletion' => ['is_complete' => true]
        ]);
    }

    /**
     * Check if student profile is complete - TAMBAHAN METHOD BARU
     */
    public function checkProfileCompletion($userId)
    {
        $missingFields = [];
        
        try {
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $userId)
                ->first();

            if (!$mahasiswa) {
                return [
                    'is_complete' => false,
                    'missing_fields' => ['Data mahasiswa tidak ditemukan']
                ];
            }

            // Cek No HP/WhatsApp
            if (!$mahasiswa->telp) {
                $missingFields[] = 'No. HP/WhatsApp';
            }

            // Cek CV
            if (!$mahasiswa->cv) {
                $missingFields[] = 'CV';
            }

            // Cek Alamat  
            if (!$mahasiswa->alamat) {
                $missingFields[] = 'Alamat';
            }

            // Cek IPK
            if (!$mahasiswa->ipk) {
                $missingFields[] = 'IPK';
            }

            return [
                'is_complete' => empty($missingFields),
                'missing_fields' => $missingFields
            ];

        } catch (\Exception $e) {
            Log::error('Error checking profile completion: ' . $e->getMessage());
            return [
                'is_complete' => false, 
                'missing_fields' => ['Terjadi kesalahan sistem']
            ];
        }
    }

    public function profile()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        $userData = null;
        $mahasiswaData = null;
        $kelasData = null;
        $wilayahData = null;
        $skills = null;
        $minat = null;

        if ($user) {
            $userData = DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->first();

            // Ambil data mahasiswa dengan join kelas dan wilayah
            $mahasiswaData = DB::table('m_mahasiswa')
                ->leftJoin('m_kelas', 'm_mahasiswa.id_kelas', '=', 'm_kelas.id_kelas')
                ->leftJoin('m_wilayah', 'm_mahasiswa.wilayah_id', '=', 'm_wilayah.wilayah_id')
                ->where('m_mahasiswa.id_user', $user->id_user)
                ->select(
                    'm_mahasiswa.*',
                    'm_kelas.nama_kelas',
                    'm_kelas.kode_prodi',
                    'm_kelas.tahun_masuk',
                    'm_wilayah.nama_kota'
                )
                ->first();

            if ($mahasiswaData) {
                // Ambil keahlian mahasiswa
                $skills = DB::table('t_skill_mahasiswa')
                    ->join('m_skill', 't_skill_mahasiswa.skill_id', '=', 'm_skill.skill_id')
                    ->where('t_skill_mahasiswa.user_id', $user->id_user)
                    ->select('m_skill.skill_id', 'm_skill.nama')
                    ->get();

                Log::info('User skills loaded:', ['skills' => $skills]);

                // Ambil minat mahasiswa
                $minat = DB::table('t_minat_mahasiswa')
                    ->join('m_minat', 't_minat_mahasiswa.minat_id', '=', 'm_minat.minat_id')
                    ->where('t_minat_mahasiswa.mahasiswa_id', $mahasiswaData->id_mahasiswa)
                    ->select('m_minat.minat_id', 'm_minat.nama_minat')
                    ->get();

                Log::info('User minat loaded:', ['minat' => $minat]);

                // Set kelasData dan wilayahData untuk konsistensi dengan view
                $kelasData = $mahasiswaData;
                $wilayahData = $mahasiswaData;
            }
        }

        // Check profile completion
        $profileCompletion = $this->checkProfileCompletion($user->id_user);
        
        Log::info('Profile page loaded:', [
            'user_id' => $user->id_user,
            'skills_count' => $skills ? $skills->count() : 0,
            'minat_count' => $minat ? $minat->count() : 0,
            'profile_completion' => $profileCompletion
        ]);

        return view('pages.mahasiswa.profile', [
            'title' => 'Profil Mahasiswa',
            'userData' => $userData,
            'mahasiswaData' => $mahasiswaData,
            'kelasData' => $kelasData,
            'wilayahData' => $wilayahData,
            'skills' => $skills,
            'minat' => $minat,
            'profileCompletion' => $profileCompletion
        ]);
    }

    public function magang()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        $userData = null;
        $magangInfo = null;

        if ($user) {
            $userData = DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->first();

            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if ($mahasiswa) {
                // ✅ PERBAIKAN: Query dengan kolom yang benar
                $magangAktif = DB::table('m_magang')
                    ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                    ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                    ->leftJoin('m_dosen', 'm_magang.id_dosen', '=', 'm_dosen.id_dosen')
                    ->leftJoin('m_user as user_dosen', 'm_dosen.user_id', '=', 'user_dosen.id_user')
                    ->select(
                        'm_magang.id_magang',
                        'm_magang.tgl_mulai',      // ✅ BENAR: Gunakan tgl_mulai
                        'm_magang.tgl_selesai',    // ✅ BENAR: Gunakan tgl_selesai
                        'm_magang.status',
                        'm_lowongan.judul_lowongan',
                        'm_perusahaan.nama_perusahaan',
                        'm_perusahaan.logo as logo_perusahaan',
                        'user_dosen.name as nama_pembimbing'
                    )
                    ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->where('m_magang.status', 'aktif')
                    ->first();

                Log::info('Magang aktif data for detail page:', [
                    'mahasiswa_id' => $mahasiswa->id_mahasiswa,
                    'magang_data' => $magangAktif,
                    'tgl_mulai' => $magangAktif->tgl_mulai ?? 'NULL',
                    'tgl_selesai' => $magangAktif->tgl_selesai ?? 'NULL'
                ]);

                if ($magangAktif) {
                    if ($magangAktif->tgl_mulai && $magangAktif->tgl_selesai) {
                        $tanggalMulai = \Carbon\Carbon::parse($magangAktif->tgl_mulai);
                        $tanggalSelesai = \Carbon\Carbon::parse($magangAktif->tgl_selesai);
                        $hariIni = \Carbon\Carbon::now();

                        $totalDurasi = $tanggalMulai->diffInDays($tanggalSelesai);

                        if ($hariIni->isBefore($tanggalMulai)) {
                            // Belum mulai
                            $lewat = 0;
                            $sisaHari = $tanggalMulai->diffInDays($hariIni);
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
                            $progress = $totalDurasi > 0 ? ($lewat / $totalDurasi) * 100 : 0;
                            $statusText = 'Sedang berlangsung';
                        }

                        $magangInfo = [
                            'data' => $magangAktif,
                            'totalDurasi' => $totalDurasi,
                            'lewat' => $lewat,
                            'sisaHari' => $sisaHari,
                            'progress' => round($progress),
                            'status_text' => $statusText,
                            'tgl_mulai_formatted' => $tanggalMulai->format('d M Y'),
                            'tgl_selesai_formatted' => $tanggalSelesai->format('d M Y')
                        ];

                        Log::info('Progress calculated for detail page:', [
                            'tgl_mulai' => $tanggalMulai->format('Y-m-d'),
                            'tgl_selesai' => $tanggalSelesai->format('Y-m-d'),
                            'progress' => $progress,
                            'lewat' => $lewat,
                            'sisa_hari' => $sisaHari
                        ]);
                    } else {
                        // ✅ FALLBACK: Jika tanggal tidak ada
                        $magangInfo = [
                            'data' => $magangAktif,
                            'totalDurasi' => 0,
                            'lewat' => 0,
                            'sisaHari' => 0,
                            'progress' => 0,
                            'status_text' => 'Jadwal magang belum ditentukan',
                            'tgl_mulai_formatted' => 'Belum ditentukan',
                            'tgl_selesai_formatted' => 'Belum ditentukan',
                            'message' => 'Jadwal magang belum ditentukan oleh admin'
                        ];

                        Log::warning('Magang active but no dates set in detail page:', [
                            'magang_id' => $magangAktif->id_magang,
                            'tgl_mulai' => $magangAktif->tgl_mulai,
                            'tgl_selesai' => $magangAktif->tgl_selesai
                        ]);
                    }
                }
            }
        }

        return view('pages.mahasiswa.MagangDetail', [
            'title' => 'Detail Magang',
            'userData' => $userData,
            'magangInfo' => $magangInfo
        ]);
    }
    public function lowongan()
    {
        try {
            // Ambil data perusahaan untuk filter
            $perusahaan = DB::table('m_perusahaan')
                ->select('perusahaan_id', 'nama_perusahaan')
                ->orderBy('nama_perusahaan')
                ->get();

            // Ambil data skill untuk filter
            $skills = DB::table('m_skill')
                ->select('skill_id', 'nama')
                ->orderBy('nama')
                ->get();

            // Ambil data lowongan (awal, bisa dibatasi 6-10 item)
            $lowongan = DB::table('m_lowongan as l')
                ->join('m_perusahaan as p', 'l.perusahaan_id', '=', 'p.perusahaan_id')
                ->join('m_wilayah as w', 'p.wilayah_id', '=', 'w.wilayah_id')
                ->select(
                    'l.id_lowongan',
                    'l.judul_lowongan',
                    'p.nama_perusahaan',
                    'w.nama_kota'
                )
                // HAPUS where l.status = active
                ->limit(6) // Batasi untuk initial load
                ->get();

            // Get user data untuk header
            $user = auth()->user();
            $userData = null;
            if ($user) {
                $userData = DB::table('m_user')
                    ->where('id_user', $user->id_user)
                    ->orWhere('email', $user->email)
                    ->first();
            }

            return view('pages.mahasiswa.MhsLowongan', [
                'title' => 'Lowongan Magang',
                'perusahaan' => $perusahaan,
                'skills' => $skills,
                'lowongan' => $lowongan,
                'userData' => $userData,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading lowongan page: ' . $e->getMessage());

            return view('pages.mahasiswa.MhsLowongan', [
                'title' => 'Lowongan Magang',
                'perusahaan' => [],
                'skills' => [],
                'lowongan' => [],
                'error' => 'Terjadi kesalahan saat memuat data',
                'userData' => null,
            ]);
        }
    }
    public function lamaran(Request $request)
    {
        try {
            // Get user data untuk header
            $user = auth()->user();
            $userData = null;
            $mahasiswaData = null;
            $magangInfo = null;
            $lamaranHistory = null;

            if ($user) {
                $userData = DB::table('m_user')
                    ->where('id_user', $user->id_user)
                    ->first();

                // Get mahasiswa data
                $mahasiswaData = DB::table('m_mahasiswa')
                    ->where('id_user', $user->id_user)
                    ->first();

                if ($mahasiswaData) {
                    // ✅ FIX: Include both 'aktif' and 'selesai' status
                    $magangAktif = DB::table('m_magang')
                        ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                        ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                        ->leftJoin('m_wilayah', 'm_perusahaan.wilayah_id', '=', 'm_wilayah.wilayah_id')
                        ->leftJoin('m_dosen', 'm_magang.id_dosen', '=', 'm_dosen.id_dosen')
                        ->leftJoin('m_user as user_dosen', 'm_dosen.user_id', '=', 'user_dosen.id_user')
                        ->leftJoin('t_evaluasi', 'm_magang.id_magang', '=', 't_evaluasi.id_magang') // Add this join to check for evaluation records
                        ->where('m_magang.id_mahasiswa', $mahasiswaData->id_mahasiswa)
                        ->whereIn('m_magang.status', ['aktif', 'selesai']) // Include both statuses
                        ->whereNull('t_evaluasi.id_evaluasi') // Add this where condition to exclude records with evaluations
                        ->select([
                            'm_magang.*',
                            'm_lowongan.judul_lowongan',
                            'm_lowongan.deskripsi',
                            'm_perusahaan.nama_perusahaan',
                            'm_perusahaan.logo',
                            'm_perusahaan.alamat_perusahaan',
                            'm_wilayah.nama_kota',
                            'user_dosen.name as nama_pembimbing',
                            'm_dosen.nip as nip_pembimbing'
                        ])
                        ->orderByRaw("FIELD(m_magang.status, 'aktif', 'selesai')") // ✅ Priority: aktif first, then selesai
                        ->first();

                    Log::info('Magang aktif data for lamaran page:', [
                        'mahasiswa_id' => $mahasiswaData->id_mahasiswa,
                        'magang_data' => $magangAktif,
                        'status' => $magangAktif->status ?? 'NOT_FOUND',
                        'tgl_mulai' => $magangAktif->tgl_mulai ?? 'NULL',
                        'tgl_selesai' => $magangAktif->tgl_selesai ?? 'NULL'
                    ]);

                    if ($magangAktif) {
                        if ($magangAktif->tgl_mulai && $magangAktif->tgl_selesai) {
                            $tanggalMulai = \Carbon\Carbon::parse($magangAktif->tgl_mulai);
                            $tanggalSelesai = \Carbon\Carbon::parse($magangAktif->tgl_selesai);
                            $hariIni = \Carbon\Carbon::now();

                            $totalDurasi = $tanggalMulai->diffInDays($tanggalSelesai);

                            if ($hariIni->isBefore($tanggalMulai)) {
                                $progress = 0;
                                $sisaHari = $tanggalMulai->diffInDays($hariIni);
                                $isExpired = false;
                                $statusText = 'Akan dimulai dalam ' . $sisaHari . ' hari';
                            } elseif ($hariIni->isAfter($tanggalSelesai)) {
                                $progress = 100;
                                $sisaHari = 0;
                                $isExpired = true;
                                $statusText = 'Magang telah selesai';
                            } else {
                                $lewat = $tanggalMulai->diffInDays($hariIni);
                                $sisaHari = $hariIni->diffInDays($tanggalSelesai);
                                $progress = $totalDurasi > 0 ? ($lewat / $totalDurasi) * 100 : 0;
                                $isExpired = false;
                                $statusText = 'Sedang berlangsung';
                            }
                        } else {
                            // Fallback calculation
                            $progress = 0;
                            $sisaHari = 0;
                            $isExpired = false;
                            $statusText = 'Jadwal magang belum ditentukan';
                        }

                        // ✅ FIX: Check status database override
                        if ($magangAktif->status === 'selesai') {
                            $isExpired = true;
                            $progress = 100;
                            $statusText = 'Magang telah selesai';
                        }

                        // ✅ GENERATE: Logo URL
                        $logoUrl = null;
                        if ($magangAktif->logo && !empty($magangAktif->logo)) {
                            if (strpos($magangAktif->logo, 'http') === 0) {
                                $logoUrl = $magangAktif->logo;
                            } else if (strpos($magangAktif->logo, 'storage/') === 0) {
                                $logoUrl = asset($magangAktif->logo);
                            } else {
                                $logoUrl = asset('storage/' . $magangAktif->logo);
                            }
                        }

                        $magangInfo = [
                            'id_magang' => $magangAktif->id_magang,
                            'id_mahasiswa' => $magangAktif->id_mahasiswa,
                            'judul_lowongan' => $magangAktif->judul_lowongan,
                            'nama_perusahaan' => $magangAktif->nama_perusahaan,
                            'logo_perusahaan' => $magangAktif->logo,
                            'logo_url' => $logoUrl,
                            'nama_pembimbing' => $magangAktif->nama_pembimbing,
                            'nip_pembimbing' => $magangAktif->nip_pembimbing,
                            'status' => $magangAktif->status,
                            'tgl_mulai' => $magangAktif->tgl_mulai,
                            'tgl_selesai' => $magangAktif->tgl_selesai,
                            'tgl_mulai_formatted' => $magangAktif->tgl_mulai ? \Carbon\Carbon::parse($magangAktif->tgl_mulai)->format('d M Y') : 'Belum ditentukan',
                            'tgl_selesai_formatted' => $magangAktif->tgl_selesai ? \Carbon\Carbon::parse($magangAktif->tgl_selesai)->format('d M Y') : 'Belum ditentukan',
                            'progress' => round($progress),
                            'sisa_hari' => $sisaHari,
                            'total_durasi' => $totalDurasi,
                            'is_expired' => $isExpired,
                            'status_text' => $statusText,
                            'is_active' => $magangAktif->status === 'aktif', // ✅ FIX: Only true if actually active
                            'needs_evaluation' => $isExpired && $magangAktif->status === 'selesai' // ✅ ADD: Flag for evaluation
                        ];

                        Log::info('✅ Magang info loaded for lamaran page:', [
                            'id_magang' => $magangAktif->id_magang,
                            'status' => $magangAktif->status,
                            'is_expired' => $isExpired,
                            'progress' => $progress,
                            'needs_evaluation' => $magangInfo['needs_evaluation']
                        ]);
                    }

                    // 2. Get lamaran history from t_lamaran
                    $lamaranHistory = DB::table('t_lamaran')
                        ->join('m_lowongan', 't_lamaran.id_lowongan', '=', 'm_lowongan.id_lowongan')
                        ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                        ->leftJoin('m_wilayah', 'm_perusahaan.wilayah_id', '=', 'm_wilayah.wilayah_id')
                        ->select(
                            't_lamaran.id_lamaran',
                            't_lamaran.tanggal_lamaran',
                            't_lamaran.auth as status',
                            'm_lowongan.judul_lowongan',
                            'm_lowongan.deskripsi',
                            'm_perusahaan.nama_perusahaan',
                            'm_perusahaan.logo as logo_perusahaan',
                            'm_wilayah.nama_kota'
                        )
                        ->where('t_lamaran.id_mahasiswa', $mahasiswaData->id_mahasiswa)
                        ->orderBy('t_lamaran.tanggal_lamaran', 'desc')
                        ->get();

                    // Calculate statistics
                    $totalLamaran = $lamaranHistory->count();
                    $menungguCount = $lamaranHistory->where('status', 'menunggu')->count();
                    $ditolakCount = $lamaranHistory->where('status', 'ditolak')->count();
                    
                    // ✅ LOGIC BARU: Calculate statistics dengan logic khusus untuk magang aktif
                    if ($magangAktif) {
                        $diterimaCount = 1; // Pasti ada 1 yang diterima karena sedang magang
                        
                        // Filter lamaran history untuk tidak menampilkan yang diterima (karena sudah jadi magang aktif)
                        $lamaranHistory = $lamaranHistory->where('status', '!=', 'diterima');
                    } else {
                        $diterimaCount = $lamaranHistory->where('status', 'diterima')->count();
                    }

                    $statistik = [
                        'total' => $totalLamaran,
                        'menunggu' => $menungguCount,
                        'diterima' => $diterimaCount,
                        'ditolak' => $ditolakCount
                    ];
                    
                    // ✅ TAMBAHKAN FLAG UNTUK VIEW
                    $showLamaranHistory = !$magangAktif; // Hanya tampilkan riwayat jika tidak ada magang aktif
                }
            }

            // ✅ CHECK IF AJAX REQUEST (untuk full page reload)
            if ($request->ajax() || $request->wantsJson()) {
                // Log untuk debugging AJAX
                Log::info('AJAX Lamaran reload:', [
                    'user_id' => $user ? $user->id_user : null,
                    'mahasiswa_found' => $mahasiswaData ? true : false,
                    'lamaran_count' => $lamaranHistory ? $lamaranHistory->count() : 0,
                    'statistik' => $statistik ?? []
                ]);

                return response()->json([
                    'success' => true,
                    'userData' => $userData,
                    'mahasiswaData' => $mahasiswaData,
                    'magangInfo' => $magangInfo,
                    'lamaranHistory' => $lamaranHistory ? $lamaranHistory->toArray() : [],
                    'statistik' => $statistik ?? ['total' => 0, 'menunggu' => 0, 'diterima' => 0, 'ditolak' => 0],
                    'showLamaranHistory' => $showLamaranHistory ?? false, // ✅ TAMBAHKAN INI
                    'timestamp' => now()->toISOString(),
                    'reload_type' => 'full_page_ajax'
                ]);
            }

            // ✅ NORMAL PAGE REQUEST (untuk initial page load)
            return view('pages.mahasiswa.MhsLamaran', [
                'title' => 'Lamaran Magang',
                'userData' => $userData,
                'mahasiswaData' => $mahasiswaData,
                'magangInfo' => $magangInfo,
                'lamaranHistory' => $lamaranHistory,
                'statistik' => $statistik ?? ['total' => 0, 'menunggu' => 0, 'diterima' => 0, 'ditolak' => 0],
                'showLamaranHistory' => $showLamaranHistory ?? false // ✅ TAMBAHKAN INI
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading lamaran page: ' . $e->getMessage(), [
                'stack' => $e->getTraceAsString(),
                'request_type' => $request->ajax() ? 'AJAX' : 'PAGE'
            ]);

            // ✅ ERROR HANDLING FOR AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat memuat data: ' . $e->getMessage(),
                    'error_code' => 'LAMARAN_LOAD_ERROR'
                ], 500);
            }

            // ✅ ERROR HANDLING FOR PAGE
            return view('pages.mahasiswa.MhsLamaran', [
                'title' => 'Lamaran Magang',
                'userData' => null,
                'magangInfo' => null,
                'lamaranHistory' => null,
                'statistik' => ['total' => 0, 'menunggu' => 0, 'diterima' => 0, 'ditolak' => 0],
                'error' => 'Terjadi kesalahan saat memuat data'
            ]);
        }
    }
    public function evaluasi()
    {
        // Ambil data user yang sedang login
        $user = auth()->user();
        $userData = null;
        $magangInfo = null;

        if ($user) {
            $userData = DB::table('m_user')
                ->where('id_user', $user->id_user)
                ->first();

            // Ambil data mahasiswa
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $user->id_user)
                ->first();

            if ($mahasiswa) {
                // Ambil data magang aktif
                $magangAktif = DB::table('m_magang')
                    ->where('id_mahasiswa', $mahasiswa->id_mahasiswa)
                    ->where('status', 'aktif')
                    ->first();

                if ($magangAktif) {
                    $magangInfo = [
                        'data' => $magangAktif
                    ];
                }
            }
        }

        return view('pages.mahasiswa.MhsEvaluasi', [
            'title' => 'Evaluasi Mahasiswa',
            'userData' => $userData,
            'magangInfo' => $magangInfo
        ]);
    }
    public function log()
    {
        try {
            // Get user data
            $user = auth()->user();
            $userData = null;
            $mahasiswaData = null;
            $magangInfo = null;
            
            if ($user) {
                $userData = DB::table('m_user')
                    ->where('id_user', $user->id_user)
                    ->first();
                    
                $mahasiswaData = DB::table('m_mahasiswa')
                    ->where('id_user', $user->id_user)
                    ->first();
                
                if ($mahasiswaData) {
                    // Check if mahasiswa has active magang
                    $magangAktif = DB::table('m_magang')
                        ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                        ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                        ->leftJoin('m_dosen', 'm_magang.id_dosen', '=', 'm_dosen.id_dosen')
                        ->leftJoin('m_user as user_dosen', 'm_dosen.user_id', '=', 'user_dosen.id_user')
                        ->select(
                            'm_magang.*',
                            'm_lowongan.judul_lowongan',
                            'm_perusahaan.nama_perusahaan',
                            'm_perusahaan.logo as logo_perusahaan',
                            'user_dosen.name as nama_pembimbing',
                            DB::raw('NOW() as tanggal_mulai'),
                            DB::raw('DATE_ADD(NOW(), INTERVAL 3 MONTH) as tanggal_selesai')
                        )
                        ->where('m_magang.id_mahasiswa', $mahasiswaData->id_mahasiswa)
                        ->where('m_magang.status', 'aktif')
                        ->first();

                    if ($magangAktif) {
                        // Calculate progress for active magang
                        $tanggalMulai = \Carbon\Carbon::parse($magangAktif->tanggal_mulai);
                        $tanggalSelesai = \Carbon\Carbon::parse($magangAktif->tanggal_selesai);
                        $hariIni = \Carbon\Carbon::now();

                        $totalDurasi = $tanggalMulai->diffInDays($tanggalSelesai);
                        $lewat = max(0, $tanggalMulai->diffInDays($hariIni));
                        $sisaHari = max(0, $hariIni->diffInDays($tanggalSelesai));

                        $progress = $totalDurasi > 0 ? min(100, max(0, ($lewat / $totalDurasi) * 100)) : 0;

                        $magangInfo = [
                            'data' => $magangAktif,
                            'totalDurasi' => $totalDurasi,
                            'lewat' => $lewat,
                            'sisaHari' => $sisaHari,
                            'progress' => round($progress)
                        ];
                    }
                }
            }
            
            // ✅ SELALU RETURN VIEW - TIDAK ADA REDIRECT
            return view('pages.mahasiswa.MhsLog', [
                'title' => 'Log Aktivitas Magang',
                'userData' => $userData,
                'mahasiswaData' => $mahasiswaData,
                'magangInfo' => $magangInfo,
                'hasActiveMagang' => isset($magangInfo) && $magangInfo
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading log page: ' . $e->getMessage());
            return view('pages.mahasiswa.MhsLog', [
                'title' => 'Log Aktivitas Magang',
                'userData' => null,
                'mahasiswaData' => null,
                'magangInfo' => null,
                'hasActiveMagang' => false,
                'error' => 'Terjadi kesalahan saat memuat data'
            ]);
        }
    }
    /**
     * Show the lamaran (applications) view for mahasiswa
     *
     * @return \Illuminate\View\View
     */
  
}