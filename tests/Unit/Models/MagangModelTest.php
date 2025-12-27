<?php

namespace Tests\Unit\Models;

use App\Models\Dosen;
use App\Models\Lowongan;
use App\Models\Magang;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MagangModelTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Carbon::setTestNow(); // reset waktu
    }

    /** @test */
    public function magang_menggunakan_table_dan_primary_key_yang_benar()
    {
        $magang = new Magang();

        $this->assertEquals('m_magang', $magang->getTable());
        $this->assertEquals('id_magang', $magang->getKeyName());
    }

    /** @test */
    public function tgl_mulai_dan_tgl_selesai_di_cast_ke_date()
    {
        $magang = Magang::factory()->create([
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addDays(10),
        ]);

        $this->assertInstanceOf(Carbon::class, $magang->tgl_mulai);
        $this->assertInstanceOf(Carbon::class, $magang->tgl_selesai);
    }

    /** @test */
    public function progress_0_persen_jika_belum_mulai()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_mulai' => now()->addDays(5),
            'tgl_selesai' => now()->addDays(15),
        ]);

        $this->assertEquals(0, $magang->progress);
    }

    /** @test */
    public function progress_100_persen_jika_sudah_selesai()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_mulai' => now()->subDays(20),
            'tgl_selesai' => now()->subDays(5),
        ]);

        $this->assertEquals(100, $magang->progress);
    }

    /** @test */
    public function progress_berjalan_dihitung_dengan_benar()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_mulai' => now()->subDays(5),
            'tgl_selesai' => now()->addDays(5),
        ]);

        $this->assertGreaterThan(0, $magang->progress);
        $this->assertLessThan(100, $magang->progress);
    }

    /** @test */
    public function hari_lewat_dihitung_dengan_benar()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_mulai' => now()->subDays(7),
        ]);

        $this->assertEquals(7, $magang->hari_lewat);
    }

    /** @test */
    public function sisa_hari_dihitung_dengan_benar()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_selesai' => now()->addDays(10),
        ]);

        $this->assertEquals(10, $magang->sisa_hari);
    }

    /** @test */
    public function total_durasi_dihitung_dengan_benar()
    {
        $magang = Magang::factory()->create([
            'tgl_mulai' => now(),
            'tgl_selesai' => now()->addDays(30),
        ]);

        $this->assertEquals(30, $magang->total_durasi);
    }

    /** @test */
    public function status_magang_belum_terjadwal_jika_tanggal_kosong()
    {
        $magang = Magang::factory()->create([
            'tgl_mulai' => null,
            'tgl_selesai' => null,
        ]);

        $this->assertEquals('belum_terjadwal', $magang->status_magang);
    }

    /** @test */
    public function status_magang_belum_mulai()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_mulai' => now()->addDays(3),
            'tgl_selesai' => now()->addDays(10),
        ]);

        $this->assertEquals('belum_mulai', $magang->status_magang);
    }

    /** @test */
    public function status_magang_berlangsung()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_mulai' => now()->subDays(3),
            'tgl_selesai' => now()->addDays(3),
        ]);

        $this->assertEquals('berlangsung', $magang->status_magang);
    }

    /** @test */
    public function status_magang_selesai()
    {
        Carbon::setTestNow(now());

        $magang = Magang::factory()->create([
            'tgl_mulai' => now()->subDays(10),
            'tgl_selesai' => now()->subDays(1),
        ]);

        $this->assertEquals('selesai', $magang->status_magang);
    }

    /** @test */
    public function magang_memiliki_relasi_mahasiswa_dosen_dan_lowongan()
    {
        $mahasiswa = Mahasiswa::factory()->create();
        $dosen = Dosen::factory()->create();
        $lowongan = Lowongan::factory()->create();

        $magang = Magang::factory()->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'id_dosen' => $dosen->id_dosen,
            'id_lowongan' => $lowongan->id_lowongan,
        ]);

        $this->assertInstanceOf(Mahasiswa::class, $magang->mahasiswa);
        $this->assertInstanceOf(Dosen::class, $magang->dosen);
        $this->assertInstanceOf(Lowongan::class, $magang->lowongan);
    }
}
