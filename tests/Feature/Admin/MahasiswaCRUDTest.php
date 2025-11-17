<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Feature Test: Admin CRUD Mahasiswa
 *
 * Test seluruh operasi CRUD mahasiswa oleh admin
 *
 * Command untuk menjalankan:
 * php artisan test --filter MahasiswaCRUDTest
 * php artisan test tests/Feature/Admin/MahasiswaCRUDTest.php
 */
class MahasiswaCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup user admin
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'level' => 'superadmin',
        ]);
    }

    /**
     * Test Case 1: Admin dapat melihat daftar mahasiswa
     */
    public function test_admin_dapat_melihat_daftar_mahasiswa()
    {
        // Arrange
        $this->actingAs($this->admin);

        Mahasiswa::factory()->count(5)->create();

        // Act
        $response = $this->get('/dataMhs');

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('mahasiswa'); // Verifikasi view punya data mahasiswa
    }

    /**
     * Test Case 2: Admin berhasil menambah mahasiswa baru
     *
     * Expected:
     * - Data tersimpan di database m_mahasiswa
     * - User account dibuat di m_users
     * - Redirect dengan success message
     */
    public function test_admin_berhasil_menambah_mahasiswa_baru()
    {
        // Arrange
        $this->actingAs($this->admin);

        $kelas = Kelas::factory()->create();

        $mahasiswaData = [
            'nim' => '2141720099',
            'nama' => 'Mahasiswa Test Baru',
            'email' => 'mhstest@example.com',
            'no_hp' => '081234567890',
            'kelas_id' => $kelas->id_kelas,
            'ipk' => 3.5,
            'password' => 'password123',
        ];

        // Act
        $response = $this->post('/dataMhs', $mahasiswaData);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verifikasi data mahasiswa tersimpan
        $this->assertDatabaseHas('m_mahasiswa', [
            'nim' => '2141720099',
            'nama' => 'Mahasiswa Test Baru',
            'email' => 'mhstest@example.com',
        ]);

        // Verifikasi user account dibuat
        $this->assertDatabaseHas('m_users', [
            'email' => 'mhstest@example.com',
            'level' => 'mahasiswa',
        ]);
    }

    /**
     * Test Case 3: Admin tidak bisa menambah mahasiswa dengan NIM duplikat
     */
    public function test_admin_tidak_bisa_menambah_mahasiswa_nim_duplikat()
    {
        // Arrange
        $this->actingAs($this->admin);

        $existingMahasiswa = Mahasiswa::factory()->create([
            'nim' => '2141720001',
        ]);

        $mahasiswaData = [
            'nim' => '2141720001', // NIM sama
            'nama' => 'Mahasiswa Duplikat',
            'email' => 'duplicate@example.com',
            'no_hp' => '081234567890',
            'password' => 'password123',
        ];

        // Act
        $response = $this->post('/dataMhs', $mahasiswaData);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHasErrors('nim'); // Validation error untuk NIM
    }

    /**
     * Test Case 4: Admin berhasil update data mahasiswa
     */
    public function test_admin_berhasil_update_data_mahasiswa()
    {
        // Arrange
        $this->actingAs($this->admin);

        $mahasiswa = Mahasiswa::factory()->create([
            'nama' => 'Nama Lama',
            'ipk' => 3.0,
        ]);

        $updateData = [
            'nama' => 'Nama Baru Updated',
            'ipk' => 3.8,
            'no_hp' => '081234567890',
        ];

        // Act
        $response = $this->put("/dataMhs/{$mahasiswa->id_mahasiswa}", $updateData);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verifikasi perubahan di database
        $this->assertDatabaseHas('m_mahasiswa', [
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'nama' => 'Nama Baru Updated',
            'ipk' => 3.8,
        ]);
    }

    /**
     * Test Case 5: Admin berhasil menghapus mahasiswa
     *
     * Expected:
     * - Data mahasiswa dihapus (soft delete atau hard delete)
     * - User account juga dihapus
     */
    public function test_admin_berhasil_menghapus_mahasiswa()
    {
        // Arrange
        $this->actingAs($this->admin);

        $mahasiswa = Mahasiswa::factory()->create();
        $userId = $mahasiswa->user_id;

        // Act
        $response = $this->delete("/dataMhs/{$mahasiswa->id_mahasiswa}");

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verifikasi mahasiswa terhapus
        $this->assertDatabaseMissing('m_mahasiswa', [
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
        ]);

        // Verifikasi user account juga terhapus (opsional, tergantung business logic)
        $this->assertDatabaseMissing('m_users', [
            'id_user' => $userId,
        ]);
    }

    /**
     * Test Case 6: Validation - NIM required
     */
    public function test_tambah_mahasiswa_validation_nim_required()
    {
        // Arrange
        $this->actingAs($this->admin);

        $mahasiswaData = [
            'nama' => 'Test',
            'email' => 'test@example.com',
            // nim tidak ada
        ];

        // Act
        $response = $this->post('/dataMhs', $mahasiswaData);

        // Assert
        $response->assertSessionHasErrors('nim');
    }

    /**
     * Test Case 7: Validation - Email harus valid format
     */
    public function test_tambah_mahasiswa_validation_email_invalid()
    {
        // Arrange
        $this->actingAs($this->admin);

        $mahasiswaData = [
            'nim' => '2141720099',
            'nama' => 'Test',
            'email' => 'invalid-email', // Email tidak valid
            'password' => 'password123',
        ];

        // Act
        $response = $this->post('/dataMhs', $mahasiswaData);

        // Assert
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test Case 8: Admin dapat search mahasiswa by NIM atau nama
     */
    public function test_admin_dapat_search_mahasiswa()
    {
        // Arrange
        $this->actingAs($this->admin);

        Mahasiswa::factory()->create([
            'nim' => '2141720001',
            'nama' => 'John Doe',
        ]);

        Mahasiswa::factory()->create([
            'nim' => '2141720002',
            'nama' => 'Jane Smith',
        ]);

        // Act: Search by nama
        $response = $this->get('/dataMhs?search=John');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
    }

    /**
     * Test Case 9: Guest tidak bisa akses halaman admin
     */
    public function test_guest_tidak_bisa_akses_halaman_admin()
    {
        // Act
        $response = $this->get('/dataMhs');

        // Assert
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test Case 10: Mahasiswa tidak bisa akses halaman admin
     */
    public function test_mahasiswa_tidak_bisa_akses_halaman_admin()
    {
        // Arrange
        $mahasiswaUser = User::factory()->create([
            'level' => 'mahasiswa',
        ]);
        $this->actingAs($mahasiswaUser);

        // Act
        $response = $this->get('/dataMhs');

        // Assert
        $response->assertStatus(403); // Forbidden
    }
}
