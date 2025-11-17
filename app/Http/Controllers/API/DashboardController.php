<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use Illuminate\Http\Request;
use App\Models\Magang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    public function index(Request $request)
    {
        $query = Mahasiswa::with(['kelas']);

        // Filter by kelas
        if ($request->has('kelas') && !empty($request->kelas)) {
            $query->where('id_kelas', $request->kelas);
        }

        // Filter by search
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('nim', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $mahasiswa = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $mahasiswa->items(),
            'meta' => [
                'current_page' => $mahasiswa->currentPage(),
                'last_page' => $mahasiswa->lastPage(),
                'per_page' => $mahasiswa->perPage(),
                'total' => $mahasiswa->total()
            ]
        ]);
    }

    public function getSummary()
    {
        try {
            // Hitung jumlah mahasiswa aktif magang
            $mahasiswaAktif = Magang::where('status', 'aktif')->count();

            // Hitung jumlah perusahaan mitra
            $perusahaanMitra = Perusahaan::count();

            // Hitung jumlah lowongan aktif
            $lowonganAktif = Lowongan::where('id_lowongan', '!=', null)->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'mahasiswa_aktif' => $mahasiswaAktif,
                    'perusahaan_mitra' => $perusahaanMitra,
                    'lowongan_aktif' => $lowonganAktif,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data summary dashboard.'
            ], 500);
        }
    }

    public function getLatestApplications()
    {
        try {
            $applications = Lamaran::with(['mahasiswa.user', 'lowongan.perusahaan'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($lamaran) {
                    // Validasi null untuk relasi
                    $mahasiswa = $lamaran->mahasiswa;
                    $user = $mahasiswa->user ?? null;
                    $lowongan = $lamaran->lowongan ?? null;
                    $perusahaan = $lowongan->perusahaan ?? null;


                    return [
                        'id' => $lamaran->id_lamaran,
                        'nama_mahasiswa' => $user->name ?? 'Tidak Diketahui',
                        'nim' => $mahasiswa->nim ?? 'Tidak Diketahui',
                        'perusahaan' => $perusahaan->nama_perusahaan ?? 'Tidak Diketahui',
                        'status' => $lamaran->auth ?? 'Tidak Diketahui', // Ambil status terbaru
                        'tanggal' => $lamaran->tanggal_lamaran ?? $lamaran->created_at
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $applications
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving latest applications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving latest applications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getActivePeriod()
    {
        try {
            Log::info('Getting active period');
            // Get active period_id from t_periode table
            $activePeriodeRecord = DB::table('t_periode')->first();

            if (!$activePeriodeRecord) {
                Log::info('No active period found in t_periode table');
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada periode aktif'
                ]);
            }

            Log::info('Active period ID: ' . $activePeriodeRecord->periode_id);

            // Get the full period details from m_periode
            $activePeriod = \App\Models\Periode::find($activePeriodeRecord->periode_id);

            if (!$activePeriod) {
                Log::warning('Period ID exists in t_periode but not found in m_periode: ' . $activePeriodeRecord->periode_id);
                return response()->json([
                    'success' => false,
                    'message' => 'Periode aktif tidak ditemukan'
                ]);
            }

            // Log the raw data for debugging
            Log::info('Active period data:', $activePeriod->toArray());

            // If dates are not set, use default values
            if (!$activePeriod->tgl_mulai) {
                Log::info('Start date not set, using created_at');
                $activePeriod->tgl_mulai = $activePeriod->created_at->format('Y-m-d');
            }

            if (!$activePeriod->tgl_selesai) {
                Log::info('End date not set, setting to 1 year after start date');
                // Default to one year duration if not set
                $activePeriod->tgl_selesai = date('Y-m-d', strtotime('+1 year', strtotime($activePeriod->tgl_mulai)));
            }

            return response()->json([
                'success' => true,
                'data' => $activePeriod
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getActivePeriod: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ SIMPLE: Get automation status
     */
    public function getSimpleAutomationStatus()
    {
        try {
            $automationService = app(\App\Services\SimpleAutomationService::class);
            $status = $automationService->getCurrentStatus();

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching simple automation status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load automation status'
            ], 500);
        }
    }

    /**
     * ✅ SIMPLE: Manual trigger automation
     */
    public function triggerSimpleAutomation(Request $request)
    {
        try {
            if (auth()->user()->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $type = $request->get('type', 'completion'); // completion or warning
            $automationService = app(\App\Services\SimpleAutomationService::class);

            if ($type === 'completion') {
                $result = $automationService->autoCompleteExpired();
                cache()->put('last_manual_completion', now(), 86400);
            } else {
                $result = $automationService->checkExpiringMagang(3);
            }

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Automation completed successfully' : 'Automation failed',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Error triggering automation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
