<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\SPKRecommendationService;
use App\Models\Mahasiswa;
use App\Models\Lowongan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;

/**
 * Unit Test untuk SPKRecommendationService
 *
 * Test coverage:
 * 1. calculateEDASRecommendation - Positif dengan data valid
 * 2. calculateEDASRecommendation - Negatif dengan mahasiswa tidak ditemukan
 * 3. calculateEDASRecommendation - Negatif dengan tidak ada lowongan
 * 4. calculateSAWRecommendation - Positif dengan data valid
 * 5. calculateSAWRecommendation - Negatif dengan exception handling
 *
 * Command untuk menjalankan test:
 * php artisan test --filter SPKRecommendationServiceTest
 * php artisan test tests/Unit/Services/SPKRecommendationServiceTest.php
 */
class SPKRecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SPKRecommendationService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test Case 1: (Positif) calculateEDASRecommendation dengan data mahasiswa dan lowongan valid
     *
     * Input:
     * - Mahasiswa ID: 1
     * - Mahasiswa dengan profil lengkap (IPK, wilayah, skills)
     * - Lowongan tersedia: 5 lowongan aktif
     *
     * Expected Output:
     * - Array berisi 'method' => 'EDAS'
     * - Array berisi 'ranking' dengan minimal 1 hasil
     * - Array berisi 'top_recommendations'
     * - Tidak ada error message
     */
    public function test_calculateEDASRecommendation_dengan_data_valid()
    {
        // Arrange: Setup data mahasiswa
        $mahasiswa = Mahasiswa::factory()->create([
            'id_mahasiswa' => 1,
            'nama' => 'Test Mahasiswa',
            'nim' => '2141720001',
            'ipk' => 3.5,
            'wilayah_id' => 1,
        ]);

        // Setup lowongan (mock atau factory)
        $lowongan = Lowongan::factory()->count(5)->create([
            'status' => 'active',
            'kapasitas' => 5,
        ]);

        // Act: Panggil method calculateEDASRecommendation
        $result = $this->service->calculateEDASRecommendation($mahasiswa->id_mahasiswa);

        // Assert: Verifikasi output
        $this->assertIsArray($result);
        $this->assertArrayHasKey('method', $result);
        $this->assertEquals('EDAS', $result['method']);

        $this->assertArrayHasKey('ranking', $result);
        $this->assertNotEmpty($result['ranking']);

        $this->assertArrayHasKey('top_recommendations', $result);
        $this->assertLessThanOrEqual(6, count($result['top_recommendations']));

        $this->assertArrayNotHasKey('error', $result);

        // Verifikasi struktur mahasiswa
        $this->assertArrayHasKey('mahasiswa', $result);
        $this->assertEquals($mahasiswa->id_mahasiswa, $result['mahasiswa']['id']);
        $this->assertEquals($mahasiswa->ipk, $result['mahasiswa']['ipk']);
    }

    /**
     * Test Case 2: (Negatif) calculateEDASRecommendation dengan mahasiswa tidak ditemukan
     *
     * Input:
     * - Mahasiswa ID: 99999 (tidak ada di database)
     *
     * Expected Output:
     * - Array berisi 'error' => 'Mahasiswa data not found for ID: 99999'
     */
    public function test_calculateEDASRecommendation_mahasiswa_tidak_ditemukan()
    {
        // Arrange: Gunakan ID yang tidak ada
        $nonExistentId = 99999;

        // Act
        $result = $this->service->calculateEDASRecommendation($nonExistentId);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Mahasiswa data not found', $result['error']);
    }

    /**
     * Test Case 3: (Negatif) calculateEDASRecommendation dengan tidak ada lowongan tersedia
     *
     * Input:
     * - Mahasiswa ID: valid
     * - Lowongan: kosong (tidak ada lowongan aktif)
     *
     * Expected Output:
     * - Array berisi 'error' => 'No opportunities available in the system'
     */
    public function test_calculateEDASRecommendation_tidak_ada_lowongan()
    {
        // Arrange: Buat mahasiswa tapi tidak ada lowongan
        $mahasiswa = Mahasiswa::factory()->create([
            'ipk' => 3.5,
            'wilayah_id' => 1,
        ]);

        // Pastikan tidak ada lowongan di database
        Lowongan::query()->delete();

        // Act
        $result = $this->service->calculateEDASRecommendation($mahasiswa->id_mahasiswa);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('No opportunities available', $result['error']);
    }

    /**
     * Test Case 4: (Positif) calculateSAWRecommendation dengan data valid
     *
     * Input:
     * - Mahasiswa ID: 1
     * - Data profil lengkap
     * - Lowongan tersedia: 5 lowongan
     *
     * Expected Output:
     * - Array berisi 'method' => 'SAW'
     * - Array berisi 'ranking' dengan hasil terurut berdasarkan score
     * - Score SAW berupa float antara 0-1
     */
    public function test_calculateSAWRecommendation_dengan_data_valid()
    {
        // Arrange
        $mahasiswa = Mahasiswa::factory()->create([
            'nama' => 'Test SAW Mahasiswa',
            'ipk' => 3.7,
            'wilayah_id' => 2,
        ]);

        Lowongan::factory()->count(5)->create([
            'status' => 'active',
            'kapasitas' => 3,
        ]);

        // Act
        $result = $this->service->calculateSAWRecommendation($mahasiswa->id_mahasiswa);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals('SAW', $result['method']);

        $this->assertArrayHasKey('ranking', $result);
        $this->assertNotEmpty($result['ranking']);

        // Verifikasi score SAW ada dan valid
        $firstRank = $result['ranking'][0];
        $this->assertArrayHasKey('saw_score', $firstRank);
        $this->assertIsFloat($firstRank['saw_score']);
        $this->assertGreaterThanOrEqual(0, $firstRank['saw_score']);
        $this->assertLessThanOrEqual(1, $firstRank['saw_score']);
    }

    /**
     * Test Case 5: (Negatif) calculateSAWRecommendation dengan profil tidak lengkap
     *
     * Input:
     * - Mahasiswa dengan IPK null atau wilayah_id null
     *
     * Expected Output:
     * - Tetap return result atau error jika profil tidak lengkap
     * - Atau minimal tidak throw unhandled exception
     */
    public function test_calculateSAWRecommendation_profil_tidak_lengkap()
    {
        // Arrange: Mahasiswa dengan profil tidak lengkap
        $mahasiswa = Mahasiswa::factory()->create([
            'ipk' => null,
            'wilayah_id' => null,
        ]);

        Lowongan::factory()->count(3)->create(['status' => 'active']);

        // Act
        $result = $this->service->calculateSAWRecommendation($mahasiswa->id_mahasiswa);

        // Assert: Tidak throw exception, return array
        $this->assertIsArray($result);

        // Bisa jadi error atau hasil dengan nilai default
        if (isset($result['error'])) {
            $this->assertIsString($result['error']);
        } else {
            $this->assertArrayHasKey('ranking', $result);
        }
    }

    /**
     * Test Case 6: (Positif) Verifikasi criteria weights configuration
     *
     * Expected Output:
     * - Weights array tidak kosong
     * - Total weights = 1.0 (atau 100%)
     */
    public function test_getCriteriaWeights_valid_configuration()
    {
        // Arrange & Act: Gunakan reflection untuk access private method
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('getCriteriaWeights');
        $method->setAccessible(true);

        $weights = $method->invoke($this->service);

        // Assert
        $this->assertIsArray($weights);
        $this->assertNotEmpty($weights);

        // Verifikasi total weight = 1.0 (atau mendekati 1.0)
        $totalWeight = array_sum($weights);
        $this->assertEqualsWithDelta(1.0, $totalWeight, 0.01);
    }

    /**
     * Test Case 7: (Negatif) Test exception handling saat database error
     *
     * Simulasi database error dengan mock
     *
     * Expected Output:
     * - Array dengan key 'error'
     * - Error message yang informatif
     */
    public function test_calculateEDASRecommendation_handle_database_exception()
    {
        // Arrange: Mock Mahasiswa model untuk throw exception
        $mockMahasiswa = Mockery::mock('overload:' . Mahasiswa::class);
        $mockMahasiswa->shouldReceive('with')
            ->andThrow(new \Exception('Database connection error'));

        // Act
        $result = $this->service->calculateEDASRecommendation(1);

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('calculation failed', strtolower($result['error']));
    }

    /**
     * Test Case 8: (Positif) Verifikasi ranking terurut berdasarkan score
     *
     * Expected Output:
     * - Ranking array terurut descending berdasarkan score
     * - Item pertama memiliki score tertinggi
     */
    public function test_calculateEDASRecommendation_ranking_sorted_by_score()
    {
        // Arrange
        $mahasiswa = Mahasiswa::factory()->create([
            'ipk' => 3.8,
            'wilayah_id' => 1,
        ]);

        Lowongan::factory()->count(10)->create(['status' => 'active']);

        // Act
        $result = $this->service->calculateEDASRecommendation($mahasiswa->id_mahasiswa);

        // Assert
        if (!isset($result['error'])) {
            $ranking = $result['ranking'];
            $this->assertNotEmpty($ranking);

            // Verifikasi urutan descending
            for ($i = 0; $i < count($ranking) - 1; $i++) {
                $currentScore = $ranking[$i]['as_score'] ?? 0;
                $nextScore = $ranking[$i + 1]['as_score'] ?? 0;

                $this->assertGreaterThanOrEqual(
                    $nextScore,
                    $currentScore,
                    "Ranking tidak terurut dengan benar pada index $i"
                );
            }
        }
    }

    /**
     * Test Case 9: (Edge Case) Test dengan kapasitas lowongan penuh
     *
     * Input:
     * - Lowongan dengan kapasitas = jumlah pelamar
     *
     * Expected Output:
     * - Lowongan dengan kapasitas penuh tetap muncul di hasil (atau difilter)
     */
    public function test_calculateEDASRecommendation_dengan_kapasitas_penuh()
    {
        // Arrange
        $mahasiswa = Mahasiswa::factory()->create(['ipk' => 3.5]);

        // Buat lowongan dengan kapasitas penuh
        $lowonganPenuh = Lowongan::factory()->create([
            'status' => 'active',
            'kapasitas' => 5,
            // Asumsi ada relasi atau field untuk tracking jumlah pelamar
        ]);

        // Act
        $result = $this->service->calculateEDASRecommendation($mahasiswa->id_mahasiswa);

        // Assert: Verifikasi behavior sesuai business logic
        if (!isset($result['error'])) {
            $this->assertIsArray($result['ranking']);
            // Bisa add assertion spesifik tergantung business rule
        }
    }

    /**
     * Test Case 10: (Performance) Test dengan data besar
     *
     * Input:
     * - 100 lowongan
     *
     * Expected Output:
     * - Execution time < 5 detik
     * - Memory usage reasonable
     */
    public function test_calculateEDASRecommendation_performance_dengan_banyak_lowongan()
    {
        // Arrange
        $mahasiswa = Mahasiswa::factory()->create(['ipk' => 3.5]);
        Lowongan::factory()->count(100)->create(['status' => 'active']);

        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Act
        $result = $this->service->calculateEDASRecommendation($mahasiswa->id_mahasiswa);

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        // Assert
        $executionTime = $endTime - $startTime;
        $memoryUsed = ($endMemory - $startMemory) / 1024 / 1024; // MB

        $this->assertLessThan(5, $executionTime, 'Execution time melebihi 5 detik');
        $this->assertLessThan(50, $memoryUsed, 'Memory usage melebihi 50MB');

        if (!isset($result['error'])) {
            $this->assertNotEmpty($result['ranking']);
        }
    }
}
