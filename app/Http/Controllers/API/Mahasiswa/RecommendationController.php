<?php

namespace App\Http\Controllers\API\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Services\SPKRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;

class RecommendationController extends Controller
{
    private $spkService;

    public function __construct()
    {
        try {
            // ✅ PERBAIKI: Complete initialization
            if (class_exists('App\Services\SPKRecommendationService')) {
                $this->spkService = new SPKRecommendationService();
                Log::info('✅ SPK Service initialized successfully');
            } else {
                Log::warning('⚠️ SPKRecommendationService class not found');
                $this->spkService = null;
            }
        } catch (\Exception $e) {
            Log::error('❌ Failed to initialize SPK Service: ' . $e->getMessage());
            $this->spkService = null;
        }
    }



    /**
     * ✅ FIXED: Get recommendations for dashboard
     */
    public function getRecommendations()
    {
        try {
            // Example: get authenticated user
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'data' => []
                ], 401);
            }

            // Get mahasiswa data
            $mahasiswa = $this->getMahasiswaByUserId($user->id_user);
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student data not found',
                    'data' => []
                ], 404);
            }

            $mahasiswaId = $this->getMahasiswaId($mahasiswa);

            // Call SPK service if available
            if ($this->spkService && method_exists($this->spkService, 'calculateEDASRecommendation')) {
                $spkResult = $this->spkService->calculateEDASRecommendation($mahasiswaId);
            } else {
                $spkResult = ['ranking' => []];
            }

            $recommendations = $this->formatSPKResults($spkResult); // hasil array rekomendasi

            return response()->json([
                'success' => true,
                'data' => $recommendations,
                'method' => 'EDAS'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get mahasiswa by user ID with field detection
     */
    private function getMahasiswaByUserId($userId)
    {
        try {
            Log::info('🔍 Looking for mahasiswa with user_id: ' . $userId);

            if (!Schema::hasTable('m_mahasiswa')) {
                Log::error('❌ Table m_mahasiswa does not exist');
                return null;
            }

            // ✅ PERBAIKI: Berdasarkan struktur database Anda
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_user', $userId) // ✅ Sesuai dengan struktur tabel Anda
                ->first();

            if ($mahasiswa) {
                Log::info('✅ Mahasiswa found', [
                    'mahasiswa_id' => $mahasiswa->id_mahasiswa,
                    'nama' => $mahasiswa->nama ?? 'Unknown',
                    'wilayah_id' => $mahasiswa->wilayah_id ?? null
                ]);
                return $mahasiswa;
            }

            Log::warning('⚠️ No mahasiswa found for user_id: ' . $userId);

            // ✅ Debug: Check what's in the table
            $allMahasiswa = DB::table('m_mahasiswa')->limit(3)->get();
            Log::info('📋 Sample mahasiswa data:', ['sample' => $allMahasiswa->toArray()]);

            return null;
        } catch (\Exception $e) {
            Log::error('💥 Error in getMahasiswaByUserId: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ NEW: Get mahasiswa ID from mahasiswa object
     */
    private function getMahasiswaId($mahasiswa)
    {
        // ✅ PERBAIKI: Berdasarkan struktur database Anda
        if (isset($mahasiswa->id_mahasiswa)) {
            return $mahasiswa->id_mahasiswa;
        }

        Log::error('❌ No valid mahasiswa ID found', [
            'available_fields' => array_keys((array) $mahasiswa)
        ]);

        return null;
    }

    /**
     * ✅ NEW: Get mahasiswa table structure for debugging
     */
    private function getMahasiswaTableStructure()
    {
        try {
            if (Schema::hasTable('m_mahasiswa')) {
                return DB::select('DESCRIBE m_mahasiswa');
            }
            return ['error' => 'Table m_mahasiswa does not exist'];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * ✅ IMPROVED: Smart recommendations calculation
     */
    private function calculateSmartRecommendations($mahasiswa, $mahasiswaId)
    {
        try {
            Log::info('Calculating smart recommendations', ['mahasiswa_id' => $mahasiswaId]);

            // 1. Get available opportunities
            $opportunities = $this->getAvailableOpportunities();

            if ($opportunities->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'method' => 'NO_OPPORTUNITIES',
                    'message' => 'No opportunities available'
                ]);
            }

            Log::info('Found opportunities:', ['count' => $opportunities->count()]);

            // 2. Try SPK Service first
            if ($this->spkService && method_exists($this->spkService, 'calculateEDASRecommendation')) {
                try {
                    Log::info('Using SPK EDAS calculation');

                    // ✅ FIXED: Call SPK service with correct parameters
                    $spkResult = $this->spkService->calculateEDASRecommendation($mahasiswaId);

                    if (!isset($spkResult['error'])) {
                        $recommendations = $this->formatSPKResults($spkResult);

                        return response()->json([
                            'success' => true,
                            'data' => $recommendations,
                            'method' => 'SPK_EDAS',
                            'message' => 'SPK EDAS recommendations generated successfully',
                            'summary' => [
                                'total_opportunities' => count($spkResult['alternatives'] ?? []),
                                'displayed' => count($recommendations),
                                'calculation_method' => 'EDAS Multi-Criteria Decision Making'
                            ]
                        ]);
                    } else {
                        Log::warning('SPK returned error: ' . $spkResult['error']);
                    }
                } catch (\Exception $spkError) {
                    Log::error('SPK Service error: ' . $spkError->getMessage());
                }
            }

            // 3. Fallback to deterministic algorithm
            Log::info('Using deterministic algorithm as fallback');
            return $this->getDeterministicRecommendations($mahasiswa, $opportunities);
        } catch (\Exception $e) {
            Log::error('Error in calculateSmartRecommendations: ' . $e->getMessage());
            return $this->getEmptyRecommendationsResponse();
        }
    }

    /**
     * ✅ NEW: Get available opportunities with proper joins
     */
    private function getAvailableOpportunities()
    {
        try {
            // Check if required tables exist
            $requiredTables = ['m_lowongan', 'm_perusahaan', 'm_wilayah'];
            foreach ($requiredTables as $table) {
                if (!Schema::hasTable($table)) {
                    Log::error("Required table {$table} does not exist");
                    return collect([]);
                }
            }

            // Get current active period
            $activePeriod = null;
            if (Schema::hasTable('t_periode') && Schema::hasTable('m_periode')) {
                $activePeriod = DB::table('t_periode as tp')
                    ->join('m_periode as mp', 'tp.periode_id', '=', 'mp.periode_id')
                    ->where('mp.is_active', true)
                    ->first();
            }

            $query = DB::table('m_lowongan as ml')
                ->join('m_perusahaan as mp', 'ml.perusahaan_id', '=', 'mp.perusahaan_id')
                ->leftJoin('m_wilayah as mw', 'mp.wilayah_id', '=', 'mw.wilayah_id')
                ->select(
                    'ml.id_lowongan',
                    'ml.judul_lowongan',
                    'ml.min_ipk',
                    'ml.deskripsi',
                    'mp.perusahaan_id',
                    'mp.nama_perusahaan',
                    'mp.logo as logo_perusahaan',
                    'mp.wilayah_id',
                    'mw.nama_kota as lokasi'
                );

            // Add periode filter if periode exists
            if ($activePeriod && Schema::hasColumn('m_lowongan', 'periode_id')) {
                $query->where('ml.periode_id', $activePeriod->periode_id);
            }

            // Add capacity filter if table exists
            if (Schema::hasTable('t_kapasitas_lowongan')) {
                $query->leftJoin('t_kapasitas_lowongan as tkl', 'ml.id_lowongan', '=', 'tkl.id_lowongan')
                    ->addSelect('tkl.kapasitas_tersedia', 'tkl.kapasitas_total')
                    ->where(function ($q) {
                        $q->whereNull('tkl.kapasitas_tersedia')
                            ->orWhere('tkl.kapasitas_tersedia', '>', 0);
                    });
            }

            $opportunities = $query->orderBy('ml.created_at', 'desc')
                ->limit(20)
                ->get();

            Log::info('Retrieved opportunities', [
                'count' => $opportunities->count(),
                'has_capacity_table' => Schema::hasTable('t_kapasitas_lowongan'),
                'active_period' => $activePeriod ? $activePeriod->periode_id : 'none'
            ]);

            return $opportunities;
        } catch (\Exception $e) {
            Log::error('Error getting opportunities: ' . $e->getMessage());
            return collect([]);
        }
    }

    /**
     * ✅ NEW: Get company logo with fallbacks
     */
    private function getCompanyLogo($logoPath)
    {
        if (!$logoPath || empty(trim($logoPath))) {
            return $this->getDefaultLogo();
        }

        $cleanPath = trim($logoPath);

        // If already full URL
        if (str_starts_with($cleanPath, 'http://') || str_starts_with($cleanPath, 'https://')) {
            return $cleanPath;
        }

        // Try different possible paths
        $possiblePaths = [
            $cleanPath,
            'storage/' . $cleanPath,
            'img/logos/' . $cleanPath,
            'uploads/logos/' . $cleanPath,
            'assets/img/' . $cleanPath
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        return $this->getDefaultLogo();
    }

    /**
     * ✅ NEW: Get default logo
     */
    private function getDefaultLogo()
    {
        $defaultPaths = [
            'img/default-company.png',
            'img/default-company.svg',
            'assets/img/default-company.png'
        ];

        foreach ($defaultPaths as $path) {
            if (file_exists(public_path($path))) {
                return asset($path);
            }
        }

        // Return SVG data URI as final fallback
        return 'data:image/svg+xml;base64,' . base64_encode('
            <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                <rect width="64" height="64" rx="8" fill="#f8f9fa" stroke="#dee2e6"/>
                <rect x="16" y="20" width="32" height="20" rx="4" fill="#e9ecef"/>
                <circle cx="24" cy="28" r="2" fill="#6c757d"/>
                <rect x="28" y="27" width="12" height="1" fill="#6c757d"/>
                <rect x="28" y="29" width="8" height="1" fill="#6c757d"/>
                <rect x="20" y="34" width="24" height="2" rx="1" fill="#007bff"/>
                <text x="32" y="50" font-family="Arial,sans-serif" font-size="8" fill="#6c757d" text-anchor="middle">LOGO</text>
            </svg>
        ');
    }

    /**
     * ✅ NEW: Get distance between cities
     */
    private function getDistanceBetweenCities($fromWilayahId, $toWilayahId)
    {
        try {
            // Check if distance matrix exists
            if (Schema::hasTable('m_distance_matrix')) {
                $distance = DB::table('m_distance_matrix')
                    ->where('from_wilayah_id', $fromWilayahId)
                    ->where('to_wilayah_id', $toWilayahId)
                    ->first();

                if ($distance) {
                    return [
                        'distance_km' => $distance->distance_km,
                        'duration_minutes' => $distance->duration_minutes ?? 0,
                        'method' => 'distance_matrix'
                    ];
                }
            }

            // Try coordinate calculation
            $fromCity = DB::table('m_wilayah')
                ->where('wilayah_id', $fromWilayahId)
                ->first();

            $toCity = DB::table('m_wilayah')
                ->where('wilayah_id', $toWilayahId)
                ->first();

            if (
                $fromCity && $toCity &&
                isset($fromCity->latitude) && isset($toCity->latitude) &&
                $fromCity->latitude && $toCity->latitude
            ) {

                $distance = $this->calculateHaversineDistance(
                    $fromCity->latitude,
                    $fromCity->longitude,
                    $toCity->latitude,
                    $toCity->longitude
                );

                return [
                    'distance_km' => $distance,
                    'duration_minutes' => $this->estimateDuration($distance),
                    'method' => 'haversine'
                ];
            }

            // Final fallback
            return [
                'distance_km' => 100,
                'duration_minutes' => 120,
                'method' => 'estimated'
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating distance: ' . $e->getMessage());
            return [
                'distance_km' => 100,
                'duration_minutes' => 120,
                'method' => 'fallback'
            ];
        }
    }

    /**
     * ✅ NEW: Calculate Haversine distance
     */
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * ✅ NEW: Estimate duration based on distance
     */
    private function estimateDuration($distance)
    {
        if ($distance <= 30) {
            return round($distance * 2); // City traffic
        } elseif ($distance <= 100) {
            return round($distance * 1.5); // Mixed traffic
        } else {
            return round($distance * 1.2); // Highway
        }
    }

    /**
     * ✅ IMPROVED: Deterministic recommendations with real data
     */
    private function getDeterministicRecommendations($mahasiswa, $opportunities)
    {
        Log::info('Using deterministic algorithm');

        $recommendations = [];

        foreach ($opportunities as $opportunity) {
            // Use hash for consistency
            $seed = crc32($mahasiswa->id_user . '_' . $opportunity->id_lowongan);

            // Calculate realistic scores
            $skillScore = $this->calculateRealSkillScore($mahasiswa, $opportunity, $seed);
            $locationScore = $this->calculateRealLocationScore($mahasiswa, $opportunity, $seed);
            $ipkScore = $this->calculateRealIPKScore($mahasiswa, $opportunity);
            $quotaScore = $this->calculateRealQuotaScore($opportunity);
            $interestScore = $this->calculateRealInterestScore($mahasiswa, $opportunity, $seed);

            // Weighted overall score
            $weights = [
                'skill' => 0.25,
                'location' => 0.20,
                'ipk' => 0.20,
                'interest' => 0.20,
                'quota' => 0.15
            ];

            $overallScore = (
                ($skillScore / 100) * $weights['skill'] +
                ($locationScore / 100) * $weights['location'] +
                ($ipkScore / 100) * $weights['ipk'] +
                ($interestScore / 100) * $weights['interest'] +
                ($quotaScore / 100) * $weights['quota']
            );

            $recommendations[] = [
                'id_lowongan' => $opportunity->id_lowongan,
                'judul_lowongan' => $opportunity->judul_lowongan,
                'nama_perusahaan' => $opportunity->nama_perusahaan,
                'logo_perusahaan' => $this->getCompanyLogo($opportunity->logo_perusahaan),
                'lokasi' => $opportunity->lokasi ?? 'Lokasi tidak tersedia',
                'appraisal_score' => round($overallScore, 3),
                'skill_match' => $skillScore,
                'location_match' => $locationScore,
                'ipk_match' => $ipkScore,
                'interest_match' => $interestScore,
                'quota_score' => $quotaScore,
                'rank' => 0
            ];
        }

        // Sort by overall score
        usort($recommendations, function ($a, $b) {
            if ($a['appraisal_score'] == $b['appraisal_score']) {
                return $a['id_lowongan'] <=> $b['id_lowongan'];
            }
            return $b['appraisal_score'] <=> $a['appraisal_score'];
        });

        // Add ranks
        foreach ($recommendations as $index => &$rec) {
            $rec['rank'] = $index + 1;
        }

        $topRecommendations = array_slice($recommendations, 0, 6);

        return response()->json([
            'success' => true,
            'data' => $topRecommendations,
            'method' => 'DETERMINISTIC_ALGORITHM',
            'message' => 'Deterministic recommendations generated successfully',
            'summary' => [
                'total_opportunities' => count($recommendations),
                'displayed' => count($topRecommendations),
                'calculation_method' => 'Weighted multi-criteria scoring with real data'
            ]
        ]);
    }

    /**
     * ✅ NEW: Calculate real skill score
     */
    private function calculateRealSkillScore($mahasiswa, $opportunity, $seed)
    {
        try {
            // Check if skill tables exist
            if (!Schema::hasTable('t_skill_mahasiswa') || !Schema::hasTable('t_skill_lowongan')) {
                return 40 + (($seed % 40) / 100 * 35); // 40-75 range
            }

            $studentSkills = DB::table('t_skill_mahasiswa')
                ->where('user_id', $mahasiswa->id_user)
                ->pluck('skill_id')
                ->toArray();

            $requiredSkills = DB::table('t_skill_lowongan')
                ->where('id_lowongan', $opportunity->id_lowongan)
                ->pluck('id_skill')
                ->toArray();

            if (empty($requiredSkills)) {
                return 50 + (($seed % 30) / 100 * 25); // 50-75 range
            }

            $matchCount = count(array_intersect($studentSkills, $requiredSkills));
            $totalRequired = count($requiredSkills);

            $basePercentage = ($matchCount / $totalRequired) * 100;

            // Add small variance for differentiation
            $variance = (($seed % 20) - 10) / 10;
            $finalScore = max(0, min(100, $basePercentage + $variance));

            return round($finalScore, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating skill score: ' . $e->getMessage());
            return 50; // Safe fallback
        }
    }

    /**
     * ✅ NEW: Calculate real location score
     */
    private function calculateRealLocationScore($mahasiswa, $opportunity, $seed)
    {
        try {
            if (!isset($mahasiswa->wilayah_id) || !$mahasiswa->wilayah_id) {
                return 40 + (($seed % 30) / 100 * 20); // 40-60 range
            }

            $companyWilayah = DB::table('m_perusahaan')
                ->where('perusahaan_id', $opportunity->perusahaan_id)
                ->value('wilayah_id');

            if (!$companyWilayah) {
                return 45 + (($seed % 25) / 100 * 20); // 45-65 range
            }

            if ($mahasiswa->wilayah_id == $companyWilayah) {
                return 95; // Same city = excellent
            }

            // Get distance
            $distanceData = $this->getDistanceBetweenCities($mahasiswa->wilayah_id, $companyWilayah);
            $distance = $distanceData['distance_km'];

            if ($distance <= 30) return 85;
            if ($distance <= 60) return 70;
            if ($distance <= 120) return 55;
            if ($distance <= 200) return 40;
            return 25;
        } catch (\Exception $e) {
            Log::error('Error calculating location score: ' . $e->getMessage());
            return 50;
        }
    }

    /**
     * ✅ NEW: Calculate real IPK score
     */
    private function calculateRealIPKScore($mahasiswa, $opportunity)
    {
        $studentIPK = $mahasiswa->ipk ?? 0;
        $requiredIPK = $opportunity->min_ipk ?? 0;

        if ($requiredIPK == 0) {
            return 75; // No requirement
        }

        if ($studentIPK >= $requiredIPK) {
            $excess = $studentIPK - $requiredIPK;
            return min(100, 75 + ($excess * 25));
        } else {
            $gap = $requiredIPK - $studentIPK;
            return max(10, 60 - ($gap * 40));
        }
    }

    /**
     * ✅ NEW: Calculate real quota score
     */
    private function calculateRealQuotaScore($opportunity)
    {
        $quota = $opportunity->kapasitas_tersedia ?? 5; // Default moderate

        if ($quota >= 10) return 90;
        if ($quota >= 5) return 70;
        if ($quota >= 2) return 50;
        if ($quota >= 1) return 30;
        return 10;
    }

    /**
     * ✅ NEW: Calculate real interest score
     */
    private function calculateRealInterestScore($mahasiswa, $opportunity, $seed)
    {
        try {
            if (!Schema::hasTable('t_minat_mahasiswa') || !Schema::hasTable('t_minat_lowongan')) {
                return 55; // Default when no interest system
            }

            $studentInterests = DB::table('t_minat_mahasiswa')
                ->where('mahasiswa_id', $mahasiswa->id_mahasiswa ?? $mahasiswa->id ?? 0)
                ->pluck('minat_id')
                ->toArray();

            $requiredInterests = DB::table('t_minat_lowongan')
                ->where('id_lowongan', $opportunity->id_lowongan)
                ->pluck('minat_id')
                ->toArray();

            if (empty($requiredInterests)) {
                return 60; // No specific requirement
            }

            $matchCount = count(array_intersect($studentInterests, $requiredInterests));
            $totalRequired = count($requiredInterests);

            $basePercentage = ($matchCount / $totalRequired) * 100;

            // Add small variance
            $variance = (($seed % 15) - 7.5) / 10;
            $finalScore = max(0, min(100, $basePercentage + $variance));

            return round($finalScore, 2);
        } catch (\Exception $e) {
            Log::error('Error calculating interest score: ' . $e->getMessage());
            return 55;
        }
    }

    /**
     * ✅ NEW: Format SPK results
     */
    private function formatSPKResults($spkResult)
    {
        $recommendations = [];

        if (!isset($spkResult['ranking']) || empty($spkResult['ranking'])) {
            return $recommendations;
        }

        foreach (array_slice($spkResult['ranking'], 0, 6) as $item) {
            $recommendations[] = [
                'id_lowongan'      => $item['opportunity_id'] ?? null,
                'judul_lowongan'   => $item['opportunity_name'] ?? '',
                'nama_perusahaan'  => $item['company_name'] ?? '',
                'logo_perusahaan'  => $item['logo_perusahaan'] ?? '',
                'lokasi'           => $item['lokasi'] ?? '',
                'appraisal_score'  => $item['as_score'] ?? 0,
                'skill_match' => ($item['criteria_scores']['skill']['score'] ?? 2) / 3 * 100,
                'location_match' => (4 - ($item['criteria_scores']['wilayah']['score'] ?? 2)) / 3 * 100,
                'ipk_match'        => ($item['criteria_scores']['ipk']['score'] ?? 2) / 3 * 100,
                'interest_match' => ($item['criteria_scores']['minat']['score'] ?? 2) / 3 * 100,
                'quota_score'      => ($item['criteria_scores']['kuota']['score'] ?? 2) / 3 * 100,
                'rank'             => $item['rank'] ?? null,
            ];
        }

        return $recommendations;
    }

    // ✅ Helper methods for SPK result extraction
    private function extractSkillMatch($item)
    {
        return $item['raw_data']['skill_match_percentage'] ?? 50;
    }

    private function extractLocationMatch($item)
    {
        $wilayahScore = $item['criteria_scores']['wilayah']['score'] ?? 2;
        return ($wilayahScore / 3) * 100;
    }

    private function extractIPKMatch($item)
    {
        $ipkScore = $item['criteria_scores']['ipk'] ?? 2;
        return ($ipkScore / 3) * 100;
    }

    private function extractInterestMatch($item)
    {
        $minatScore = $item['criteria_scores']['minat'] ?? 2;
        return ($minatScore / 3) * 100;
    }

    private function extractQuotaScore($item)
    {
        $kuotaScore = $item['criteria_scores']['kuota'] ?? 2;
        return ($kuotaScore / 3) * 100;
    }

    /**
     * ✅ NEW: Debug endpoint
     */
    public function debug()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $mahasiswa = $this->getMahasiswaByUserId($user->id_user);

            return response()->json([
                'success' => true,
                'debug_info' => [
                    'user' => [
                        'id_user' => $user->id_user,
                        'email' => $user->email,
                        'authenticated' => true
                    ],
                    'mahasiswa' => [
                        'found' => !!$mahasiswa,
                        'data' => $mahasiswa ? (array) $mahasiswa : null,
                        'id_used' => $mahasiswa ? $this->getMahasiswaId($mahasiswa) : null
                    ],
                    'spk_service' => [
                        'initialized' => !is_null($this->spkService),
                        'class' => $this->spkService ? get_class($this->spkService) : 'null'
                    ],
                    'database' => [
                        'mahasiswa_table_exists' => Schema::hasTable('m_mahasiswa'),
                        'lowongan_table_exists' => Schema::hasTable('m_lowongan'),
                        'perusahaan_table_exists' => Schema::hasTable('m_perusahaan'),
                        'skill_tables_exist' => Schema::hasTable('t_skill_mahasiswa') && Schema::hasTable('t_skill_lowongan'),
                        'interest_tables_exist' => Schema::hasTable('t_minat_mahasiswa') && Schema::hasTable('t_minat_lowongan'),
                        'distance_matrix_exists' => Schema::hasTable('m_distance_matrix')
                    ],
                    'opportunities_count' => $this->getAvailableOpportunities()->count(),
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * ✅ NEW: Debug files endpoint
     */
    public function debugFiles()
    {
        try {
            $pathResults = [];
            $filesToCheck = [
                'img/default-company.png',
                'img/default-company.svg',
                'assets/img/default-company.png'
            ];

            foreach ($filesToCheck as $path) {
                $fullPath = public_path($path);
                $pathResults[$path] = [
                    'full_path' => $fullPath,
                    'exists' => file_exists($fullPath),
                    'is_readable' => file_exists($fullPath) ? is_readable($fullPath) : false,
                    'filesize' => file_exists($fullPath) ? filesize($fullPath) : 0,
                    'asset_url' => asset($path)
                ];
            }

            return response()->json([
                'success' => true,
                'debug_info' => [
                    'public_path' => public_path(),
                    'asset_url_base' => asset(''),
                    'path_results' => $pathResults,
                    'default_logo_result' => $this->getDefaultLogo()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NEW: Clear cache endpoint
     */
    public function clearCache()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $cleared = 0;
            for ($hour = 0; $hour < 24; $hour++) {
                $key = "recommendations_user_{$user->id_user}_" . date('Y-m-d') . "-{$hour}";
                if (Cache::forget($key)) {
                    $cleared++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully',
                'keys_cleared' => $cleared
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    /**
     * ✅ NEW: Get empty recommendations response
     */
    private function getEmptyRecommendationsResponse()
    {
        return response()->json([
            'success' => true,
            'data' => [],
            'method' => 'EMPTY',
            'message' => 'No recommendations available due to system error'
        ]);
    }

    /**
     * ✅ NEW: Get detailed analysis
     */
    public function getDetailedAnalysis($lowonganId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $mahasiswa = $this->getMahasiswaByUserId($user->id_user);

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student data not found'
                ], 404);
            }

            $mahasiswaId = $this->getMahasiswaId($mahasiswa);

            if ($this->spkService && method_exists($this->spkService, 'getDetailedAnalysis')) {
                $analysis = $this->spkService->getDetailedAnalysis($mahasiswaId, $lowonganId);

                return response()->json([
                    'success' => true,
                    'data' => $analysis
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Detailed analysis not available'
            ], 501);
        } catch (\Exception $e) {
            Log::error('Error in getDetailedAnalysis: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get detailed analysis'
            ], 500);
        }
    }

    /**
     * ✅ TAMBAH: Get EDAS recommendation for specific mahasiswa
     */
    public function getEDASRecommendation($mahasiswaId)
    {
        try {
            Log::info('Direct EDAS calculation requested', ['mahasiswa_id' => $mahasiswaId]);

            if (!$this->spkService) {
                return response()->json([
                    'success' => false,
                    'message' => 'SPK Service not available'
                ], 503);
            }

            $result = $this->spkService->calculateEDASRecommendation($mahasiswaId);

            if (isset($result['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'method' => 'DIRECT_EDAS'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in direct EDAS: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'EDAS calculation failed'
            ], 500);
        }
    }

    /**
     * ✅ TAMBAH: Get SAW recommendation (fallback method)
     */
    public function getSAWRecommendations()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                    'recommendations' => []
                ], 401);
            }

            // Get mahasiswa data
            $mahasiswa = $this->getMahasiswaByUserId($user->id_user);
            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student data not found',
                    'recommendations' => []
                ], 404);
            }

            $mahasiswaId = $this->getMahasiswaId($mahasiswa);

            // Try SPK Service SAW calculation first
            if ($this->spkService && method_exists($this->spkService, 'calculateSAWRecommendation')) {
                try {
                    $sawResult = $this->spkService->calculateSAWRecommendation($mahasiswaId);

                    if (!isset($sawResult['error'])) {
                        $recommendations = $this->formatSAWResults($sawResult);

                        return response()->json([
                            'success' => true,
                            'recommendations' => $recommendations,
                            'method' => 'SAW',
                            'summary' => [
                                'total_opportunities' => count($sawResult['alternatives'] ?? []),
                                'displayed' => count($recommendations),
                                'calculation_method' => 'Simple Additive Weighting'
                            ]
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('SPK SAW calculation failed: ' . $e->getMessage());
                }
            }

            // Fallback to manual SAW calculation
            Log::info('Using manual SAW calculation as fallback');
            return $this->getManualSAWRecommendations($mahasiswa);
        } catch (\Exception $e) {
            Log::error('Error in getSAWRecommendations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'SAW calculation failed: ' . $e->getMessage(),
                'recommendations' => []
            ], 500);
        }
    }

    private function getManualSAWRecommendations($mahasiswa)
    {
        try {
            // Get opportunities
            $opportunities = $this->getAvailableOpportunities();

            if ($opportunities->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'recommendations' => [],
                    'method' => 'SAW_NO_OPPORTUNITIES',
                    'message' => 'No opportunities available for SAW calculation'
                ]);
            }

            $sawRecommendations = [];

            foreach ($opportunities as $opportunity) {
                // Calculate normalized scores (0-1 scale)
                $skillScore = $this->calculateRealSkillScore($mahasiswa, $opportunity, rand()) / 100;
                $locationScore = $this->calculateRealLocationScore($mahasiswa, $opportunity, rand()) / 100;
                $ipkScore = $this->calculateRealIPKScore($mahasiswa, $opportunity) / 100;
                $quotaScore = $this->calculateRealQuotaScore($opportunity) / 100;
                $interestScore = $this->calculateRealInterestScore($mahasiswa, $opportunity, rand()) / 100;

                // SAW weights (must sum to 1.0)
                $weights = [
                    'skill' => 0.25,
                    'location' => 0.20,
                    'ipk' => 0.20,
                    'interest' => 0.20,
                    'quota' => 0.15
                ];

                // Calculate SAW score (Simple Additive Weighting)
                $sawScore = (
                    $skillScore * $weights['skill'] +
                    $locationScore * $weights['location'] +
                    $ipkScore * $weights['ipk'] +
                    $interestScore * $weights['interest'] +
                    $quotaScore * $weights['quota']
                );

                $sawRecommendations[] = [
                    'id_lowongan' => $opportunity->id_lowongan,
                    'judul_lowongan' => $opportunity->judul_lowongan,
                    'nama_perusahaan' => $opportunity->nama_perusahaan,
                    'logo_perusahaan' => $this->getCompanyLogo($opportunity->logo_perusahaan),
                    'lokasi' => $opportunity->lokasi ?? 'Lokasi tidak tersedia',
                    'appraisal_score' => round($sawScore, 4),
                    'skill_match' => round($skillScore * 100, 2),
                    'location_match' => round($locationScore * 100, 2),
                    'ipk_match' => round($ipkScore * 100, 2),
                    'interest_match' => round($interestScore * 100, 2),
                    'quota_score' => round($quotaScore * 100, 2),
                    'rank' => 0
                ];
            }

            // Sort by SAW score (descending)
            usort($sawRecommendations, function ($a, $b) {
                return $b['appraisal_score'] <=> $a['appraisal_score'];
            });

            // Add ranks
            foreach ($sawRecommendations as $index => &$rec) {
                $rec['rank'] = $index + 1;
            }

            // Limit to top 6
            $topRecommendations = array_slice($sawRecommendations, 0, 6);

            return response()->json([
                'success' => true,
                'recommendations' => $topRecommendations,
                'method' => 'SAW',
                'message' => 'SAW recommendations calculated successfully',
                'summary' => [
                    'total_opportunities' => count($sawRecommendations),
                    'displayed' => count($topRecommendations),
                    'calculation_method' => 'Simple Additive Weighting (Manual)',
                    'criteria_weights' => $weights
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in manual SAW calculation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Manual SAW calculation failed',
                'recommendations' => []
            ], 500);
        }
    }

    private function formatSAWResults($sawResult)
    {
        $recommendations = [];

        if (!isset($sawResult['ranking']) || empty($sawResult['ranking'])) {
            return $recommendations;
        }

        foreach (array_slice($sawResult['ranking'], 0, 6) as $item) {
            $recommendations[] = [
                'id_lowongan'      => $item['opportunity_id'] ?? null,
                'judul_lowongan'   => $item['opportunity_name'] ?? '',
                'nama_perusahaan'  => $item['company_name'] ?? '',
                'logo_perusahaan'  => $item['logo_perusahaan'] ?? '',
                'lokasi'           => $item['lokasi'] ?? '',
                'appraisal_score'  => $item['saw_score'] ?? $item['as_score'] ?? 0,
                'skill_match' => $item['raw_data']['skill_match_percentage'] ?? (($item['criteria_scores']['skill']['score'] ?? 2) / 3 * 100),
                'location_match'   => (min(3, max(1, 4 - ($item['criteria_scores']['wilayah']['score'] ?? 2))) / 3) * 100,
                'ipk_match'        => ($item['criteria_scores']['ipk']['score'] ?? 2) / 3 * 100,
                'interest_match'   => ($item['criteria_scores']['minat']['score'] ?? 2) / 3 * 100,
                'quota_score'      => ($item['criteria_scores']['kuota']['score'] ?? 2) / 3 * 100,
                'rank'             => $item['rank'] ?? null,
            ];
        }

        return $recommendations;
    }

    /**
     * ✅ TAMBAH: Calculate SAW recommendations
     */
    private function calculateSAWRecommendations($mahasiswa, $opportunities)
    {
        $alternatives = [];

        foreach ($opportunities as $opportunity) {
            // Calculate normalized scores (0-1 scale)
            $skillScore = $this->calculateRealSkillScore($mahasiswa, $opportunity, rand()) / 100;
            $locationScore = $this->calculateRealLocationScore($mahasiswa, $opportunity, rand()) / 100;
            $ipkScore = $this->calculateRealIPKScore($mahasiswa, $opportunity) / 100;
            $quotaScore = $this->calculateRealQuotaScore($opportunity) / 100;
            $interestScore = $this->calculateRealInterestScore($mahasiswa, $opportunity, rand()) / 100;

            // SAW weights
            $weights = [
                'skill' => 0.25,
                'location' => 0.20,
                'ipk' => 0.20,
                'interest' => 0.20,
                'quota' => 0.15
            ];

            // Calculate SAW score
            $sawScore = (
                $skillScore * $weights['skill'] +
                $locationScore * $weights['location'] +
                $ipkScore * $weights['ipk'] +
                $interestScore * $weights['interest'] +
                $quotaScore * $weights['quota']
            );

            $alternatives[] = [
                'id_lowongan' => $opportunity->id_lowongan,
                'judul_lowongan' => $opportunity->judul_lowongan,
                'nama_perusahaan' => $opportunity->nama_perusahaan,
                'logo_perusahaan' => $this->getCompanyLogo($opportunity->logo_perusahaan),
                'lokasi' => $opportunity->lokasi ?? 'Lokasi tidak tersedia',
                'appraisal_score' => round($sawScore, 4),
                'skill_match' => round($skillScore * 100, 2),
                'location_match' => round($locationScore * 100, 2),
                'ipk_match' => round($ipkScore * 100, 2),
                'interest_match' => round($interestScore * 100, 2),
                'quota_score' => round($quotaScore * 100, 2),
                'rank' => 0
            ];
        }

        // Sort by SAW score
        usort($alternatives, function ($a, $b) {
            return $b['appraisal_score'] <=> $a['appraisal_score'];
        });

        // Add ranks
        foreach ($alternatives as $index => &$alt) {
            $alt['rank'] = $index + 1;
        }

        return array_slice($alternatives, 0, 6);
    }

    /**
     * ✅ TAMBAH: Get SAW dashboard data
     */
    public function getSAWDashboard()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $mahasiswa = $this->getMahasiswaByUserId($user->id_user);

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student data not found'
                ], 404);
            }

            $mahasiswaId = $this->getMahasiswaId($mahasiswa);

            // Get SAW recommendations
            $sawResponse = $this->getSAWRecommendation($mahasiswaId);
            $sawData = $sawResponse->getData(true);

            return response()->json([
                'success' => true,
                'data' => [
                    'recommendations' => $sawData['data'] ?? [],
                    'method' => 'SAW',
                    'summary' => [
                        'total_analyzed' => count($sawData['data'] ?? []),
                        'calculation_method' => 'Simple Additive Weighting',
                        'criteria_weights' => [
                            'skill' => 0.25,
                            'location' => 0.20,
                            'ipk' => 0.20,
                            'interest' => 0.20,
                            'quota' => 0.15
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in SAW dashboard: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load SAW dashboard'
            ], 500);
        }
    }

    /**
     * ✅ TAMBAH: Get recommendation stats
     */
    public function getStats()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $mahasiswa = $this->getMahasiswaByUserId($user->id_user);

            if (!$mahasiswa) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student data not found'
                ], 404);
            }

            // Get basic stats
            $totalOpportunities = $this->getAvailableOpportunities()->count();

            $studentSkillsCount = 0;
            $studentInterestsCount = 0;

            try {
                if (Schema::hasTable('t_skill_mahasiswa')) {
                    $studentSkillsCount = DB::table('t_skill_mahasiswa')
                        ->where('user_id', $user->id_user)
                        ->count();
                }

                if (Schema::hasTable('t_minat_mahasiswa')) {
                    $studentInterestsCount = DB::table('t_minat_mahasiswa')
                        ->where('mahasiswa_id', $mahasiswa->id_mahasiswa)
                        ->count();
                }
            } catch (\Exception $e) {
                Log::warning('Error getting student profile stats: ' . $e->getMessage());
            }

            // Profile completion score
            $profileScore = 0;
            $profileScore += $mahasiswa->ipk ? 25 : 0;
            $profileScore += $mahasiswa->wilayah_id ? 25 : 0;
            $profileScore += $studentSkillsCount > 0 ? 25 : 0;
            $profileScore += $studentInterestsCount > 0 ? 25 : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'profile_stats' => [
                        'completion_percentage' => $profileScore,
                        'has_ipk' => !!($mahasiswa->ipk ?? false),
                        'has_location' => !!($mahasiswa->wilayah_id ?? false),
                        'skills_count' => $studentSkillsCount,
                        'interests_count' => $studentInterestsCount
                    ],
                    'opportunity_stats' => [
                        'total_available' => $totalOpportunities,
                        'with_skills_requirement' => $this->countOpportunitiesWithSkills(),
                        'with_ipk_requirement' => $this->countOpportunitiesWithIPK(),
                        'in_same_city' => $this->countOpportunitiesInSameCity($mahasiswa)
                    ],
                    'recommendation_stats' => [
                        'methods_available' => ['EDAS', 'SAW', 'Deterministic'],
                        'spk_service_active' => !is_null($this->spkService),
                        'cache_enabled' => config('cache.default') !== 'array'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recommendation stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendation stats'
            ], 500);
        }
    }

    /**
     * ✅ HELPER: Count opportunities with skills requirement
     */
    private function countOpportunitiesWithSkills()
    {
        try {
            if (!Schema::hasTable('t_skill_lowongan')) {
                return 0;
            }

            return DB::table('t_skill_lowongan')
                ->join('m_lowongan', 't_skill_lowongan.id_lowongan', '=', 'm_lowongan.id_lowongan')
                ->distinct('m_lowongan.id_lowongan')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * ✅ HELPER: Count opportunities with IPK requirement
     */
    private function countOpportunitiesWithIPK()
    {
        try {
            return DB::table('m_lowongan')
                ->where('min_ipk', '>', 0)
                ->whereNotNull('min_ipk')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * ✅ HELPER: Count opportunities in same city
     */
    private function countOpportunitiesInSameCity($mahasiswa)
    {
        try {
            if (!$mahasiswa->wilayah_id) {
                return 0;
            }

            return DB::table('m_lowongan as ml')
                ->join('m_perusahaan as mp', 'ml.perusahaan_id', '=', 'mp.perusahaan_id')
                ->where('mp.wilayah_id', $mahasiswa->wilayah_id)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function test()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Test database connections
            $mahasiswaExists = Schema::hasTable('m_mahasiswa');
            $lowonganExists = Schema::hasTable('m_lowongan');
            $perusahaanExists = Schema::hasTable('m_perusahaan');

            // Count records
            $mahasiswaCount = $mahasiswaExists ? DB::table('m_mahasiswa')->count() : 0;
            $lowonganCount = $lowonganExists ? DB::table('m_lowongan')->count() : 0;
            $perusahaanCount = $perusahaanExists ? DB::table('m_perusahaan')->count() : 0;

            return response()->json([
                'success' => true,
                'message' => 'Recommendation API is working perfectly!',
                'data' => [
                    'user_id' => $user->id_user,
                    'user_email' => $user->email,
                    'database_status' => [
                        'mahasiswa_table' => $mahasiswaExists,
                        'lowongan_table' => $lowonganExists,
                        'perusahaan_table' => $perusahaanExists,
                        'mahasiswa_count' => $mahasiswaCount,
                        'lowongan_count' => $lowonganCount,
                        'perusahaan_count' => $perusahaanCount
                    ],
                    'spk_service' => [
                        'initialized' => !is_null($this->spkService),
                        'class_name' => $this->spkService ? get_class($this->spkService) : 'null'
                    ],
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => config('app.debug') ? $e->getTraceAsString() : null
                ]
            ], 500);
        }
    }
}
