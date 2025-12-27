<?php

namespace Tests\Unit\Models;

use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerusahaanModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function perusahaan_menggunakan_table_dan_primary_key_yang_benar()
    {
        $perusahaan = new Perusahaan();

        $this->assertEquals('m_perusahaan', $perusahaan->getTable());
        $this->assertEquals('perusahaan_id', $perusahaan->getKeyName());
        $this->assertTrue($perusahaan->getIncrementing());
        $this->assertEquals('int', $perusahaan->getKeyType());
    }

    /** @test */
    public function perusahaan_dapat_dibuat_dengan_fillable_attributes()
    {
        $wilayah = Wilayah::factory()->create();

        $perusahaan = Perusahaan::factory()->create([
            'nama_perusahaan'   => 'PT Contoh',
            'alamat_perusahaan' => 'Jl. Contoh No. 1',
            'wilayah_id'        => $wilayah->wilayah_id,
            'email'             => 'contoh@test.com',
        ]);

        $this->assertEquals('PT Contoh', $perusahaan->nama_perusahaan);
        $this->assertEquals($wilayah->wilayah_id, $perusahaan->wilayah_id);
    }

    /** @test */
    public function logo_url_accessor_menambahkan_storage_jika_path_relatif()
    {
        $perusahaan = Perusahaan::factory()->create([
            'logo' => 'logos/test.png',
        ]);

        $this->assertEquals(
            asset('storage/logos/test.png'),
            $perusahaan->logo_url
        );
    }

    /** @test */
    public function logo_url_accessor_tidak_menggandakan_storage_jika_sudah_ada()
    {
        $perusahaan = Perusahaan::factory()->create([
            'logo' => 'storage/logos/test.png',
        ]);

        $this->assertEquals(
            asset('storage/logos/test.png'),
            $perusahaan->logo_url
        );
    }

    /** @test */
    public function logo_path_accessor_menghapus_prefix_storage()
    {
        $perusahaan = Perusahaan::factory()->create([
            'logo' => 'storage/logos/test.png',
        ]);

        $this->assertEquals(
            'logos/test.png',
            $perusahaan->logo_path
        );
    }

    /** @test */
    public function logo_accessor_null_jika_tidak_ada_logo()
    {
        $perusahaan = Perusahaan::factory()->create([
            'logo' => null,
        ]);

        $this->assertNull($perusahaan->logo_url);
        $this->assertNull($perusahaan->logo_path);
    }

    /** @test */
    public function perusahaan_memiliki_relasi_wilayah()
    {
        $wilayah = Wilayah::factory()->create();

        $perusahaan = Perusahaan::factory()->create([
            'wilayah_id' => $wilayah->wilayah_id,
        ]);

        $this->assertInstanceOf(Wilayah::class, $perusahaan->wilayah);
    }

    /** @test */
    public function perusahaan_dapat_memiliki_banyak_lowongan()
    {
        $perusahaan = Perusahaan::factory()->create();

        Lowongan::factory()->count(3)->create([
            'perusahaan_id' => $perusahaan->perusahaan_id,
        ]);

        $this->assertCount(3, $perusahaan->lowongan);
    }
}
