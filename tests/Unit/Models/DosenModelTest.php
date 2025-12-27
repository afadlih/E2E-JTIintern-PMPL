<?php

namespace Tests\Unit\Models;

use App\Models\Dosen;
use App\Models\Perusahaan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dosen_menggunakan_table_dan_primary_key_yang_benar()
    {
        $dosen = new Dosen();

        $this->assertEquals('m_dosen', $dosen->getTable());
        $this->assertEquals('id_dosen', $dosen->getKeyName());
    }

    /** @test */
    public function dosen_memiliki_relasi_ke_user()
    {
        $user = User::factory()->create();

        $dosen = Dosen::factory()->create([
            'user_id' => $user->id_user,
        ]);

        $this->assertInstanceOf(User::class, $dosen->user);
        $this->assertEquals($user->id_user, $dosen->user->id_user);
    }

    /** @test */
    public function dosen_dapat_memiliki_banyak_perusahaan()
    {
        $dosen = Dosen::factory()->create();

        $perusahaan1 = Perusahaan::factory()->create();
        $perusahaan2 = Perusahaan::factory()->create();

        $dosen->perusahaan()->attach([
            $perusahaan1->id_perusahaan,
            $perusahaan2->id_perusahaan,
        ]);

        $this->assertCount(2, $dosen->perusahaan);
        $this->assertTrue($dosen->perusahaan->contains($perusahaan1));
        $this->assertTrue($dosen->perusahaan->contains($perusahaan2));
    }

    /** @test */
    public function dosen_dapat_memiliki_wilayah_langsung()
    {
        $wilayah = Wilayah::factory()->create();

        $dosen = Dosen::factory()->create([
            'wilayah_id' => $wilayah->wilayah_id,
        ]);

        $this->assertInstanceOf(Wilayah::class, $dosen->wilayah);
        $this->assertEquals($wilayah->wilayah_id, $dosen->wilayah->wilayah_id);
    }

    /** @test */
    public function dosen_dapat_mengambil_wilayah_melalui_perusahaan()
    {
        $wilayah = Wilayah::factory()->create();

        $dosen = Dosen::factory()->create();

        $perusahaan = Perusahaan::factory()->create([
            'wilayah_id' => $wilayah->wilayah_id,
        ]);

        $dosen->perusahaan()->attach($perusahaan->id);

        $wilayahResult = $dosen->wilayahPerusahaan;

        $this->assertTrue($wilayahResult->contains($wilayah));
    }
}
