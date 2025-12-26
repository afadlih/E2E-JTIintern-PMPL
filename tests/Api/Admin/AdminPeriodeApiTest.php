<?php

namespace Tests\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Admin Periode Endpoints
 *
 * Test REST API endpoints untuk manajemen periode oleh admin
 *
 * Command untuk menjalankan:
 * php artisan test --filter AdminPeriodeApiTest
 */
class AdminPeriodeApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup authenticated admin
        $this->admin = User::factory()->admin()->create();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    /**
     * Test Case 1: GET /api/periode - Get list periode
     */
    public function test_api_get_periode_list()
    {
        // Arrange
        Periode::factory()->count(3)->create();

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/periode');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'periode_id',
                        'waktu',
                        'tgl_mulai',
                        'tgl_selesai',
                        'status',
                    ],
                ],
            ]);

        $this->assertGreaterThanOrEqual(3, count($response->json('data')));
    }

    /**
     * Test Case 2: POST /api/periode - Create periode berhasil
     */
    public function test_api_create_periode_berhasil()
    {
        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/periode', [
            'waktu' => 'Januari - Juni 2025',
            'tgl_mulai' => '2025-01-01',
            'tgl_selesai' => '2025-06-30',
            'status' => 'aktif',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('m_periode', [
            'waktu' => 'Januari - Juni 2025',
            'status' => 'aktif',
        ]);
    }

    /**
     * Test Case 3: POST /api/periode - Validation error (missing fields)
     */
    public function test_api_create_periode_validation_error()
    {
        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/periode', [
            'waktu' => 'Test Periode',
            // Missing tgl_mulai, tgl_selesai
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tgl_mulai', 'tgl_selesai']);
    }

    /**
     * Test Case 4: GET /api/periode/{id} - Get periode by ID
     */
    public function test_api_get_periode_by_id()
    {
        // Arrange
        $periode = Periode::factory()->create([
            'waktu' => 'Test Periode',
            'status' => 'aktif',
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/periode/{$periode->periode_id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'periode_id' => $periode->periode_id,
                    'waktu' => 'Test Periode',
                    'status' => 'aktif',
                ],
            ]);
    }

    /**
     * Test Case 5: GET /api/periode/{id} - Periode not found
     */
    public function test_api_get_periode_not_found()
    {
        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/periode/99999');

        // Assert
        $response->assertStatus(404);
    }
}
