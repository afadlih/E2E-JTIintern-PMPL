<?php

namespace Tests\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Login Authentication
 *
 * Test REST API endpoint untuk login authentication
 *
 * Command untuk menjalankan:
 * php artisan test --filter LoginApiTest
 * php artisan test tests/Api/Auth/LoginApiTest.php
 */
class LoginApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: POST /api/login dengan credentials benar (Admin)
     */
    public function test_api_login_admin_berhasil()
    {
        // Arrange
        $user = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user',
                    'token',
                    'role',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'role' => 'admin',
                ],
            ]);

        $this->assertNotNull($response->json('data.token'));
    }

    /**
     * Test Case 2: POST /api/login dengan credentials salah
     */
    public function test_api_login_credentials_salah()
    {
        // Arrange
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * Test Case 3: POST /api/login dengan mahasiswa credentials
     */
    public function test_api_login_mahasiswa_berhasil()
    {
        // Arrange
        $user = User::factory()->mahasiswa()->create([
            'email' => 'mahasiswa@example.com',
            'password' => bcrypt('password123'),
        ]);

        Mahasiswa::factory()->create([
            'id_user' => $user->id_user,
            'nim' => 2141720001,
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'mahasiswa@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'user',
                    'token',
                    'role',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'role' => 'mahasiswa',
                ],
            ]);
    }

    /**
     * Test Case 4: POST /api/login tanpa email
     */
    public function test_api_login_tanpa_username()
    {
        // Act
        $response = $this->postJson('/api/login', [
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test Case 5: POST /api/login tanpa password
     */
    public function test_api_login_tanpa_password()
    {
        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test Case 6: POST /api/logout dengan valid token
     */
    public function test_api_logout_berhasil()
    {
        // Arrange
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Logged out successfully',
            ]);

        // Verify token is deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id_user,
        ]);
    }

    /**
     * Test Case 7: GET /api/user dengan valid token
     */
    public function test_api_get_authenticated_user()
    {
        // Arrange
        $user = User::factory()->admin()->create([
            'email' => 'testuser@example.com',
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'id_user',
                'email',
                'role',
            ])
            ->assertJson([
                'email' => 'testuser@example.com',
                'role' => 'admin',
            ]);
    }

    /**
     * Test Case 8: GET /api/user tanpa token (unauthenticated)
     */
    public function test_api_get_user_tanpa_token()
    {
        // Act
        $response = $this->getJson('/api/user');

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test Case 9: POST /api/login dengan dosen credentials
     */
    public function test_api_login_dosen_berhasil()
    {
        // Arrange
        $user = User::factory()->dosen()->create([
            'email' => 'dosen@example.com',
            'password' => bcrypt('password123'),
        ]);

        Dosen::factory()->create([
            'user_id' => $user->id_user,
        ]);

        // Act
        $response = $this->postJson('/api/login', [
            'email' => 'dosen@example.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'role' => 'dosen',
                ],
            ]);
    }

    /**
     * Test Case 10: POST /api/logout tanpa token (unauthenticated)
     */
    public function test_api_logout_tanpa_token()
    {
        // Act
        $response = $this->postJson('/api/logout');

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
