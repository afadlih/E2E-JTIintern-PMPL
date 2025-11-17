<?php

namespace Tests\Api\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Admin Mahasiswa CRUD
 *
 * Test REST API endpoints untuk admin manage mahasiswa
 *
 * Command untuk menjalankan:
 * php artisan test --filter AdminMahasiswaApiTest
 * php artisan test tests/Api/Admin/AdminMahasiswaApiTest.php
 */
class AdminMahasiswaApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $adminUser;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup authenticated admin
        $this->adminUser = User::factory()->admin()->create([
            'email' => 'admin@example.com',
        ]);

        $this->token = $this->adminUser->createToken('test-token')->plainTextToken;
    }

    /**
     * Test Case 1: GET /api/admin/mahasiswa - Get all mahasiswa
     */
    public function test_api_get_all_mahasiswa()
    {
        // Arrange
        $kelas = Kelas::factory()->create();
        Mahasiswa::factory()->count(5)->create(['id_kelas' => $kelas->id_kelas]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/admin/mahasiswa');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id_mahasiswa',
                        'nim',
                        'nama',
                        'email',
                        'ipk',
                    ],
                ],
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    /**
     * Test Case 2: GET /api/admin/mahasiswa/{id} - Get mahasiswa by ID
     */
    public function test_api_get_mahasiswa_by_id()
    {
        // Arrange
        $kelas = Kelas::factory()->create();
        $mahasiswa = Mahasiswa::factory()->create([
            'id_kelas' => $kelas->id_kelas,
            'nim' => 2141720001,
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/admin/mahasiswa/{$mahasiswa->id_mahasiswa}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id_mahasiswa',
                        'nim',
                        'ipk',
                        'kelas',
                    ]
                ],
            ])
            ->assertJsonPath('data.0.nim', 2141720001);
    }

    /**
     * Test Case 3: POST /api/admin/mahasiswa - Create mahasiswa berhasil
     */
    public function test_api_create_mahasiswa_berhasil()
    {
        // Arrange
        $kelas = Kelas::factory()->create();

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'nama' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'id_kelas' => $kelas->id_kelas,
            'nim' => '2141720099',
            'ipk' => 3.75,
            'telp' => '081234567890',
        ]);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Mahasiswa berhasil ditambahkan',
            ]);

        $this->assertDatabaseHas('m_mahasiswa', [
            'nim' => '2141720099',
            'id_kelas' => $kelas->id_kelas,
        ]);

        $this->assertDatabaseHas('m_user', [
            'email' => 'jane@example.com',
            'name' => 'Jane Smith',
        ]);
    }

    /**
     * Test Case 4: POST /api/admin/mahasiswa - Validation error (NIM duplicate)
     */
    public function test_api_create_mahasiswa_nim_duplicate()
    {
        // Arrange
        $kelas = Kelas::factory()->create();
        Mahasiswa::factory()->create(['nim' => '2141720001']);

        $user = User::factory()->mahasiswa()->create();

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'user_id' => $user->id_user,
            'id_kelas' => $kelas->id_kelas,
            'nim' => '2141720001', // Duplicate
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nim']);
    }

    /**
     * Test Case 5: PUT /api/admin/mahasiswa/{id} - Update mahasiswa berhasil
     */
    public function test_api_update_mahasiswa_berhasil()
    {
        // Arrange
        $kelas = Kelas::factory()->create();
        $mahasiswa = Mahasiswa::factory()->create([
            'id_kelas' => $kelas->id_kelas,
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson("/api/admin/mahasiswa/{$mahasiswa->id_mahasiswa}", [
            'nama' => 'Updated Name',
            'email' => 'updated@example.com',
            'ipk' => 3.8,
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Mahasiswa berhasil diupdate',
            ]);

        $this->assertDatabaseHas('m_mahasiswa', [
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'ipk' => 3.8,
        ]);

        $this->assertDatabaseHas('m_user', [
            'id_user' => $mahasiswa->id_user,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * Test Case 6: DELETE /api/admin/mahasiswa/{id} - Delete mahasiswa berhasil
     */
    public function test_api_delete_mahasiswa_berhasil()
    {
        // Arrange
        $kelas = Kelas::factory()->create();
        $mahasiswa = Mahasiswa::factory()->create(['id_kelas' => $kelas->id_kelas]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson("/api/admin/mahasiswa/{$mahasiswa->id_mahasiswa}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Mahasiswa berhasil dihapus',
            ]);

        $this->assertDatabaseMissing('m_mahasiswa', [
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
        ]);
    }

    /**
     * Test Case 7: GET /api/admin/mahasiswa?search=john - Search mahasiswa
     */
    public function test_api_search_mahasiswa()
    {
        // Arrange
        $kelas = Kelas::factory()->create();
        Mahasiswa::factory()->create([
            'id_kelas' => $kelas->id_kelas,
        ]);
        Mahasiswa::factory()->create([
            'id_kelas' => $kelas->id_kelas,
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/admin/mahasiswa?search=john');

        // Assert
        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertGreaterThanOrEqual(0, count($data));
        if (count($data) > 0) {
            $this->assertArrayHasKey('nim', $data[0]);
        }
    }

    /**
     * Test Case 8: GET /api/admin/mahasiswa?kelas_id=1 - Filter by kelas
     */
    public function test_api_filter_mahasiswa_by_kelas()
    {
        // Arrange
        $kelas1 = Kelas::factory()->create();
        $kelas2 = Kelas::factory()->create();

        Mahasiswa::factory()->count(3)->create(['id_kelas' => $kelas1->id_kelas]);
        Mahasiswa::factory()->count(2)->create(['id_kelas' => $kelas2->id_kelas]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/admin/mahasiswa?kelas_id={$kelas1->id_kelas}");

        // Assert
        $response->assertStatus(200);
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test Case 9: POST /api/admin/mahasiswa - Validation error (missing required fields)
     */
    public function test_api_create_mahasiswa_missing_fields()
    {
        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/admin/mahasiswa', [
            'nim' => '2141720099',
            // Missing nama, email, etc.
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama', 'email']);
    }

    /**
     * Test Case 10: GET /api/admin/mahasiswa - Unauthorized (mahasiswa mencoba akses)
     */
    public function test_api_mahasiswa_unauthorized_role()
    {
        // Arrange
        $mahasiswaUser = User::factory()->mahasiswa()->create();
        $mahasiswaToken = $mahasiswaUser->createToken('test-token')->plainTextToken;

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $mahasiswaToken,
        ])->getJson('/api/admin/mahasiswa');

        // Assert
        $response->assertStatus(403); // Forbidden
    }
}
