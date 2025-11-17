<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\LogbookPhotoService;

class LogbookController extends Controller
{
    protected $photoService;

    public function __construct()
    {
        $this->photoService = new LogbookPhotoService();
    }

    /**
     * Ambil semua entri logbook untuk mahasiswa yang sedang login
     */
    public function index()
    {
        try {
            $magangInfo = $this->getMagangInfo();

            if (!$magangInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki magang aktif',
                    'code' => 'NO_ACTIVE_MAGANG'
                ], 403);
            }

            Log::info('Fetching logbook entries for magang:', [
                'id_magang' => $magangInfo->id_magang,
                'mahasiswa_id' => $magangInfo->id_mahasiswa
            ]);

            $logEntries = DB::table('t_log')
                ->join('m_magang', 't_log.id_magang', '=', 'm_magang.id_magang')
                ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
                ->where('t_log.id_magang', $magangInfo->id_magang)
                ->select(
                    't_log.*',
                    'm_perusahaan.nama_perusahaan'
                )
                ->orderBy('t_log.tanggal', 'desc')
                ->orderBy('t_log.created_at', 'desc')
                ->get();

            $groupedEntries = $this->groupLogEntriesByMonth($logEntries);

            Log::info('Logbook entries fetched successfully:', [
                'total_entries' => $logEntries->count(),
                'grouped_months' => count($groupedEntries)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data logbook',
                'data' => $groupedEntries,
                'magang_info' => [
                    'id_magang' => $magangInfo->id_magang,
                    'nama_perusahaan' => $logEntries->first()->nama_perusahaan ?? 'Unknown'
                ],
                'total_entries' => $logEntries->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching logbook entries: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data logbook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan entri logbook baru
     */
    public function store(Request $request)
    {
        try {
            Log::info('Storing new logbook entry:', $request->except(['foto']));

            $validator = Validator::make($request->all(), [
                'tanggal' => 'required|date|before_or_equal:today',
                'deskripsi' => 'required|string|min:10|max:1000',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
            ], [
                'tanggal.required' => 'Tanggal aktivitas wajib diisi',
                'tanggal.date' => 'Format tanggal tidak valid',
                'tanggal.before_or_equal' => 'Tanggal tidak boleh di masa depan',
                'deskripsi.required' => 'Deskripsi kegiatan wajib diisi',
                'deskripsi.min' => 'Deskripsi minimal 10 karakter',
                'deskripsi.max' => 'Deskripsi maksimal 1000 karakter',
                'foto.image' => 'File harus berupa gambar',
                'foto.mimes' => 'Format foto harus: JPEG, PNG, JPG, GIF, atau WebP',
                'foto.max' => 'Ukuran foto maksimal 5MB'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $magangInfo = $this->getMagangInfo();
            if (!$magangInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki magang aktif',
                    'code' => 'NO_ACTIVE_MAGANG'
                ], 403);
            }

            $existingLog = DB::table('t_log')
                ->where('id_magang', $magangInfo->id_magang)
                ->where('tanggal', $request->tanggal)
                ->first();

            if ($existingLog) {
                return response()->json([
                    'success' => false,
                    'message' => 'Logbook untuk tanggal ' . Carbon::parse($request->tanggal)->format('d M Y') . ' sudah ada',
                    'code' => 'DUPLICATE_DATE'
                ], 422);
            }

            DB::beginTransaction();

            $fotoPath = null;
            $fotoUrl = null;

            if ($request->hasFile('foto')) {
                $photoResult = $this->photoService->storePhoto(
                    $request->file('foto'),
                    $magangInfo->id_magang,
                    $request->tanggal
                );

                if (!$photoResult['success']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan foto: ' . $photoResult['message'],
                        'code' => 'PHOTO_UPLOAD_FAILED'
                    ], 400);
                }

                $fotoPath = $photoResult['file_path'];
                $fotoUrl = $photoResult['url'];

                Log::info('Photo uploaded successfully:', [
                    'file_path' => $fotoPath,
                    'file_size' => $photoResult['file_size'],
                    'url' => $fotoUrl
                ]);
            }

            $id_log = DB::table('t_log')->insertGetId([
                'id_magang' => $magangInfo->id_magang,
                'tanggal' => $request->tanggal,
                'log_aktivitas' => trim($request->deskripsi),
                'foto' => $fotoPath,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            DB::commit();

            Log::info('Logbook entry created successfully:', [
                'id_log' => $id_log,
                'id_magang' => $magangInfo->id_magang,
                'tanggal' => $request->tanggal,
                'has_foto' => !is_null($fotoPath)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aktivitas berhasil disimpan',
                'data' => [
                    'id_log' => $id_log,
                    'tanggal' => $request->tanggal,
                    'tanggal_formatted' => Carbon::parse($request->tanggal)->format('d M Y'),
                    'has_foto' => !is_null($fotoPath),
                    'foto_url' => $fotoUrl,
                    'deskripsi' => trim($request->deskripsi)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saving logbook entry: ' . $e->getMessage(), [
                'request_data' => $request->except(['foto']),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan logbook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete logbook entry
     */
    public function destroy($id)
    {
        try {
            $magangInfo = $this->getMagangInfo();

            if (!$magangInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki magang aktif'
                ], 403);
            }

            $logEntry = DB::table('t_log')
                ->where('id_log', $id)
                ->where('id_magang', $magangInfo->id_magang)
                ->first();

            if (!$logEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data logbook tidak ditemukan'
                ], 404);
            }

            DB::beginTransaction();

            // ✅ GUNAKAN: PhotoService untuk delete foto
            if ($logEntry->foto) {
                $this->photoService->deletePhoto($logEntry->foto);
            }

            DB::table('t_log')->where('id_log', $id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Logbook berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting logbook entry: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus logbook: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ PERBAIKAN: Group log entries dengan URL foto yang benar
     */
    private function groupLogEntriesByMonth($logEntries)
    {
        $grouped = [];

        foreach ($logEntries as $entry) {
            $date = Carbon::parse($entry->tanggal);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('F Y');

            if (!isset($grouped[$monthKey])) {
                $grouped[$monthKey] = [
                    'month' => $monthLabel,
                    'entries' => []
                ];
            }

            // ✅ PERBAIKAN: Generate URL foto dengan service
            $fotoUrl = null;
            $hasFoto = false;

            if ($entry->foto) {
                // ✅ CEK: Foto menggunakan service
                $photoInfo = $this->photoService->getPhotoInfo($entry->foto);
                
                if ($photoInfo['exists']) {
                    $fotoUrl = $photoInfo['url'];
                    $hasFoto = true;
                    
                    Log::debug('Photo found for entry:', [
                        'entry_id' => $entry->id_log,
                        'foto_path' => $entry->foto,
                        'foto_url' => $fotoUrl
                    ]);
                } else {
                    Log::warning('Photo file not found:', [
                        'entry_id' => $entry->id_log,
                        'foto_path' => $entry->foto,
                        'photo_info' => $photoInfo
                    ]);
                }
            }

            $grouped[$monthKey]['entries'][] = [
                'id' => $entry->id_log,
                'tanggal' => $entry->tanggal,
                'tanggal_formatted' => $date->format('d M Y'),
                'tanggal_hari' => $date->format('l'),
                'deskripsi' => $entry->log_aktivitas,
                'foto' => $fotoUrl,
                'has_foto' => $hasFoto,
                'created_at' => $entry->created_at,
                'time_ago' => Carbon::parse($entry->created_at)->diffForHumans()
            ];
        }

        krsort($grouped);
        return array_values($grouped);
    }

    /**
     * Get magang info untuk user yang login
     */
    private function getMagangInfo()
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $mahasiswa = DB::table('m_mahasiswa')
            ->where('id_user', $user->id_user)
            ->first();

        if (!$mahasiswa) {
            return null;
        }

        $magangAktif = DB::table('m_magang')
            ->join('m_lowongan', 'm_magang.id_lowongan', '=', 'm_lowongan.id_lowongan')
            ->join('m_perusahaan', 'm_lowongan.perusahaan_id', '=', 'm_perusahaan.perusahaan_id')
            ->where('m_magang.id_mahasiswa', $mahasiswa->id_mahasiswa)
            ->where('m_magang.status', 'aktif')
            ->select(
                'm_magang.*',
                'm_lowongan.judul_lowongan',
                'm_perusahaan.nama_perusahaan'
            )
            ->first();

        return $magangAktif;
    }

    public function getByMahasiswa($id_mahasiswa, Request $request)
    {
        try {
            $id_magang = $request->get('id_magang');
            
            // Log untuk debugging
            Log::info("Getting logbook for mahasiswa ID: {$id_mahasiswa}, magang ID: {$id_magang}");

            if (!$id_magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID Magang diperlukan'
                ], 400);
            }

            // Verify magang exists and belongs to the correct mahasiswa
            $magang = DB::table('m_magang')
                ->where('id_magang', $id_magang)
                ->where('id_mahasiswa', $id_mahasiswa)
                ->first();

            if (!$magang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data magang tidak ditemukan atau tidak memiliki akses'
                ], 403);
            }

            // Get logs specific to this magang
            $logbook = DB::table('t_log')
                ->where('id_magang', '=', $id_magang)
                ->select(
                    'id_log as id',
                    'tanggal',
                    'log_aktivitas as deskripsi',
                    'foto',
                    'created_at'
                )
                ->orderBy('tanggal', 'desc')
                ->get();

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
            Log::error('Error in getByMahasiswa logbook: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data logbook: ' . $e->getMessage()
            ], 500);
        }
    }
}
