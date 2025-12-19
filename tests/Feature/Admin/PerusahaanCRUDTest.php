<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Perusahaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class PerusahaanCRUDTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    /**
     * Test Case 1: Admin dapat melihat daftar perusahaan
     */
    public function test_admin_dapat_melihat_daftar_perusahaan()
    {
        $this->actingAs($this->admin);

        Perusahaan::factory()->count(3)->create();

        $response = $this->get('/data-perusahaan');

        $response->assertStatus(200);
        $response->assertViewIs('pages.data_perusahaan');
    }

    /**
     * Test Case 2: Admin berhasil menambah perusahaan
     */
    public function test_admin_berhasil_menambah_perusahaan()
    {
        $this->actingAs($this->admin);

        $data = [
            'nama_perusahaan' => 'PT Teknologi Canggih',
            'email' => 'hr@teknologi.com',
            'contact_person' => '08123456789',
        ];

        $response = $this->postJson('/api/admin/tambah-perusahaan', $data);

        $response->assertStatus(201);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('m_perusahaan', [
            'nama_perusahaan' => 'PT Teknologi Canggih',
            'email' => 'hr@teknologi.com',
        ]);
    }

    /**
     * Test Case 3: Admin tidak bisa tambah perusahaan email duplikat
     */
    public function test_admin_tidak_bisa_menambah_perusahaan_email_duplikat()
    {
        $this->actingAs($this->admin);

        Perusahaan::factory()->create([
            'email' => 'duplikat@mail.com',
        ]);

        $data = [
            'nama_perusahaan' => 'Perusahaan Baru',
            'email' => 'duplikat@mail.com',
            'contact_person' => '08123456789',
        ];

        $response = $this->postJson('/api/admin/perusahaan', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Test Case 4: Admin berhasil update perusahaan
     */
    public function test_admin_berhasil_update_perusahaan()
    {
        $this->actingAs($this->admin);

        $perusahaan = Perusahaan::factory()->create([
            'nama_perusahaan' => 'Nama Lama',
            'contact_person' => '0811111111',
        ]);

        $updateData = [
            'nama_perusahaan' => 'Nama Perusahaan Baru',
            'contact_person' => '0899999999',
        ];

        $response = $this->putJson("/api/admin/perusahaan/{$perusahaan->perusahaan_id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('m_perusahaan', [
            'perusahaan_id' => $perusahaan->perusahaan_id,
            'nama_perusahaan' => 'Nama Perusahaan Baru',
            'contact_person' => '0899999999',
        ]);
    }

    /**
     * Test Case 5: Admin berhasil menghapus perusahaan
     */
    public function test_admin_berhasil_menghapus_perusahaan()
    {
        $this->actingAs($this->admin);

        $perusahaan = Perusahaan::factory()->create();

        $response = $this->deleteJson("/api/admin/perusahaan/{$perusahaan->perusahaan_id}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('m_perusahaan', [
            'perusahaan_id' => $perusahaan->perusahaan_id,
        ]);
    }

    /**
     * Test Case 6: Validasi nama perusahaan wajib
     */
    public function test_validation_nama_perusahaan_required()
    {
        $this->actingAs($this->admin);

        $data = [
            'email' => 'empty@test.com'
        ];

        $response = $this->postJson('/api/admin/perusahaan', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('nama_perusahaan');
    }

    /**
     * Test Case 7: Validasi email harus format valid
     */
    public function test_validation_email_format_invalid()
    {
        $this->actingAs($this->admin);

        $data = [
            'nama_perusahaan' => 'PT Test',
            'email' => 'invalidemail',
        ];

        $response = $this->postJson('/api/admin/perusahaan', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /**
     * Test Case 8: Guest tidak boleh akses halaman admin
     */
    public function test_guest_tidak_bisa_akses_halaman_admin_perusahaan()
    {
        $response = $this->get('/data-perusahaan');

        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test Case 9: Mahasiswa tidak boleh akses halaman admin
     */
    public function test_mahasiswa_tidak_bisa_akses_halaman_admin_perusahaan()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);

        $this->actingAs($mahasiswa);

        $response = $this->get('/data-perusahaan');

        $response->assertStatus(302);
    }
}
