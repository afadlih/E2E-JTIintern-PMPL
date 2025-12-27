<?php

namespace Tests\Unit\Models;

use App\Models\Dosen;
use App\Models\Lamaran;
use App\Models\Lowongan;
use App\Models\Mahasiswa;
use App\Models\Perusahaan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LamaranModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lamaran_menggunakan_table_dan_primary_key_yang_benar()
    {
        $lamaran = new Lamaran();

        $this->assertEquals('t_lamaran', $lamaran->getTable());
        $this->assertEquals('id_lamaran', $lamaran->getKeyName());
    }

    /** @test */
    public function lamaran_dapat_dibuat_dengan_fillable_attributes()
    {
        $mahasiswa = Mahasiswa::factory()->create();
        $lowongan  = Lowongan::factory()->create();

        $lamaran = Lamaran::factory()->create([
            'id_mahasiswa'     => $mahasiswa->id_mahasiswa,
            'id_lowongan'      => $lowongan->id_lowongan,
            'status'           => 'aktif',
            'auth'             => 'menunggu',
            'tanggal_lamaran'  => now(),
        ]);

        $this->assertEquals('aktif', $lamaran->status);
        $this->assertEquals('menunggu', $lamaran->auth);
        $this->assertEquals($mahasiswa->id_mahasiswa, $lamaran->id_mahasiswa);
    }

    /** @test */
    public function tanggal_lamaran_di_cast_ke_date()
    {
        $lamaran = Lamaran::factory()->create([
            'tanggal_lamaran' => now(),
        ]);

        $this->assertInstanceOf(
            Carbon::class,
            $lamaran->tanggal_lamaran
        );
    }

    /** @test */
    public function lamaran_memiliki_relasi_mahasiswa()
    {
        $mahasiswa = Mahasiswa::factory()->create();

        $lamaran = Lamaran::factory()->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
        ]);

        $this->assertInstanceOf(Mahasiswa::class, $lamaran->mahasiswa);
        $this->assertEquals(
            $mahasiswa->id_mahasiswa,
            $lamaran->mahasiswa->id_mahasiswa
        );
    }

    /** @test */
    public function lamaran_memiliki_relasi_lowongan_dengan_default()
    {
        $lamaran = Lamaran::factory()->create([
            'id_lowongan' => null,
        ]);

        // withDefault() memastikan tidak null
        $this->assertNotNull($lamaran->lowongan);
    }

    /** @test */
    public function lamaran_memiliki_relasi_dosen()
    {
        $dosen = Dosen::factory()->create();

        $lamaran = Lamaran::factory()->create([
            'id_dosen' => $dosen->id_dosen,
        ]);

        $this->assertInstanceOf(Dosen::class, $lamaran->dosen);
    }

    /** @test */
    public function lamaran_memiliki_relasi_perusahaan()
    {
        $perusahaan = Perusahaan::factory()->create();

        $lamaran = Lamaran::factory()->create([
            'perusahaan_id' => $perusahaan->perusahaan_id,
        ]);

        $this->assertInstanceOf(Perusahaan::class, $lamaran->perusahaan);
    }
}
