<?php

namespace Tests\Api\Mahasiswa;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\Periode;
use App\Models\Lamaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * API Test: Mahasiswa Lowongan Endpoints
 *
 * Test REST API endpoints untuk mahasiswa lowongan
 *
 * Command untuk menjalankan:
 * php artisan test --filter MahasiswaLowonganApiTest
 * php artisan test tests/Api/Mahasiswa/MahasiswaLowonganApiTest.php
 */
class MahasiswaLowonganApiTest extends TestCase
{
    use RefreshDatabase;

    protected $mahasiswa;
    protected $user;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup authenticated mahasiswa
        $this->user = User::factory()->mahasiswa()->create([
            'email' => 'mahasiswa@example.com',
        ]);

        $this->mahasiswa = Mahasiswa::factory()->create([
            'id_user' => $this->user->id_user,
            'nim' => 2141720001,
            'ipk' => 3.5,
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * Test Case 1: GET /api/mahasiswa/lowongan - Get list lowongan
     */
    public function test_api_get_list_lowongan()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create(['status' => 'aktif']);

        Lowongan::factory()->count(3)->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
            'status' => 'aktif',
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/mahasiswa/lowongan');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id_lowongan',
                        'nama_lowongan',
                        'deskripsi',
                        'kuota',
                        'status',
                    ],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test Case 2: GET /api/mahasiswa/lowongan/{id} - Get lowongan detail
     */
    public function test_api_get_lowongan_detail()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
            'nama_lowongan' => 'Web Developer Intern',
            'kuota' => 5,
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/mahasiswa/lowongan/{$lowongan->id_lowongan}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id_lowongan',
                    'nama_lowongan',
                    'deskripsi',
                    'kuota',
                    'perusahaan',
                ],
            ])
            ->assertJson([
                'data' => [
                    'nama_lowongan' => 'Web Developer Intern',
                    'kuota' => 5,
                ],
            ]);
    }

    /**
     * Test Case 3: POST /api/mahasiswa/apply/{lowongan_id} - Apply lowongan berhasil
     */
    public function test_api_apply_lowongan_berhasil()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create(['status' => 'aktif']);

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
            'kuota' => 5,
            'status' => 'aktif',
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/mahasiswa/apply/{$lowongan->id_lowongan}");

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Lamaran berhasil dikirim',
            ]);

        // Verify database
        $this->assertDatabaseHas('t_lamaran', [
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowongan->id_lowongan,
            'status' => 'pending',
        ]);
    }

    /**
     * Test Case 4: POST /api/mahasiswa/apply/{lowongan_id} - Prevent duplicate application
     */
    public function test_api_apply_lowongan_duplicate()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create(['status' => 'aktif']);

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
            'status' => 'aktif',
        ]);

        // Sudah apply sebelumnya
        Lamaran::factory()->create([
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowongan->id_lowongan,
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/mahasiswa/apply/{$lowongan->id_lowongan}");

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Anda sudah melamar lowongan ini',
            ]);
    }

    /**
     * Test Case 5: POST /api/mahasiswa/apply/{lowongan_id} - Kuota penuh
     */
    public function test_api_apply_lowongan_kuota_penuh()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create(['status' => 'aktif']);

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
            'kuota' => 2,
            'status' => 'aktif',
        ]);

        // Fill kuota dengan mahasiswa lain
        $mahasiswaLain = Mahasiswa::factory()->count(2)->create();
        foreach ($mahasiswaLain as $mhs) {
            Lamaran::factory()->create([
                'id_mahasiswa' => $mhs->id_mahasiswa,
                'id_lowongan' => $lowongan->id_lowongan,
                'status' => 'diterima',
            ]);
        }

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson("/api/mahasiswa/apply/{$lowongan->id_lowongan}");

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Kuota lowongan sudah penuh',
            ]);
    }

    /**
     * Test Case 6: GET /api/mahasiswa/applications/user - Get user applications
     */
    public function test_api_get_user_applications()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
        ]);

        Lamaran::factory()->count(2)->create([
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowongan->id_lowongan,
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/mahasiswa/applications/user');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id_lamaran',
                        'status',
                        'lowongan',
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    /**
     * Test Case 7: GET /api/mahasiswa/lowongan/{id}/application-status - Check application status
     */
    public function test_api_check_application_status()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
        ]);

        Lamaran::factory()->create([
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowongan->id_lowongan,
            'status' => 'pending',
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson("/api/mahasiswa/lowongan/{$lowongan->id_lowongan}/application-status");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'has_applied' => true,
                    'application_status' => 'pending',
                ],
            ]);
    }

    /**
     * Test Case 8: GET /api/mahasiswa/active-internship - Check active internship
     */
    public function test_api_check_active_internship()
    {
        // Arrange
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
        ]);

        Lamaran::factory()->create([
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowongan->id_lowongan,
            'status' => 'diterima',
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/mahasiswa/active-internship');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'has_active_internship' => true,
                ],
            ]);
    }

    /**
     * Test Case 9: GET /api/mahasiswa/lowongan - Unauthorized (dosen mencoba akses)
     */
    public function test_api_lowongan_unauthorized_role()
    {
        // Arrange
        $dosenUser = User::factory()->dosen()->create();
        $dosenToken = $dosenUser->createToken('test-token')->plainTextToken;

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $dosenToken,
        ])->getJson('/api/mahasiswa/lowongan');

        // Assert
        $response->assertStatus(403); // Forbidden
    }

    /**
     * Test Case 10: GET /api/mahasiswa/lowongan/active-period - Get active period
     */
    public function test_api_get_active_period()
    {
        // Arrange
        Periode::factory()->active()->create([
            'waktu' => 'Periode Genap 2024',
        ]);

        // Act
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/mahasiswa/lowongan/active-period');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'periode_id',
                    'waktu',
                    'tgl_mulai',
                    'tgl_selesai',
                ],
            ])
            ->assertJson([
                'data' => [
                    'waktu' => 'Periode Genap 2024',
                ],
            ]);
    }
}
