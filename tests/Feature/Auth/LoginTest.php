<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Feature Test: Login Authentication
 *
 * Test seluruh flow authentication dari request sampai response
 *
 * Command untuk menjalankan:
 * php artisan test --filter LoginTest
 * php artisan test tests/Feature/Auth/LoginTest.php
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: Login berhasil dengan credentials yang benar (Admin)
     *
     * Scenario:
     * 1. Buat user admin di database
     * 2. Submit POST request ke /login dengan email & password benar
     * 3. Verifikasi redirect ke /dashboard
     * 4. Verifikasi user sudah authenticated
     */
    public function test_admin_dapat_login_dengan_credentials_benar()
    {
        // Arrange: Buat user admin
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'level' => 'superadmin',
        ]);

        // Act: Submit login form
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        // Assert: Verifikasi response
        $response->assertStatus(302); // Redirect
        $response->assertRedirect('/dashboard');

        // Verifikasi user sudah authenticated
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test Case 2: Login gagal dengan password salah
     *
     * Expected:
     * - Redirect kembali ke login page
     * - Ada error message
     * - User tidak authenticated
     */
    public function test_login_gagal_dengan_password_salah()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Submit dengan password salah
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'wrongpassword',
        ]);

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('/'); // Redirect ke login
        $response->assertSessionHasErrors(); // Ada error message

        // Verifikasi user TIDAK authenticated
        $this->assertGuest();
    }

    /**
     * Test Case 3: Login mahasiswa redirect ke mahasiswa dashboard
     */
    public function test_mahasiswa_redirect_ke_mahasiswa_dashboard()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'mahasiswa@test.com',
            'password' => Hash::make('password123'),
            'level' => 'mahasiswa',
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'mahasiswa@test.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/mahasiswa/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test Case 4: Login dosen redirect ke dosen dashboard
     */
    public function test_dosen_redirect_ke_dosen_dashboard()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'dosen@test.com',
            'password' => Hash::make('password123'),
            'level' => 'dosen',
        ]);

        // Act
        $response = $this->post('/login', [
            'email' => 'dosen@test.com',
            'password' => 'password123',
        ]);

        // Assert
        $response->assertRedirect('/dosen/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test Case 5: Validation error - email required
     */
    public function test_login_validation_email_required()
    {
        // Act: Submit tanpa email
        $response = $this->post('/login', [
            'password' => 'password123',
        ]);

        // Assert
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test Case 6: Validation error - password required
     */
    public function test_login_validation_password_required()
    {
        // Act: Submit tanpa password
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
        ]);

        // Assert
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /**
     * Test Case 7: Logout functionality
     */
    public function test_user_dapat_logout()
    {
        // Arrange: Login dulu
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Logout
        $response = $this->post('/logout');

        // Assert
        $response->assertStatus(302);
        $this->assertGuest();
    }

    /**
     * Test Case 8: Remember me functionality
     */
    public function test_login_dengan_remember_me()
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Login dengan remember me
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        // Assert
        $response->assertStatus(302);
        $this->assertAuthenticatedAs($user);

        // Verifikasi remember token ada
        $this->assertNotNull($user->fresh()->remember_token);
    }
}
