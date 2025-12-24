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
 * @group api
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
    protected $perusahaan;
    protected $periode;
    protected $jenis;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup user mahasiswa
        $this->user = User::factory()->create([
            'email' => 'mahasiswa@test.com',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        // Setup data mahasiswa
        $this->mahasiswa = Mahasiswa::factory()->create([
            'id_user' => $this->user->id_user,
            'nim' => '2141720001',
            'nama' => 'Test Mahasiswa',
            'ipk' => 3.5,
        ]);

        // Setup perusahaan
        $this->perusahaan = Perusahaan::factory()->create([
            'nama_perusahaan' => 'PT Test Company',
        ]);

        // Setup periode
        $this->periode = Periode::factory()->create([
            'waktu' => 'Periode Ganjil 2025',
            'status' => 'aktif',
        ]);

        // Setup jenis
        $this->jenis = \App\Models\Jenis::factory()->create();

        // Setup lowongan - menggunakan for() untuk set relationships
        $this->lowongan = Lowongan::factory()
            ->for($this->perusahaan, 'perusahaan')
            ->for($this->periode, 'periode')
            ->for($this->jenis, 'jenis')
            ->create([
                'judul_lowongan' => 'Full Stack Developer',
                'deskripsi' => 'Mencari mahasiswa untuk magang sebagai developer',
                'kapasitas' => 5,
                'status' => 'aktif',
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
        $response->assertSee($this->lowongan->judul_lowongan);
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
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $this->lowongan->id_lowongan,
            'auth' => 'menunggu',
        ]);
    }

    /**
     * Test Case 4: Mahasiswa tidak bisa apply lowongan yang sama 2x
     */
    public function test_mahasiswa_tidak_bisa_apply_lowongan_sama_dua_kali()
    {
        // Arrange: Sudah apply sebelumnya
        $this->actingAs($this->user);

        Lamaran::factory()->pending()->create([
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $this->lowongan->id_lowongan,
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
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $this->lowongan->id_lowongan,
        ])->count());
    }

    /**
     * Test Case 5: Mahasiswa tidak bisa apply lowongan yang sudah penuh
     */
    public function test_mahasiswa_tidak_bisa_apply_lowongan_penuh()
    {
        // Arrange: Lowongan dengan kapasitas 1, sudah ada 1 lamaran diterima
        $perusahaan = Perusahaan::factory()->create();
        $periode = Periode::factory()->create(['waktu' => 'Periode Ganjil 2025', 'status' => 'aktif']);
        $jenis = \App\Models\Jenis::factory()->create();

        $lowonganPenuh = Lowongan::factory()
            ->for($perusahaan, 'perusahaan')
            ->for($periode, 'periode')
            ->for($jenis, 'jenis')
            ->create([
                'kapasitas' => 1,
                'status' => 'aktif',
            ]);

        Lamaran::factory()->diterima()->create([
            'id_lowongan' => $lowonganPenuh->id_lowongan,
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
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowonganPenuh->id_lowongan,
        ]);
    }

    /**
     * Test Case 6: Mahasiswa dapat melihat status lamaran
     */
    public function test_mahasiswa_dapat_melihat_status_lamaran()
    {
        // Arrange: Sudah apply lowongan
        $this->actingAs($this->user);

        $lamaran = Lamaran::factory()->pending()->create([
            'id_mahasiswa' => $this->mahasiswa->id_mahasiswa,
            'id_lowongan' => $this->lowongan->id_lowongan,
        ]);

        // Act: Request halaman lamaran saya
        $response = $this->get('/mahasiswa/lamaran');

        // Assert
        $response->assertStatus(200);
        $response->assertSee($this->lowongan->judul_lowongan);
        $response->assertSee('menunggu');
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
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        // Act
        $response = $this->get('/mahasiswa/lowongan');

        // Assert: Forbidden atau redirect
        $response->assertStatus(403); // Atau 302 tergantung middleware
    }
}
