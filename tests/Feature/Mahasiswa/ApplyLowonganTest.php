<?php

namespace Tests\Feature\Mahasiswa;

use Tests\TestCase;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Lowongan;
use App\Models\Lamaran;
use App\Models\Perusahaan;
use App\Models\Periode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * Feature Test: Mahasiswa Apply Lowongan
 *
 * Test full workflow mahasiswa apply lowongan magang
 *
 * Command untuk menjalankan:
 * php artisan test --filter ApplyLowonganTest
 * php artisan test tests/Feature/Mahasiswa/ApplyLowonganTest.php
 */
class ApplyLowonganTest extends TestCase
{
    use RefreshDatabase;

    protected $mahasiswa;
    protected $user;
    protected $lowongan;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup user mahasiswa
        $this->user = User::factory()->create([
            'email' => 'mahasiswa@test.com',
            'password' => Hash::make('password'),
            'level' => 'mahasiswa',
        ]);

        // Setup data mahasiswa
        $this->mahasiswa = Mahasiswa::factory()->create([
            'user_id' => $this->user->id_user,
            'nim' => '2141720001',
            'nama' => 'Test Mahasiswa',
            'ipk' => 3.5,
        ]);

        // Setup perusahaan
        $perusahaan = Perusahaan::factory()->create([
            'nama_perusahaan' => 'PT Test Company',
        ]);

        // Setup periode
        $periode = Periode::factory()->create([
            'tahun' => 2025,
            'semester' => 'Ganjil',
            'status' => 'active',
        ]);

        // Setup lowongan
        $this->lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->id_perusahaan,
            'periode_id' => $periode->id_periode,
            'judul' => 'Full Stack Developer',
            'deskripsi' => 'Mencari mahasiswa untuk magang sebagai developer',
            'kapasitas' => 5,
            'status' => 'active',
        ]);
    }

    /**
     * Test Case 1: Mahasiswa dapat melihat daftar lowongan
     */
    public function test_mahasiswa_dapat_melihat_daftar_lowongan()
    {
        // Arrange: Login sebagai mahasiswa
        $this->actingAs($this->user);

        // Act: Request ke halaman lowongan
        $response = $this->get('/mahasiswa/lowongan');

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Full Stack Developer');
        $response->assertSee('PT Test Company');
    }

    /**
     * Test Case 2: Mahasiswa dapat melihat detail lowongan
     */
    public function test_mahasiswa_dapat_melihat_detail_lowongan()
    {
        // Arrange
        $this->actingAs($this->user);

        // Act
        $response = $this->get("/mahasiswa/lowongan/{$this->lowongan->id_lowongan}");

        // Assert
        $response->assertStatus(200);
        $response->assertSee($this->lowongan->judul);
        $response->assertSee($this->lowongan->deskripsi);
    }

    /**
     * Test Case 3: Mahasiswa berhasil apply lowongan
     *
     * Expected:
     * - Lamaran tersimpan di database
     * - Redirect ke halaman lamaran saya
     * - Ada success message
     */
    public function test_mahasiswa_berhasil_apply_lowongan()
    {
        // Arrange
        $this->actingAs($this->user);

        // Act: Submit apply lowongan
        $response = $this->post("/mahasiswa/lowongan/{$this->lowongan->id_lowongan}/apply", [
            'cv' => 'path/to/cv.pdf', // Mock CV upload
            'motivasi' => 'Saya tertarik untuk belajar full stack development',
        ]);

        // Assert: Verifikasi response
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // Verifikasi data tersimpan di database
        $this->assertDatabaseHas('t_lamaran', [
            'mahasiswa_id' => $this->mahasiswa->id_mahasiswa,
            'lowongan_id' => $this->lowongan->id_lowongan,
            'status' => 'pending',
        ]);
    }

    /**
     * Test Case 4: Mahasiswa tidak bisa apply lowongan yang sama 2x
     */
    public function test_mahasiswa_tidak_bisa_apply_lowongan_sama_dua_kali()
    {
        // Arrange: Sudah apply sebelumnya
        $this->actingAs($this->user);

        Lamaran::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id_mahasiswa,
            'lowongan_id' => $this->lowongan->id_lowongan,
            'status' => 'pending',
        ]);

        // Act: Apply lagi
        $response = $this->post("/mahasiswa/lowongan/{$this->lowongan->id_lowongan}/apply", [
            'motivasi' => 'Apply lagi',
        ]);

        // Assert: Ditolak
        $response->assertStatus(302);
        $response->assertSessionHas('error');

        // Verifikasi hanya ada 1 lamaran di database
        $this->assertEquals(1, Lamaran::where([
            'mahasiswa_id' => $this->mahasiswa->id_mahasiswa,
            'lowongan_id' => $this->lowongan->id_lowongan,
        ])->count());
    }

    /**
     * Test Case 5: Mahasiswa tidak bisa apply lowongan yang sudah penuh
     */
    public function test_mahasiswa_tidak_bisa_apply_lowongan_penuh()
    {
        // Arrange: Lowongan dengan kapasitas 1, sudah ada 1 lamaran diterima
        $lowonganPenuh = Lowongan::factory()->create([
            'kapasitas' => 1,
            'status' => 'active',
        ]);

        Lamaran::factory()->create([
            'lowongan_id' => $lowonganPenuh->id_lowongan,
            'status' => 'diterima',
        ]);

        $this->actingAs($this->user);

        // Act: Coba apply
        $response = $this->post("/mahasiswa/lowongan/{$lowonganPenuh->id_lowongan}/apply", [
            'motivasi' => 'Ingin apply',
        ]);

        // Assert
        $response->assertStatus(302);
        $response->assertSessionHas('error');

        // Verifikasi tidak ada lamaran baru dari mahasiswa ini
        $this->assertDatabaseMissing('t_lamaran', [
            'mahasiswa_id' => $this->mahasiswa->id_mahasiswa,
            'lowongan_id' => $lowonganPenuh->id_lowongan,
        ]);
    }

    /**
     * Test Case 6: Mahasiswa dapat melihat status lamaran
     */
    public function test_mahasiswa_dapat_melihat_status_lamaran()
    {
        // Arrange: Sudah apply lowongan
        $this->actingAs($this->user);

        $lamaran = Lamaran::factory()->create([
            'mahasiswa_id' => $this->mahasiswa->id_mahasiswa,
            'lowongan_id' => $this->lowongan->id_lowongan,
            'status' => 'pending',
        ]);

        // Act: Request halaman lamaran saya
        $response = $this->get('/mahasiswa/lamaran');

        // Assert
        $response->assertStatus(200);
        $response->assertSee($this->lowongan->judul);
        $response->assertSee('pending');
    }

    /**
     * Test Case 7: Guest tidak bisa akses halaman lowongan mahasiswa
     */
    public function test_guest_tidak_bisa_akses_halaman_mahasiswa()
    {
        // Act: Request tanpa login
        $response = $this->get('/mahasiswa/lowongan');

        // Assert: Redirect ke login
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test Case 8: Admin tidak bisa akses halaman mahasiswa
     */
    public function test_admin_tidak_bisa_akses_halaman_mahasiswa()
    {
        // Arrange: Login sebagai admin
        $admin = User::factory()->create([
            'level' => 'superadmin',
        ]);
        $this->actingAs($admin);

        // Act
        $response = $this->get('/mahasiswa/lowongan');

        // Assert: Forbidden atau redirect
        $response->assertStatus(403); // Atau 302 tergantung middleware
    }
}
