<?php

namespace Tests\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TokenManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_access_without_token()
    {
        $response = $this->getJson('/api/admin/mahasiswa');

        $response->assertStatus(401);
    }

    public function test_api_access_with_empty_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer ')
                         ->getJson('/api/admin/mahasiswa');

        $response->assertStatus(401);
    }

    public function test_api_access_with_malformed_token()
    {
        $response = $this->withHeader('Authorization', 'Bearer abc123xyz')
                         ->getJson('/api/admin/mahasiswa');

        $response->assertStatus(401);
    }

    public function test_api_logout_without_token()
    {
        $response = $this->postJson('/api/logout');

        $response->assertStatus(401);
    }

    public function test_api_get_user_without_token()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
