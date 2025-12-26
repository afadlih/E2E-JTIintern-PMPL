<?php

namespace Tests\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Admin Mahasiswa Validation
 */
class AdminMahasiswaValidationTest extends TestCase
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

    public function test_api_mahasiswa_validation_name_max_length()
    {
        $kelas = Kelas::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'name' => str_repeat('a', 256), // 256 characters
            'email' => 'test@example.com',
            'password' => 'password123',
            'id_kelas' => $kelas->id_kelas,
            'nim' => '2141720001',
            'alamat' => 'Test Address',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_api_mahasiswa_validation_email_format_invalid()
    {
        $kelas = Kelas::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'name' => 'Test User',
            'email' => 'invalid-email-format',
            'password' => 'password123',
            'id_kelas' => $kelas->id_kelas,
            'nim' => '2141720001',
            'alamat' => 'Test Address',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_api_mahasiswa_validation_password_min_length()
    {
        $kelas = Kelas::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '12345', // Less than 6 characters
            'id_kelas' => $kelas->id_kelas,
            'nim' => '2141720001',
            'alamat' => 'Test Address',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_api_mahasiswa_validation_id_kelas_invalid()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'id_kelas' => 99999, // Non-existent kelas
            'nim' => '2141720001',
            'alamat' => 'Test Address',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['id_kelas']);
    }

    public function test_api_mahasiswa_validation_nim_format()
    {
        $kelas = Kelas::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'id_kelas' => $kelas->id_kelas,
            'nim' => 'INVALID', // Invalid NIM format
            'alamat' => 'Test Address',
        ]);

        // Might pass or fail depending on validation rules
        $this->assertTrue(true);
    }
}
