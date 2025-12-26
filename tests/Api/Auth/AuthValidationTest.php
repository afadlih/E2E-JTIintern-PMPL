<?php

namespace Tests\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Authentication Validation
 */
class AuthValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_login_validation_email_format()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'not-an-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_api_login_validation_email_required()
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_api_login_validation_password_required()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_api_login_with_non_existent_user()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_api_get_user_without_authentication()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}
