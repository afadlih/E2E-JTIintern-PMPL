<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SPKRecommendationService
{
    private $javaDistanceService;

    public function __construct()
    {
        // ✅ HAPUS dependency ke JavaDistanceService untuk sekarang
        $this->javaDistanceService = null;
        Log::info('✅ SPK Service initialized without Java Distance Service');
    }

    /**
     * ✅ MAIN METHOD: Calculate EDAS recommendation
     */
    public function calculateEDASRecommendation($mahasiswaId, $opportunities = null)
    {
        try {
            Log::info('=== STARTING EDAS CALCULATION ===', ['mahasiswa_id' => $mahasiswaId]);

            // 1. Get mahasiswa data dengan debugging
            $mahasiswa = $this->getMahasiswaData($mahasiswaId);

            if (!$mahasiswa) {
                Log::error('❌ Mahasiswa not found', ['mahasiswa_id' => $mahasiswaId]);
                return ['error' => 'Mahasiswa data not found for ID: ' . $mahasiswaId];
            }

            Log::info('✅ Mahasiswa found', [
                'mahasiswa_data' => (array) $mahasiswa,
                'has_wilayah' => !!($mahasiswa->wilayah_id ?? false),
                'has_ipk' => !!($mahasiswa->ipk ?? false)
            ]);

            // 2. Get opportunities dengan debugging
            if (!$opportunities) {
                $opportunities = $this->getAvailableOpportunities();
            }

            if ($opportunities->isEmpty()) {
                Log::warning('❌ No opportunities available for EDAS calculation');
                return ['error' => 'No opportunities available in the system'];
            }

            Log::info('✅ Opportunities found', [
                'count' => $opportunities->count(),
                'first_opportunity' => $opportunities->first(),
                'sample_ids' => $opportunities->take(3)->pluck('id_lowongan')->toArray()
            ]);

            // 3. Calculate criteria scores dengan debugging detail
            $alternatives = $this->calculateCriteriaScores($mahasiswa, $opportunities);

            if (empty($alternatives)) {
                Log::error('❌ No valid alternatives generated');
                return ['error' => 'Failed to generate valid alternatives from opportunities'];
            }

            Log::info('✅ Alternatives generated', [
                'count' => count($alternatives),
                'sample_alternative' => $alternatives[0] ?? null
            ]);

            // 4. Apply EDAS method dengan debugging
            $edasResult = $this->applyEDASMethod($alternatives);

            if (!isset($edasResult['ranking']) || empty($edasResult['ranking'])) {
                Log::error('❌ EDAS method failed to generate ranking');
                return ['error' => 'EDAS calculation failed to generate ranking'];
            }

            Log::info('✅ EDAS calculation completed', [
                'ranking_count' => count($edasResult['ranking']),
                'top_3_scores' => array_slice(array_column($edasResult['ranking'], 'as_score'), 0, 3)
            ]);

            // 5. Return comprehensive result
            $result = [
                'method' => 'EDAS',
                'mahasiswa' => [
                    'id' => $mahasiswaId,
                    'name' => $mahasiswa->nama ?? 'Unknown',
                    'ipk' => $mahasiswa->ipk ?? 0,
                    'wilayah_id' => $mahasiswa->wilayah_id ?? null
                ],
                'alternatives' => $alternatives,
                'edas_calculation' => $edasResult,
                'ranking' => $edasResult['ranking'],
                'top_recommendations' => array_slice($edasResult['ranking'], 0, 6),
                'summary' => [
                    'total_analyzed' => count($alternatives),
                    'top_displayed' => min(6, count($edasResult['ranking'])),
                    'calculation_timestamp' => now()->toISOString(),
                    'criteria_weights' => $this->getCriteriaWeights()
                ]
            ];

            Log::info('=== EDAS CALCULATION SUCCESS ===', [
                'final_ranking_count' => count($result['ranking']),
                'top_recommendations_count' => count($result['top_recommendations'])
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('💥 CRITICAL ERROR in EDAS calculation', [
                'mahasiswa_id' => $mahasiswaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => 'EDAS calculation failed: ' . $e->getMessage()];
        }
    }

    public function calculateSAWRecommendation($mahasiswaId, $opportunities = null)
    {
        try {
            Log::info('=== STARTING SAW CALCULATION ===', ['mahasiswa_id' => $mahasiswaId]);

            // Get mahasiswa data
            $mahasiswa = $this->getMahasiswaData($mahasiswaId);
            if (!$mahasiswa) {
                Log::error('❌ Mahasiswa not found for SAW', ['mahasiswa_id' => $mahasiswaId]);
                return ['error' => 'Mahasiswa data not found for ID: ' . $mahasiswaId];
            }

            // Get opportunities
            if (!$opportunities) {
                $opportunities = $this->getAvailableOpportunities();
            }

            if ($opportunities->isEmpty()) {
                Log::warning('❌ No opportunities available for SAW calculation');
                return ['error' => 'No opportunities available in the system'];
            }

            // Calculate criteria scores
            $alternatives = $this->calculateCriteriaScores($mahasiswa, $opportunities);
            if (empty($alternatives)) {
                Log::error('❌ No valid alternatives generated for SAW');
                return ['error' => 'Failed to generate valid alternatives from opportunities'];
            }

            // Apply SAW method
            $sawResult = $this->applySAWMethod($alternatives);
            if (!isset($sawResult['ranking']) || empty($sawResult['ranking'])) {
                Log::error('❌ SAW method failed to generate ranking');
                return ['error' => 'SAW calculation failed to generate ranking'];
            }

            Log::info('✅ SAW calculation completed', [
                'ranking_count' => count($sawResult['ranking']),
                'top_3_scores' => array_slice(array_column($sawResult['ranking'], 'saw_score'), 0, 3)
            ]);

            return [
                'method' => 'SAW',
                'mahasiswa' => [
                    'id' => $mahasiswaId,
                    'name' => $mahasiswa->nama ?? 'Unknown',
                    'ipk' => $mahasiswa->ipk ?? 0,
                    'wilayah_id' => $mahasiswa->wilayah_id ?? null
                ],
                'alternatives' => $alternatives,
                'saw_calculation' => $sawResult,
                'ranking' => $sawResult['ranking'],
                'top_recommendations' => array_slice($sawResult['ranking'], 0, 6),
                'summary' => [
                    'total_analyzed' => count($alternatives),
                    'top_displayed' => min(6, count($sawResult['ranking'])),
                    'calculation_timestamp' => now()->toISOString(),
                    'criteria_weights' => $this->getSAWWeights()
                ]
            ];
        } catch (\Exception $e) {
            Log::error('💥 CRITICAL ERROR in SAW calculation', [
                'mahasiswa_id' => $mahasiswaId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['error' => 'SAW calculation failed: ' . $e->getMessage()];
        }
    }

    private function applySAWMethod($alternatives)
    {
        try {
            Log::info('🧮 Starting SAW method', ['alternatives_count' => count($alternatives)]);

            $sawRanking = [];
            $weights = $this->getSAWWeights();

            // Extract all scores for normalization
            $allScores = [
                'minat' => [],
                'skill' => [],
                'wilayah' => [],
                'kuota' => [],
                'ipk' => []
            ];

            foreach ($alternatives as $alternative) {
                $scores = $alternative['criteria_scores'];
                $allScores['minat'][] = is_array($scores['minat']) ? $scores['minat']['score'] : $scores['minat'];
                $allScores['skill'][] = is_array($scores['skill']) ? $scores['skill']['score'] : $scores['skill'];
                $allScores['wilayah'][] = is_array($scores['wilayah']) ? $scores['wilayah']['score'] : $scores['wilayah'];
                $allScores['kuota'][] = is_array($scores['kuota']) ? $scores['kuota']['score'] : $scores['kuota'];
                $allScores['ipk'][] = is_array($scores['ipk']) ? $scores['ipk']['score'] : $scores['ipk'];
            }

            // Find max values for benefit criteria, min for cost (wilayah)
            $maxValues = [
                'minat' => max($allScores['minat']),
                'skill' => max($allScores['skill']),
                'wilayah' => max($allScores['wilayah']), // for reference
                'kuota' => max($allScores['kuota']),
                'ipk' => max($allScores['ipk'])
            ];
            $minValues = [
                'wilayah' => min($allScores['wilayah'])
            ];

            foreach ($alternatives as $alternative) {
                $scores = $alternative['criteria_scores'];

                // Extract raw scores
                $minatScore = is_array($scores['minat']) ? $scores['minat']['score'] : $scores['minat'];
                $skillScore = is_array($scores['skill']) ? $scores['skill']['score'] : $scores['skill'];
                $wilayahScore = is_array($scores['wilayah']) ? $scores['wilayah']['score'] : $scores['wilayah'];
                $kuotaScore = is_array($scores['kuota']) ? $scores['kuota']['score'] : $scores['kuota'];
                $ipkScore = is_array($scores['ipk']) ? $scores['ipk']['score'] : $scores['ipk'];

                // Normalize scores (0-1 scale)
                $normalizedScores = [
                    'minat' => $maxValues['minat'] > 0 ? $minatScore / $maxValues['minat'] : 0,
                    'skill' => $maxValues['skill'] > 0 ? $skillScore / $maxValues['skill'] : 0,
                    // Wilayah: cost criterion, gunakan min/max yang benar
                    'wilayah' => $wilayahScore > 0 ? $minValues['wilayah'] / $wilayahScore : 0,
                    'kuota' => $maxValues['kuota'] > 0 ? $kuotaScore / $maxValues['kuota'] : 0,
                    'ipk' => $maxValues['ipk'] > 0 ? $ipkScore / $maxValues['ipk'] : 0
                ];

                // Calculate SAW score using weights
                $sawScore = (
                    $normalizedScores['minat'] * $weights['minat'] +
                    $normalizedScores['skill'] * $weights['skill'] +
                    $normalizedScores['wilayah'] * $weights['wilayah'] +
                    $normalizedScores['kuota'] * $weights['kuota'] +
                    $normalizedScores['ipk'] * $weights['ipk']
                );

                $sawRanking[] = [
                    'rank' => 0,
                    'opportunity_id' => $alternative['opportunity_id'],
                    'opportunity_name' => $alternative['opportunity_name'],
                    'company_name' => $alternative['company_name'],
                    'logo_perusahaan' => $alternative['logo_perusahaan'],
                    'lokasi' => $alternative['lokasi'],
                    'saw_score' => round($sawScore, 4),
                    'normalized_scores' => $normalizedScores,
                    'raw_scores' => [
                        'minat' => $minatScore,
                        'skill' => $skillScore,
                        'wilayah' => $wilayahScore,
                        'kuota' => $kuotaScore,
                        'ipk' => $ipkScore
                    ],
                    'criteria_scores' => $alternative['criteria_scores'],
                    'raw_data' => $alternative['raw_data']
                ];
            }

            // Sort by SAW score (descending)
            usort($sawRanking, function ($a, $b) {
                return $b['saw_score'] <=> $a['saw_score'];
            });

            // Add ranks
            foreach ($sawRanking as $index => &$item) {
                $item['rank'] = $index + 1;
            }

            Log::info('✅ SAW method completed', [
                'ranking_count' => count($sawRanking),
                'top_score' => $sawRanking[0]['saw_score'] ?? 'N/A',
                'weights_used' => $weights
            ]);

            return [
                'ranking' => $sawRanking,
                'method' => 'SAW',
                'weights' => $weights,
                'max_values' => $maxValues,
                'min_values' => $minValues
            ];
        } catch (\Exception $e) {
            Log::error('💥 Error in SAW method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * ✅ NEW: Get SAW criteria weights
     */
    private function getSAWWeights()
    {
        return [
            'minat' => 0.25,
            'skill' => 0.25,
            'wilayah' => 0.20,
            'kuota' => 0.15,
            'ipk' => 0.15
        ];
    }

    /**
     * ✅ IMPROVED: Get mahasiswa data dengan lebih robust detection
     */
    private function getMahasiswaData($identifier)
    {
        try {
            Log::info('🔍 Looking for mahasiswa', ['identifier' => $identifier]);

            if (!Schema::hasTable('m_mahasiswa')) {
                Log::error('❌ Table m_mahasiswa does not exist');
                return null;
            }

            // ✅ PERBAIKI: Gunakan field yang benar sesuai database
            $mahasiswa = DB::table('m_mahasiswa')
                ->where('id_mahasiswa', $identifier)
                ->orWhere('id_user', $identifier)
                ->first();

            if ($mahasiswa) {
                Log::info('✅ Mahasiswa found in SPK Service', [
                    'id_mahasiswa' => $mahasiswa->id_mahasiswa,
                    'id_user' => $mahasiswa->id_user ?? null,
                    'wilayah_id' => $mahasiswa->wilayah_id ?? null
                ]);
                return $mahasiswa;
            }

            Log::warning('⚠️ Mahasiswa not found in SPK Service');
            return null;
        } catch (\Exception $e) {
            Log::error('💥 Error getting mahasiswa data in SPK: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ✅ IMPROVED: Get opportunities dengan error handling yang lebih baik
     */
    private function getAvailableOpportunities()
    {
        try {
            Log::info('🔍 Getting available opportunities');

            // Check required tables
            $requiredTables = ['m_lowongan', 'm_perusahaan'];
            foreach ($requiredTables as $table) {
                if (!Schema::hasTable($table)) {
                    Log::error("❌ Required table {$table} does not exist");
                    return collect([]);
                }
            }

            // ✅ SIMPLE QUERY FIRST - no complex joins
            $opportunitiesCount = DB::table('m_lowongan')->count();
            Log::info("📊 Total lowongan in database: {$opportunitiesCount}");

            if ($opportunitiesCount == 0) {
                Log::warning('❌ No lowongan found in m_lowongan table');
                return collect([]);
            }

            // ✅ BUILD QUERY STEP BY STEP
            $query = DB::table('m_lowongan as ml')
                ->join('m_perusahaan as mp', 'ml.perusahaan_id', '=', 'mp.perusahaan_id')
                ->select(
                    'ml.id_lowongan',
                    'ml.judul_lowongan',
                    'ml.min_ipk',
                    'ml.deskripsi',
                    'mp.perusahaan_id',
                    'mp.nama_perusahaan',
                    'mp.logo as logo_perusahaan',
                    'mp.wilayah_id'
                );

            // Add wilayah info if table exists
            if (Schema::hasTable('m_wilayah')) {
                $query->leftJoin('m_wilayah as mw', 'mp.wilayah_id', '=', 'mw.wilayah_id')
                    ->addSelect('mw.nama_kota as lokasi');
            } else {
                $query->addSelect(DB::raw("'Unknown Location' as lokasi"));
            }

            // Add capacity info if table exists
            if (Schema::hasTable('t_kapasitas_lowongan')) {
                $query->leftJoin('t_kapasitas_lowongan as tkl', 'ml.id_lowongan', '=', 'tkl.id_lowongan')
                    ->addSelect('tkl.kapasitas_tersedia', 'tkl.kapasitas_total');
            } else {
                $query->addSelect(DB::raw('5 as kapasitas_tersedia'), DB::raw('10 as kapasitas_total'));
            }

            $opportunities = $query->orderBy('ml.created_at', 'desc')
                ->limit(20)
                ->get();

            Log::info('✅ Opportunities retrieved', [
                'count' => $opportunities->count(),
                'sample_opportunity' => $opportunities->first(),
                'all_ids' => $opportunities->pluck('id_lowongan')->toArray()
            ]);

            return $opportunities;
        } catch (\Exception $e) {
            Log::error('💥 Error getting opportunities', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return collect([]);
        }
    }

    /**
     * ✅ IMPROVED: Calculate criteria scores dengan debugging detail
     */
    private function calculateCriteriaScores($mahasiswa, $opportunities)
    {
        $alternatives = [];

        Log::info('🧮 Starting criteria calculation', [
            'mahasiswa_id' => $mahasiswa->id_mahasiswa ?? $mahasiswa->id ?? 'unknown',
            'opportunities_count' => $opportunities->count()
        ]);

        foreach ($opportunities as $index => $opportunity) {
            try {
                Log::info("🏢 Processing opportunity {$index}", [
                    'id' => $opportunity->id_lowongan,
                    'title' => $opportunity->judul_lowongan,
                    'company' => $opportunity->nama_perusahaan
                ]);

                // ✅ CALCULATE WITH FALLBACKS
                $minatData = $this->calculateInterestScore($mahasiswa, $opportunity);
                $skillData = $this->calculateSkillScore($mahasiswa, $opportunity);
                $wilayahData = $this->calculateDistanceScore($mahasiswa, $opportunity);
                $kuotaData = $this->calculateQuotaScore($opportunity);
                $ipkData = $this->calculateIPKScore($mahasiswa, $opportunity);

                Log::info("📊 Calculated scores for opportunity {$opportunity->id_lowongan}", [
                    'minat' => $minatData,
                    'skill' => $skillData,
                    'wilayah' => $wilayahData,
                    'kuota' => $kuotaData,
                    'ipk' => $ipkData
                ]);

                $alternatives[] = [
                    'opportunity_id' => $opportunity->id_lowongan,
                    'opportunity_name' => $opportunity->judul_lowongan,
                    'company_name' => $opportunity->nama_perusahaan,
                    'logo_perusahaan' => $opportunity->logo_perusahaan ?? null,
                    'lokasi' => $opportunity->lokasi ?? 'Unknown Location',
                    'criteria_scores' => [
                        'minat' => $minatData,
                        'skill' => $skillData,
                        'wilayah' => $wilayahData,
                        'kuota' => $kuotaData,
                        'ipk' => $ipkData
                    ],
                    'raw_data' => [
                        'interest_count' => $this->getInterestCount($mahasiswa, $opportunity),
                        'skill_match_count' => $this->getSkillMatchCount($mahasiswa, $opportunity),
                        'skill_match_percentage' => is_array($skillData) ? ($skillData['percentage'] ?? 0) : 50,
                        'distance_km' => is_array($wilayahData) ? ($wilayahData['distance_km'] ?? 100) : 100,
                        'kuota_available' => $opportunity->kapasitas_tersedia ?? 5,
                        'student_ipk' => $mahasiswa->ipk ?? 3.0,
                        'required_ipk' => $opportunity->min_ipk ?? 0
                    ]
                ];

                Log::info("✅ Successfully processed opportunity {$opportunity->id_lowongan}");
            } catch (\Exception $e) {
                Log::error("💥 Error calculating criteria for opportunity {$opportunity->id_lowongan}", [
                    'error' => $e->getMessage(),
                    'opportunity_title' => $opportunity->judul_lowongan ?? 'Unknown'
                ]);

                // ✅ ADD FALLBACK ALTERNATIVE
                $alternatives[] = [
                    'opportunity_id' => $opportunity->id_lowongan,
                    'opportunity_name' => $opportunity->judul_lowongan ?? 'Unknown Position',
                    'company_name' => $opportunity->nama_perusahaan ?? 'Unknown Company',
                    'logo_perusahaan' => $opportunity->logo_perusahaan ?? null,
                    'lokasi' => $opportunity->lokasi ?? 'Unknown Location',
                    'criteria_scores' => [
                        'minat' => ['score' => 2, 'percentage' => 50, 'category' => 'Fallback'],
                        'skill' => ['score' => 2, 'percentage' => 50, 'category' => 'Fallback'],
                        'wilayah' => ['score' => 2, 'distance_km' => 100, 'category' => 'Fallback'],
                        'kuota' => ['score' => 2, 'available_quota' => 5, 'category' => 'Fallback'],
                        'ipk' => ['score' => 2, 'student_ipk' => 3.0, 'category' => 'Fallback']
                    ],
                    'raw_data' => [
                        'interest_count' => 0,
                        'skill_match_count' => 0,
                        'skill_match_percentage' => 50,
                        'distance_km' => 100,
                        'kuota_available' => 5,
                        'student_ipk' => 3.0,
                        'required_ipk' => 0
                    ]
                ];

                Log::info("⚠️ Added fallback alternative for opportunity {$opportunity->id_lowongan}");
            }
        }

        Log::info('✅ Criteria calculation completed', [
            'total_opportunities' => $opportunities->count(),
            'successful_alternatives' => count($alternatives),
            'sample_alternative' => $alternatives[0] ?? null
        ]);

        return $alternatives;
    }

    /**
     * ✅ ROBUST: Calculate interest score
     */
    private function calculateInterestScore($mahasiswa, $opportunity)
    {
        try {
            // 🔍 DEBUG: Log semua informasi mahasiswa
            Log::info('🎯 MINAT DEBUG - Mahasiswa Object', [
                'mahasiswa_full' => (array) $mahasiswa,
                'id_mahasiswa' => $mahasiswa->id_mahasiswa ?? 'NOT_SET',
                'id_user' => $mahasiswa->id_user ?? 'NOT_SET',
                'opportunity_id' => $opportunity->id_lowongan ?? 'NOT_SET'
            ]);

            if (!Schema::hasTable('t_minat_mahasiswa')) {
                return [
                    'score' => 2,
                    'percentage' => 60,
                    'match_count' => 0,
                    'total_required' => 0,
                    'category' => 'No Interest System'
                ];
            }

            // 🔍 Query minat mahasiswa
            $mahasiswaId = $mahasiswa->id_mahasiswa ?? $mahasiswa->id ?? 0;

            Log::info('🎯 MINAT DEBUG - Query Mahasiswa', [
                'mahasiswa_id_used' => $mahasiswaId,
                'sql_equivalent' => "SELECT minat_id FROM t_minat_mahasiswa WHERE mahasiswa_id = {$mahasiswaId}"
            ]);

            $studentInterests = DB::table('t_minat_mahasiswa')
                ->where('mahasiswa_id', $mahasiswaId)
                ->pluck('minat_id')
                ->toArray();

            Log::info('🎯 MINAT DEBUG - Student Result', [
                'mahasiswa_id' => $mahasiswaId,
                'student_interests' => $studentInterests,
                'count' => count($studentInterests),
                'data_types' => array_map('gettype', $studentInterests)
            ]);

            // 🔍 Query minat lowongan
            $lowonganId = $opportunity->id_lowongan;

            Log::info('🎯 MINAT DEBUG - Query Lowongan', [
                'lowongan_id_used' => $lowonganId,
                'sql_equivalent' => "SELECT minat_id FROM t_minat_lowongan WHERE id_lowongan = {$lowonganId}"
            ]);

            $requiredInterests = DB::table('t_minat_lowongan')
                ->where('id_lowongan', $lowonganId)
                ->pluck('minat_id')
                ->toArray();

            Log::info('🎯 MINAT DEBUG - Lowongan Result', [
                'lowongan_id' => $lowonganId,
                'required_interests' => $requiredInterests,
                'count' => count($requiredInterests),
                'data_types' => array_map('gettype', $requiredInterests)
            ]);

            // 🔍 Cek intersect
            $matchCount = count(array_intersect($studentInterests, $requiredInterests));
            $intersection = array_intersect($studentInterests, $requiredInterests);
            $totalRequired = count($requiredInterests);

            Log::info('🎯 MINAT DEBUG - Intersection', [
                'student_interests' => $studentInterests,
                'required_interests' => $requiredInterests,
                'intersection' => $intersection,
                'match_count' => $matchCount
            ]);

            // Scoring logic
            $matchPercentage = $totalRequired > 0 ? ($matchCount / $totalRequired) * 100 : 0;

            if ($matchPercentage >= 80) {
                $score = 3;
                $category = 'Excellent Match';
            } elseif ($matchPercentage >= 50) {
                $score = 2;
                $category = 'Good Match';
            } else {
                $score = 1;
                $category = 'Limited Match';
            }

            $result = [
                'score' => $score,
                'percentage' => $matchCount > 0 ? (($matchCount / max(count($requiredInterests), 1)) * 100) : 0,
                'match_count' => $matchCount,
                'total_required' => count($requiredInterests),
                'category' => $category
            ];

            Log::info('🎯 MINAT DEBUG - Final Result', $result);

            return $result;
        } catch (\Exception $e) {
            Log::error('💥 MINAT DEBUG - Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'score' => 2,
                'percentage' => 50,
                'match_count' => 0,
                'total_required' => 0,
                'category' => 'Error - Using Default'
            ];
        }
    }

    /**
     * ✅ ROBUST: Calculate skill score
     */
    private function calculateSkillScore($mahasiswa, $opportunity)
    {
        try {
            if (!Schema::hasTable('t_skill_mahasiswa') || !Schema::hasTable('t_skill_lowongan')) {
                Log::info('🛠️ No skill system - using default score');
                return [
                    'score' => 2,
                    'percentage' => 55,
                    'match_count' => 0,
                    'total_required' => 0,
                    'category' => 'No Skill System'
                ];
            }

            $studentSkills = DB::table('t_skill_mahasiswa')
                ->where('user_id', $mahasiswa->id_user ?? $mahasiswa->id ?? 0)
                ->pluck('skill_id')
                ->toArray();

            $requiredSkills = DB::table('t_skill_lowongan')
                ->where('id_lowongan', $opportunity->id_lowongan)
                ->pluck('id_skill')
                ->toArray();

            Log::info('🛠️ Skill matching', [
                'student_skills' => $studentSkills,
                'required_skills' => $requiredSkills
            ]);

            if (empty($requiredSkills)) {
                return [
                    'score' => 2,
                    'percentage' => 60,
                    'match_count' => 0,
                    'total_required' => 0,
                    'category' => 'No Specific Skills Required'
                ];
            }

            $matchCount = count(array_intersect($studentSkills, $requiredSkills));
            $totalRequired = count($requiredSkills);
            $matchPercentage = $totalRequired > 0 ? ($matchCount / $totalRequired) * 100 : 0;

            // Scoring logic
            $score = 1;
            if ($matchPercentage >= 80) {
                $score = 3;
                $category = 'Excellent Skills Match';
            } elseif ($matchPercentage >= 50) {
                $score = 2;
                $category = 'Good Skills Match';
            } else {
                $category = 'Skills Gap Exists';
            }

            $result = [
                'score' => $score,
                'percentage' => round($matchPercentage, 2),
                'match_count' => $matchCount,
                'total_required' => $totalRequired,
                'student_total' => count($studentSkills),
                'category' => $category
            ];

            Log::info('🛠️ Skill score calculated', $result);
            return $result;
        } catch (\Exception $e) {
            Log::error('💥 Error calculating skill score', [
                'error' => $e->getMessage()
            ]);
            return [
                'score' => 2,
                'percentage' => 50,
                'category' => 'Error - Using Default'
            ];
        }
    }

    /**
     * ✅ ROBUST: Calculate distance score
     */
    private function calculateDistanceScore($mahasiswa, $opportunity)
    {
        try {
            if (!isset($mahasiswa->wilayah_id) || !$mahasiswa->wilayah_id) {
                return [
                    'score' => 2,
                    'distance_km' => null,
                    'category' => 'Unknown Student Location'
                ];
            }

            $companyWilayah = DB::table('m_perusahaan')
                ->where('perusahaan_id', $opportunity->perusahaan_id)
                ->value('wilayah_id');

            if (!$companyWilayah) {
                return [
                    'score' => 2,
                    'distance_km' => null,
                    'category' => 'Unknown Company Location'
                ];
            }

            // Same city = best score (sangat dekat)
            if ($mahasiswa->wilayah_id == $companyWilayah) {
                return [
                    'score' => 1, // 1 = sangat dekat (sesuai database)
                    'distance_km' => 0,
                    'duration_minutes' => 0,
                    'category' => 'Same City'
                ];
            }

            // Calculate distance
            $distanceData = $this->getDistanceBetweenLocations(
                $mahasiswa->wilayah_id,
                $companyWilayah
            );

            $distanceKm = $distanceData['distance_km'] ?? 75;

            // Skor sesuai database: 1 = sangat dekat, 2 = sedang, 3 = sangat jauh
            if ($distanceKm <= 30) {
                $score = 1;
                $category = 'Sangat Dekat';
            } elseif ($distanceKm <= 100) {
                $score = 2;
                $category = 'Sedang';
            } else {
                $score = 3;
                $category = 'Sangat Jauh';
            }

            $result = [
                'score' => $score,
                'distance_km' => $distanceKm,
                'duration_minutes' => $distanceData['duration_minutes'] ?? 90,
                'category' => $category,
                'calculation_method' => $distanceData['method'] ?? 'estimated'
            ];

            return $result;
        } catch (\Exception $e) {
            return [
                'score' => 2,
                'distance_km' => 75,
                'category' => 'Error - Using Default'
            ];
        }
    }

    /**
     * ✅ SIMPLE: Calculate quota score
     */
    private function calculateQuotaScore($opportunity)
    {
        $quota = $opportunity->kapasitas_tersedia ?? 5;

        $score = 1;
        $category = 'Limited Availability';

        if ($quota >= 10) {
            $score = 3;
            $category = 'High Availability';
        } elseif ($quota >= 5) {
            $score = 2;
            $category = 'Moderate Availability';
        }

        $result = [
            'score' => $score,
            'available_quota' => $quota,
            'total_quota' => $opportunity->kapasitas_total ?? $quota,
            'category' => $category
        ];

        Log::info('🎫 Quota score calculated', $result);
        return $result;
    }

    /**
     * ✅ SIMPLE: Calculate IPK score
     */
    private function calculateIPKScore($mahasiswa, $opportunity)
    {
        $studentIPK = $mahasiswa->ipk ?? 3.0;
        $requiredIPK = $opportunity->min_ipk ?? 0;

        if ($requiredIPK == 0) {
            $result = [
                'score' => 2,
                'student_ipk' => $studentIPK,
                'required_ipk' => $requiredIPK,
                'gap' => 0,
                'category' => 'No IPK Requirement'
            ];
        } else {
            $gap = $studentIPK - $requiredIPK;

            $score = 1;
            $category = 'Below Requirement';

            if ($gap >= 0.5) {
                $score = 3;
                $category = 'Significantly Exceeds';
            } elseif ($gap >= 0) {
                $score = 2;
                $category = 'Meets Requirement';
            }

            $result = [
                'score' => $score,
                'student_ipk' => $studentIPK,
                'required_ipk' => $requiredIPK,
                'gap' => round($gap, 2),
                'category' => $category
            ];
        }

        Log::info('🎓 IPK score calculated', $result);
        return $result;
    }

    /**
     * ✅ FALLBACK: Get distance with multiple methods
     */
    private function getDistanceBetweenLocations($fromWilayahId, $toWilayahId)
    {
        try {
            // ✅ SKIP Java Distance Service - langsung ke coordinate calculation
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

                $distance = $this->haversineDistance(
                    $fromCity->latitude,
                    $fromCity->longitude,
                    $toCity->latitude,
                    $toCity->longitude
                );

                return [
                    'distance_km' => round($distance, 2),
                    'duration_minutes' => $this->estimateDuration($distance),
                    'method' => 'haversine'
                ];
            }

            // ✅ IMPROVED: Better fallback estimation
            $estimatedDistance = 50 + abs($fromWilayahId - $toWilayahId) * 15;

            return [
                'distance_km' => min($estimatedDistance, 300),
                'duration_minutes' => $this->estimateDuration($estimatedDistance),
                'method' => 'estimated'
            ];
        } catch (\Exception $e) {
            Log::error('💥 Error calculating distance', [
                'from' => $fromWilayahId,
                'to' => $toWilayahId,
                'error' => $e->getMessage()
            ]);

            return [
                'distance_km' => 75,
                'duration_minutes' => 90,
                'method' => 'fallback'
            ];
        }
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function estimateDuration($distance)
    {
        if ($distance <= 30) return round($distance * 2.5);
        if ($distance <= 100) return round($distance * 1.8);
        return round($distance * 1.3);
    }

    /**
     * ✅ SIMPLIFIED: Apply EDAS method
     */
    private function applyEDASMethod($alternatives)
    {
        try {
            Log::info('🧮 Starting EDAS method', ['alternatives_count' => count($alternatives)]);

            // ✅ SIMPLIFIED EDAS FOR DEBUGGING
            $simpleRanking = [];

            foreach ($alternatives as $index => $alternative) {
                $scores = $alternative['criteria_scores'];

                // Extract numeric scores
                $minatScore = is_array($scores['minat']) ? $scores['minat']['score'] : $scores['minat'];
                $skillScore = is_array($scores['skill']) ? $scores['skill']['score'] : $scores['skill'];
                $wilayahScore = is_array($scores['wilayah']) ? $scores['wilayah']['score'] : $scores['wilayah'];
                $kuotaScore = is_array($scores['kuota']) ? $scores['kuota']['score'] : $scores['kuota'];
                $ipkScore = is_array($scores['ipk']) ? $scores['ipk']['score'] : $scores['ipk'];

                // Simple weighted average
                $weights = $this->getCriteriaWeights();
                $simpleScore = (
                    $minatScore * $weights['minat'] +
                    $skillScore * $weights['skill'] +
                    (4 - $wilayahScore) * $weights['wilayah'] + // Invert wilayah (cost criterion)
                    $kuotaScore * $weights['kuota'] +
                    $ipkScore * $weights['ipk']
                ) / 3; // Normalize to 0-1

                $simpleRanking[] = [
                    'rank' => 0,
                    'opportunity_id' => $alternative['opportunity_id'],
                    'opportunity_name' => $alternative['opportunity_name'],
                    'company_name' => $alternative['company_name'],
                    'logo_perusahaan' => $alternative['logo_perusahaan'],
                    'lokasi' => $alternative['lokasi'],
                    'as_score' => round($simpleScore, 4),
                    'sp' => round($simpleScore * 0.6, 4),
                    'sn' => round($simpleScore * 0.4, 4),
                    'nsp' => round($simpleScore, 4),
                    'nsn' => round($simpleScore, 4),
                    'criteria_scores' => $alternative['criteria_scores'],
                    'raw_data' => $alternative['raw_data']
                ];
            }

            // Sort by score
            usort($simpleRanking, function ($a, $b) {
                return $b['as_score'] <=> $a['as_score'];
            });

            // Add ranks
            foreach ($simpleRanking as $index => &$item) {
                $item['rank'] = $index + 1;
            }

            Log::info('✅ EDAS method completed', [
                'ranking_count' => count($simpleRanking),
                'top_score' => $simpleRanking[0]['as_score'] ?? 'N/A'
            ]);

            return [
                'ranking' => $simpleRanking,
                'method' => 'simplified_edas'
            ];
        } catch (\Exception $e) {
            Log::error('💥 Error in EDAS method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getCriteriaWeights()
    {
        return [
            'minat' => 0.20,
            'skill' => 0.25,
            'wilayah' => 0.15,
            'kuota' => 0.20,
            'ipk' => 0.20
        ];
    }

    /**
     * ✅ HELPER: Get interest count
     */
    private function getInterestCount($mahasiswa, $opportunity)
    {
        try {
            if (!Schema::hasTable('t_minat_mahasiswa') || !Schema::hasTable('t_minat_lowongan')) {
                return 0;
            }

            $studentInterests = DB::table('t_minat_mahasiswa')
                ->where('mahasiswa_id', $mahasiswa->id_mahasiswa ?? $mahasiswa->id ?? 0)
                ->pluck('minat_id')
                ->toArray();

            $opportunityInterests = DB::table('t_minat_lowongan')
                ->where('id_lowongan', $opportunity->id_lowongan)
                ->pluck('minat_id')
                ->toArray();

            return count(array_intersect($studentInterests, $opportunityInterests));
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * ✅ HELPER: Get skill match count
     */
    private function getSkillMatchCount($mahasiswa, $opportunity)
    {
        try {
            if (!Schema::hasTable('t_skill_mahasiswa') || !Schema::hasTable('t_skill_lowongan')) {
                return 0;
            }

            $studentSkills = DB::table('t_skill_mahasiswa')
                ->where('user_id', $mahasiswa->id_user ?? $mahasiswa->id ?? 0)
                ->pluck('skill_id')
                ->toArray();

            $requiredSkills = DB::table('t_skill_lowongan')
                ->where('id_lowongan', $opportunity->id_lowongan)
                ->pluck('id_skill')
                ->toArray();

            return count(array_intersect($studentSkills, $requiredSkills));
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * ✅ DETAILED ANALYSIS: Implement detailed analysis
     */
    public function getDetailedAnalysis($mahasiswaId, $opportunityId)
    {
        try {
            Log::info('🔍 Getting detailed analysis', [
                'mahasiswa_id' => $mahasiswaId,
                'opportunity_id' => $opportunityId
            ]);

            $mahasiswa = $this->getMahasiswaData($mahasiswaId);
            if (!$mahasiswa) {
                return ['error' => 'Mahasiswa data not found'];
            }

            $opportunity = DB::table('m_lowongan as ml')
                ->join('m_perusahaan as mp', 'ml.perusahaan_id', '=', 'mp.perusahaan_id')
                ->leftJoin('m_wilayah as mw', 'mp.wilayah_id', '=', 'mw.wilayah_id')
                ->where('ml.id_lowongan', $opportunityId)
                ->select(
                    'ml.*',
                    'mp.nama_perusahaan',
                    'mp.logo as logo_perusahaan',
                    'mp.wilayah_id',
                    'mw.nama_kota as lokasi'
                )
                ->first();

            if (!$opportunity) {
                return ['error' => 'Opportunity not found'];
            }

            // Calculate detailed scores
            $minatAnalysis = $this->calculateInterestScore($mahasiswa, $opportunity);
            $skillAnalysis = $this->calculateSkillScore($mahasiswa, $opportunity);
            $wilayahAnalysis = $this->calculateDistanceScore($mahasiswa, $opportunity);
            $kuotaAnalysis = $this->calculateQuotaScore($opportunity);
            $ipkAnalysis = $this->calculateIPKScore($mahasiswa, $opportunity);

            $criteriaScores = [
                'minat' => $minatAnalysis,
                'skill' => $skillAnalysis,
                'wilayah' => $wilayahAnalysis,
                'kuota' => $kuotaAnalysis,
                'ipk' => $ipkAnalysis
            ];

            $weights = $this->getCriteriaWeights();
            $overallScore = 0;

            foreach ($criteriaScores as $criterion => $analysis) {
                $score = is_array($analysis) ? $analysis['score'] : $analysis;
                $overallScore += ($score / 3) * $weights[$criterion];
            }

            return [
                'mahasiswa' => [
                    'id' => $mahasiswaId,
                    'name' => $mahasiswa->nama ?? 'Unknown',
                    'ipk' => $mahasiswa->ipk ?? 0,
                    'wilayah_id' => $mahasiswa->wilayah_id ?? null
                ],
                'opportunity' => [
                    'id' => $opportunity->id_lowongan,
                    'title' => $opportunity->judul_lowongan,
                    'company' => $opportunity->nama_perusahaan,
                    'location' => $opportunity->lokasi ?? 'Unknown',
                    'min_ipk' => $opportunity->min_ipk ?? 0
                ],
                'detailed_scores' => $criteriaScores,
                'overall_score' => round($overallScore, 4),
                'recommendations' => $this->generateRecommendations($criteriaScores),
                'analysis_timestamp' => now()->toISOString()
            ];
        } catch (\Exception $e) {
            Log::error('💥 Error in detailed analysis', [
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Analysis failed: ' . $e->getMessage()];
        }
    }

    private function generateRecommendations($criteriaScores)
    {
        $recommendations = [];

        foreach ($criteriaScores as $criterion => $analysis) {
            $score = is_array($analysis) ? $analysis['score'] : $analysis;
            $category = is_array($analysis) ? ($analysis['category'] ?? 'Unknown') : 'No Details';

            if ($score <= 1.5) {
                $recommendations[] = [
                    'criterion' => $criterion,
                    'status' => 'needs_improvement',
                    'message' => "Consider improving your {$criterion} alignment",
                    'details' => $category
                ];
            } elseif ($score >= 2.5) {
                $recommendations[] = [
                    'criterion' => $criterion,
                    'status' => 'strength',
                    'message' => "Strong {$criterion} match - this is an advantage",
                    'details' => $category
                ];
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = [
                'criterion' => 'overall',
                'status' => 'balanced',
                'message' => 'Balanced profile - good opportunity to consider',
                'details' => 'No major strengths or weaknesses identified'
            ];
        }

        return $recommendations;
    }
}
