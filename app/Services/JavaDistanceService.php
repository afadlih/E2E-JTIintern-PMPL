<?php
// filepath: d:\laragon\www\JTIintern\app\Services\JavaDistanceService.php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class JavaDistanceService
{
    /**
     * Get distance between two cities in Java
     */
    public function getDistance($fromWilayahId, $toWilayahId)
    {
        if ($fromWilayahId == $toWilayahId) {
            return [
                'distance_km' => 0,
                'duration_minutes' => 0,
                'score' => 1,
                'category' => 'Sama Lokasi',
                'source' => 'same_location',
                'method' => 'direct'
            ];
        }

        $cacheKey = "java_distance_{$fromWilayahId}_{$toWilayahId}";

        return Cache::remember($cacheKey, 60 * 24 * 30, function () use ($fromWilayahId, $toWilayahId) {

            // Try distance matrix first
            $distance = DB::table('m_distance_matrix')
                ->where('from_wilayah_id', $fromWilayahId)
                ->where('to_wilayah_id', $toWilayahId)
                ->first();

            if ($distance) {
                return [
                    'distance_km' => $distance->distance_km,
                    'duration_minutes' => $distance->duration_minutes,
                    'score' => $distance->score,
                    'category' => $distance->category,
                    'source' => 'distance_matrix',
                    'method' => $distance->calculation_method
                ];
            }

            // If not found, calculate from coordinates
            return $this->calculateFromCoordinates($fromWilayahId, $toWilayahId);
        });
    }

    /**
     * Calculate distance from coordinates
     */
    private function calculateFromCoordinates($fromWilayahId, $toWilayahId)
    {
        $fromCity = DB::table('m_wilayah')
            ->where('wilayah_id', $fromWilayahId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();

        $toCity = DB::table('m_wilayah')
            ->where('wilayah_id', $toWilayahId)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->first();

        if (!$fromCity || !$toCity) {
            return $this->getFallbackDistance();
        }

        // Calculate Haversine distance
        $distance = $this->haversineDistance(
            $fromCity->latitude,
            $fromCity->longitude,
            $toCity->latitude,
            $toCity->longitude
        );

        $score = $this->distanceToScore($distance);
        $category = $this->scoreToCategory($score);
        $duration = $this->estimateDuration($distance);

        // Save for future use
        $this->saveToMatrix($fromWilayahId, $toWilayahId, $distance, $duration, $score, $category);

        return [
            'distance_km' => round($distance, 2),
            'duration_minutes' => $duration,
            'score' => $score,
            'category' => $category,
            'source' => 'calculated_coordinates',
            'method' => 'haversine'
        ];
    }

    /**
     * Calculate Haversine distance between two coordinates
     */
    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Convert distance to score (1-3 scale)
     */
    private function distanceToScore($distance)
    {
        if ($distance <= 1) return 3; // Sangat dekat
        if ($distance <= 3) return 2; // Sedang
        return 1; // Jauh
    }

    /**
     * Convert score to category
     */
    private function scoreToCategory($score)
    {
        return match ($score) {
            1 => 'Sangat Dekat',
            2 => 'Sedang',
            3 => 'Jauh',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Estimate duration based on distance
     */
    private function estimateDuration($distance)
    {
        // More realistic duration calculation for Java Island
        if ($distance <= 30) {
            return round($distance * 1.8); // City traffic - slower
        } elseif ($distance <= 100) {
            return round($distance * 1.3); // Mixed traffic
        } else {
            return round($distance * 1.1); // Mostly highway - faster
        }
    }

    /**
     * Get fallback distance when coordinates not available
     */
    private function getFallbackDistance()
    {
        return [
            'distance_km' => 75,
            'duration_minutes' => 90,
            'score' => 2,
            'category' => 'Estimasi',
            'source' => 'fallback',
            'method' => 'estimated'
        ];
    }

    /**
     * Save calculated distance to matrix for future use
     */
    private function saveToMatrix($fromId, $toId, $distance, $duration, $score, $category)
    {
        try {
            // Check if already exists
            $exists = DB::table('m_distance_matrix')
                ->where('from_wilayah_id', $fromId)
                ->where('to_wilayah_id', $toId)
                ->exists();

            if (!$exists) {
                DB::table('m_distance_matrix')->insert([
                    'from_wilayah_id' => $fromId,
                    'to_wilayah_id' => $toId,
                    'distance_km' => round($distance, 2),
                    'duration_minutes' => $duration,
                    'score' => $score,
                    'category' => $category,
                    'calculation_method' => 'haversine',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("Failed to save distance matrix: " . $e->getMessage());
        }
    }

    /**
     * Get distance for SPK calculation with enhanced scoring
     */
    public function getDistanceForSPK($fromWilayahId, $toWilayahId)
    {
        $distance = $this->getDistance($fromWilayahId, $toWilayahId);

        // Enhanced SPK scoring based on Java Island context
        $criterionValue = $this->calculateSPKCriterionValue($distance);

        return [
            'criterion_value' => $criterionValue,
            'distance_data' => $distance,
            'normalized_score' => $criterionValue * 33.33, // 0-100 scale
            'interpretation' => $this->getInterpretation($distance['score']),
            'recommendation' => $this->getRecommendation($distance['score']),
            'transport_analysis' => $this->getTransportAnalysis($distance),
            'cost_analysis' => $this->getCostAnalysis($distance),
            'accessibility_score' => $this->getAccessibilityScore($distance)
        ];
    }

    /**
     * Calculate SPK criterion value with Java-specific logic
     */
    private function calculateSPKCriterionValue($distance)
    {
        $baseScore = match ($distance['score']) {
            1 => 3, // Dekat = nilai tinggi (terbaik untuk cost criterion)
            2 => 2, // Sedang = nilai menengah
            3 => 1, // Jauh = nilai rendah (terburuk untuk cost criterion)
            default => 2
        };

        return $baseScore;
    }

    /**
     * Get transport analysis for Java Island
     */
    private function getTransportAnalysis($distance)
    {
        $transportOptions = [];
        $primaryMode = '';
        $difficulty = 'Easy';

        switch ($distance['score']) {
            case 1: // ≤30km
                $transportOptions = [
                    'Motor pribadi' => ['cost' => 15000, 'time' => $distance['duration_minutes']],
                    'Mobil pribadi' => ['cost' => 25000, 'time' => $distance['duration_minutes']],
                    'Ojek online' => ['cost' => 20000, 'time' => $distance['duration_minutes'] + 10],
                    'Angkot/Bus kota' => ['cost' => 8000, 'time' => $distance['duration_minutes'] + 30]
                ];
                $primaryMode = 'Motor/Mobil pribadi';
                $difficulty = 'Sangat Mudah';
                break;

            case 2: // 31-100km
                $transportOptions = [
                    'Motor pribadi' => ['cost' => 35000, 'time' => $distance['duration_minutes']],
                    'Mobil pribadi' => ['cost' => 60000, 'time' => $distance['duration_minutes']],
                    'Bus AKAP' => ['cost' => 25000, 'time' => $distance['duration_minutes'] + 60],
                    'Travel/Shuttle' => ['cost' => 50000, 'time' => $distance['duration_minutes'] + 30],
                    'Kereta' => ['cost' => 30000, 'time' => $distance['duration_minutes'] - 30]
                ];
                $primaryMode = 'Bus/Travel/Kereta';
                $difficulty = 'Mudah';
                break;

            case 3: // >100km
                $transportOptions = [
                    'Mobil pribadi' => ['cost' => 100000, 'time' => $distance['duration_minutes']],
                    'Bus AKAP' => ['cost' => 50000, 'time' => $distance['duration_minutes'] + 90],
                    'Kereta' => ['cost' => 75000, 'time' => $distance['duration_minutes']],
                    'Akomodasi (kost/hotel)' => ['cost' => 300000, 'time' => 0]
                ];
                $primaryMode = 'Kereta/Akomodasi';
                $difficulty = 'Memerlukan Perencanaan';
                break;
        }

        return [
            'options' => $transportOptions,
            'recommended_mode' => $primaryMode,
            'difficulty_level' => $difficulty,
            'daily_commute_feasible' => $distance['score'] <= 2,
            'weekly_commute_feasible' => $distance['score'] <= 3
        ];
    }

    /**
     * Get cost analysis
     */
    private function getCostAnalysis($distance)
    {
        $dailyCost = 0;
        $monthlyCost = 0;
        $recommendedBudget = '';

        switch ($distance['score']) {
            case 1:
                $dailyCost = 20000;
                $monthlyCost = $dailyCost * 22; // 22 working days
                $recommendedBudget = 'Rp 400,000 - 500,000/bulan';
                break;
            case 2:
                $dailyCost = 50000;
                $monthlyCost = $dailyCost * 22;
                $recommendedBudget = 'Rp 1,000,000 - 1,200,000/bulan';
                break;
            case 3:
                $dailyCost = 0; // Assume accommodation
                $monthlyCost = 1500000; // Monthly accommodation
                $recommendedBudget = 'Rp 1,500,000 - 2,000,000/bulan (dengan akomodasi)';
                break;
        }

        return [
            'daily_cost_estimate' => $dailyCost,
            'monthly_cost_estimate' => $monthlyCost,
            'recommended_budget' => $recommendedBudget,
            'cost_category' => $this->getCostCategory($monthlyCost),
            'financial_feasibility' => $this->getFinancialFeasibility($monthlyCost)
        ];
    }

    /**
     * Get accessibility score based on Java infrastructure
     */
    private function getAccessibilityScore($distance)
    {
        $baseScore = match ($distance['score']) {
            1 => 90,
            2 => 70,
            3 => 50,
            default => 30
        };

        // Java Island infrastructure bonus
        $infraBonus = 10;

        return min(100, $baseScore + $infraBonus);
    }

    private function getInterpretation($score)
    {
        switch ($score) {
            case 1:
                return 'Lokasi sangat strategis untuk magang harian di Pulau Jawa';
            case 2:
                return 'Lokasi cukup baik dengan infrastruktur transportasi Jawa yang memadai';
            case 3:
                return 'Lokasi jauh, pertimbangkan akomodasi atau weekend internship';
            default:
                return 'Perlu evaluasi lokasi lebih detail';
        }
    }

    private function getRecommendation($score)
    {
        return match ($score) {
            1 => 'Sangat direkomendasikan - optimal untuk commuting harian',
            2 => 'Direkomendasikan - manfaatkan transportasi umum Jawa yang baik',
            3 => 'Pertimbangkan jika sesuai passion dan ada bantuan akomodasi',
            default => 'Perlu analisis cost-benefit lebih detail'
        };
    }

    private function getCostCategory($monthlyCost)
    {
        if ($monthlyCost <= 500000) return 'Ekonomis';
        if ($monthlyCost <= 1200000) return 'Menengah';
        return 'Premium';
    }

    private function getFinancialFeasibility($monthlyCost)
    {
        // Based on typical student budget
        if ($monthlyCost <= 400000) return 'Sangat Feasible';
        if ($monthlyCost <= 800000) return 'Feasible';
        if ($monthlyCost <= 1500000) return 'Perlu Pertimbangan';
        return 'Perlu Bantuan/Beasiswa';
    }

    /**
     * Get comprehensive city analysis for student location
     */
    public function getStudentLocationAnalysis($studentWilayahId)
    {
        $studentCity = DB::table('m_wilayah')->where('wilayah_id', $studentWilayahId)->first();

        if (!$studentCity) {
            return ['error' => 'Student location not found'];
        }

        $distances = DB::table('m_distance_matrix as dm')
            ->join('m_wilayah as w', 'dm.to_wilayah_id', '=', 'w.wilayah_id')
            ->where('dm.from_wilayah_id', $studentWilayahId)
            ->select(
                'dm.*',
                'w.nama_kota',
                'w.province_name',
                'w.city_type'
            )
            ->orderBy('dm.score')
            ->orderBy('dm.distance_km')
            ->get();

        $analysis = [
            'student_location' => [
                'city' => $studentCity->nama_kota,
                'province' => $studentCity->province_name ?? 'Unknown',
                'coordinates' => [
                    'lat' => $studentCity->latitude,
                    'lng' => $studentCity->longitude
                ]
            ],
            'accessibility_summary' => [
                'total_reachable_cities' => $distances->count(),
                'very_accessible' => $distances->where('score', 1)->count(),
                'moderately_accessible' => $distances->where('score', 2)->count(),
                'less_accessible' => $distances->where('score', 3)->count(),
                'average_distance' => round($distances->avg('distance_km'), 2)
            ],
            'top_recommendations' => $distances->take(10)->map(function ($item) {
                return [
                    'wilayah_id' => $item->to_wilayah_id,
                    'nama_kota' => $item->nama_kota,
                    'province' => $item->province_name,
                    'distance_km' => $item->distance_km,
                    'duration_minutes' => $item->duration_minutes,
                    'category' => $item->category,
                    'accessibility_score' => $this->getAccessibilityScore(['score' => $item->score])
                ];
            })
        ];

        return $analysis;
    }
}
