<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lowongan;
use App\Models\Perusahaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Feature Test: Admin CRUD Lowongan
 *
 * Command:
 * php artisan test --filter LowonganCRUDTest
 */
class LowonganCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    /**
     * Test 1: Admin dapat melihat halaman daftar lowongan
     */
    public function test_admin_dapat_melihat_daftar_lowongan()
    {
        $this->actingAs($this->admin);

        Lowongan::factory()->count(5)->create();

        $response = $this->get('/lowongan');

        $response->assertStatus(200);
        $response->assertViewIs('pages.data_lowongan'); // view yang ditampilkan
    }

    /**
     * Test 2: Admin berhasil menambah lowongan baru
     */
    public function test_admin_berhasil_menambah_lowongan()
    {
        $this->actingAs($this->admin);

        $perusahaan = Perusahaan::factory()->create();

        $data = [
            'judul_lowongan' => 'Backend Developer Intern',
            'id_perusahaan' => $perusahaan->perusahaan_id,
            'kapasitas' => 10,
            'deskripsi' => 'Lowongan untuk posisi backend developer',
        ];

        $response = $this->postJson('/api/admin/lowongan', $data);

        $response->assertStatus(201);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('m_lowongan', [
            'judul_lowongan' => 'Backend Developer Intern',
        ]);
    }

    /**
     * Test 3: Admin tidak bisa tambah lowongan tanpa judul
     */
    public function test_admin_tidak_bisa_menambah_lowongan_tanpa_judul()
    {
        $this->actingAs($this->admin);

        $perusahaan = Perusahaan::factory()->create();

        $data = [
            'id_perusahaan' => $perusahaan->perusahaan_id,
            'kapasitas' => 5,
        ];

        $response = $this->postJson('/api/admin/lowongan', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('judul_lowongan');
    }

    /**
     * Test 4: Admin berhasil update lowongan
     */
    public function test_admin_berhasil_update_lowongan()
    {
        $this->actingAs($this->admin);

        $lowongan = Lowongan::factory()->create([
            'judul_lowongan' => 'Old Title',
            'kapasitas' => 5,
        ]);

        $updateData = [
            'judul_lowongan' => 'Updated Title',
            'kapasitas' => 20,
        ];

        $response = $this->putJson("/api/admin/lowongan/{$lowongan->id_lowongan}", $updateData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('m_lowongan', [
            'id_lowongan' => $lowongan->id_lowongan,
            'judul_lowongan' => 'Updated Title',
            'kapasitas' => 20,
        ]);
    }

    /**
     * Test 5: Admin berhasil menghapus lowongan
     */
    public function test_admin_berhasil_menghapus_lowongan()
    {
        $this->actingAs($this->admin);

        $lowongan = Lowongan::factory()->create();

        $response = $this->deleteJson("/api/admin/lowongan/{$lowongan->id_lowongan}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('m_lowongan', [
            'id_lowongan' => $lowongan->id_lowongan,
        ]);
    }

    /**
     * Test 6: Validasi kapasitas harus angka
     */
    public function test_validation_kapasitas_harus_angka()
    {
        $this->actingAs($this->admin);

        $perusahaan = Perusahaan::factory()->create();

        $data = [
            'judul_lowongan' => 'Test',
            'id_perusahaan' => $perusahaan->perusahaan_id,
            'kapasitas' => 'bukan angka',
        ];

        $response = $this->postJson('/api/admin/lowongan', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('kapasitas');
    }

    /**
     * Test 7: Admin dapat search lowongan
     */
    public function test_admin_dapat_search_lowongan()
    {
        $this->actingAs($this->admin);

        Lowongan::factory()->create(['judul_lowongan' => 'DevOps Intern']);
        Lowongan::factory()->create(['judul_lowongan' => 'Mobile App Developer']);

        $response = $this->get('/dataLowongan?search=DevOps');

        $response->assertStatus(200);
        $response->assertViewIs('pages.data_lowongan');
    }

    /**
     * Test 8: Guest tidak boleh akses halaman lowongan
     */
    public function test_guest_tidak_bisa_akses_halaman_admin_lowongan()
    {
        $response = $this->get('/dataLowongan');

        $response->assertRedirect('/login');
    }

    /**
     * Test 9: Mahasiswa tidak boleh akses halaman lowongan admin
     */
    public function test_mahasiswa_tidak_bisa_akses_halaman_admin_lowongan()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($mahasiswa);

        $response = $this->get('/dataLowongan');

        $response->assertStatus(302);
    }
}
