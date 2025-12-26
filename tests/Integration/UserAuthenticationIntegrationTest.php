<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\Kelas;

/**
 * Integration Tests untuk User Authentication Flow
 * Testing integrasi antara User, Mahasiswa, dan Authentication
 *
 * @group integration
 * @group integration-auth
 */
class UserAuthenticationIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test integrasi: User creation dengan role mahasiswa
     *
     * @group integration
     */
    public function test_create_user_with_mahasiswa_role(): void
    {
        $user = User::factory()->create([
            'username' => 'mahasiswa01',
            'email' => 'mhs01@example.com',
            'level' => 'MHS',
        ]);

        $this->assertDatabaseHas('m_user', [
            'username' => 'mahasiswa01',
            'email' => 'mhs01@example.com',
            'level' => 'MHS',
        ]);

        $this->assertEquals('MHS', $user->level);
    }

    /**
     * Test integrasi: Login flow complete
     *
     * @group integration
     * @group integration-api
     */
    public function test_complete_login_flow(): void
    {
        // Create user
        $user = User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'level' => 'MHS',
        ]);

        // Attempt login via API
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user'
        ]);

        $this->assertAuthenticatedAs($user, 'sanctum');
    }

    /**
     * Test integrasi: Logout removes token
     *
     * @group integration
     */
    public function test_logout_removes_token(): void
    {
        $user = User::factory()->create([
            'level' => 'MHS',
        ]);

        // Create token
        $token = $user->createToken('test-token')->plainTextToken;

        // Login with token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertOk();

        // Token should be deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    /**
     * Test integrasi: Failed login attempt
     *
     * @group integration
     */
    public function test_failed_login_attempt(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnauthorized();
        $this->assertGuest();
    }

    /**
     * Test integrasi: Multiple login attempts tracking
     *
     * @group integration
     */
    public function test_multiple_login_attempts(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Multiple failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'wrong',
            ])->assertUnauthorized();
        }

        // Successful login should still work
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertOk();
    }

    /**
     * Test integrasi: Token expiration (if implemented)
     *
     * @group integration
     */
    public function test_token_usage(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Use token to access protected route
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertOk();
        $response->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
