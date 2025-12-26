<?php

namespace Tests\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Admin Periode Validation
 */
class AdminPeriodeValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->token = $this->admin->createToken('test-token')->plainTextToken;
    }

    public function test_api_periode_validation_waktu_required()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/periode', [
            'tgl_mulai' => '2025-01-01',
            'tgl_selesai' => '2025-06-30',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['waktu']);
    }

    public function test_api_periode_validation_tgl_mulai_date_format()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/periode', [
            'waktu' => 'Test Periode',
            'tgl_mulai' => 'not-a-date',
            'tgl_selesai' => '2025-06-30',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tgl_mulai']);
    }

    public function test_api_periode_validation_tgl_selesai_after_tgl_mulai()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/periode', [
            'waktu' => 'Test Periode',
            'tgl_mulai' => '2025-06-30',
            'tgl_selesai' => '2025-01-01', // Before tgl_mulai
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['tgl_selesai']);
    }

    public function test_api_periode_validation_all_fields_empty()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/periode', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['waktu', 'tgl_mulai', 'tgl_selesai']);
    }

    public function test_api_periode_get_nonexistent_id()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/periode/999999');

        $response->assertStatus(404);
    }
}
