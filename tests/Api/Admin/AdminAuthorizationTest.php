<?php

namespace Tests\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Admin Authorization
 */
class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_admin_mahasiswa_unauthorized_no_token()
    {
        $response = $this->getJson('/api/admin/mahasiswa');

        $response->assertStatus(401);
    }

    public function test_api_admin_mahasiswa_unauthorized_invalid_token()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid-token-here',
        ])->getJson('/api/admin/mahasiswa');

        $response->assertStatus(401);
    }

    public function test_api_periode_unauthorized_no_token()
    {
        $response = $this->getJson('/api/periode');

        $response->assertStatus(401);
    }

    public function test_api_periode_post_unauthorized()
    {
        $response = $this->postJson('/api/periode', [
            'waktu' => 'Test',
            'tgl_mulai' => '2025-01-01',
            'tgl_selesai' => '2025-06-30',
        ]);

        $response->assertStatus(401);
    }

    public function test_api_admin_endpoint_with_expired_token()
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        // Delete the token to simulate expiry
        $admin->tokens()->delete();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/admin/mahasiswa');

        $response->assertStatus(401);
    }
}
