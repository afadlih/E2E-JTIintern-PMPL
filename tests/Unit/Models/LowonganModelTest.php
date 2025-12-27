<?php

namespace Tests\Unit\Models;

use App\Models\Jenis;
use App\Models\Lowongan;
use App\Models\Periode;
use App\Models\Perusahaan;
use App\Models\Skill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LowonganModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function lowongan_menggunakan_table_dan_primary_key_yang_benar()
    {
        $lowongan = new Lowongan();

        $this->assertEquals('m_lowongan', $lowongan->getTable());
        $this->assertEquals('id_lowongan', $lowongan->getKeyName());
    }

    /** @test */
    public function lowongan_dapat_dibuat_dengan_fillable_attributes()
    {
        $perusahaan = Perusahaan::factory()->create();
        $periode    = Periode::factory()->create();
        $jenis      = Jenis::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'judul_lowongan' => 'Backend Developer',
            'perusahaan_id'  => $perusahaan->perusahaan_id,
            'periode_id'     => $periode->periode_id,
            'jenis_id'       => $jenis->jenis_id,
            'kapasitas'      => 5,
            'min_ipk'        => 3.25,
            'deskripsi'      => 'Lowongan backend Laravel',
        ]);

        $this->assertEquals('Backend Developer', $lowongan->judul_lowongan);
        $this->assertEquals(5, $lowongan->kapasitas);
    }

    /** @test */
    public function min_ipk_dan_kapasitas_di_cast_dengan_benar()
    {
        $lowongan = Lowongan::factory()->create([
            'min_ipk'   => 3.5,
            'kapasitas' => '10',
        ]);

        $this->assertIsFloat((float) $lowongan->min_ipk);
        $this->assertIsInt($lowongan->kapasitas);
    }

    /** @test */
    public function lowongan_memiliki_relasi_perusahaan()
    {
        $perusahaan = Perusahaan::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'perusahaan_id' => $perusahaan->perusahaan_id,
        ]);

        $this->assertInstanceOf(Perusahaan::class, $lowongan->perusahaan);
    }

    /** @test */
    public function lowongan_memiliki_relasi_periode()
    {
        $periode = Periode::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'periode_id' => $periode->periode_id,
        ]);

        $this->assertInstanceOf(Periode::class, $lowongan->periode);
    }

    /** @test */
    public function lowongan_memiliki_relasi_jenis()
    {
        $jenis = Jenis::factory()->create();

        $lowongan = Lowongan::factory()->create([
            'jenis_id' => $jenis->jenis_id,
        ]);

        $this->assertInstanceOf(Jenis::class, $lowongan->jenis);
    }

    /** @test */
    public function lowongan_dapat_memiliki_banyak_skill()
    {
        $lowongan = Lowongan::factory()->create();

        $skill1 = Skill::factory()->create();
        $skill2 = Skill::factory()->create();

        $lowongan->skills()->attach([
            $skill1->getKey(),
            $skill2->getKey(),
        ]);

        $this->assertCount(2, $lowongan->skills);
        $this->assertTrue($lowongan->skills->contains($skill1));
    }
}
