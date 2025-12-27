<?php

namespace Tests\Unit\Models;

use App\Models\Dokumen;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DokumenModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dokumen_menggunakan_table_dan_primary_key_yang_benar()
    {
        $dokumen = new Dokumen();

        $this->assertEquals('m_dokumen', $dokumen->getTable());
        $this->assertEquals('id_dokumen', $dokumen->getKeyName());
    }

    /** @test */
    public function dokumen_dapat_dibuat_dengan_fillable_attributes()
    {
        $user = User::factory()->create();

        $dokumen = Dokumen::factory()->create([
            'id_user'     => $user->id_user,
            'file_name'   => 'cv_fabian.pdf',
            'file_path'   => '/uploads/cv_fabian.pdf',
            'file_type'   => 'pdf',
            'description' => 'Curriculum Vitae',
        ]);

        $this->assertEquals('cv_fabian.pdf', $dokumen->file_name);
        $this->assertEquals($user->id_user, $dokumen->id_user);
    }

    /** @test */
    public function dokumen_memiliki_relasi_ke_user()
    {
        $user = User::factory()->create();

        $dokumen = Dokumen::factory()->create([
            'id_user' => $user->id_user,
        ]);

        $this->assertInstanceOf(User::class, $dokumen->user);
        $this->assertEquals($user->id_user, $dokumen->user->id_user);
    }

    /** @test */
    public function dokumen_dapat_diakses_melalui_relasi_mahasiswa()
    {
        $user = User::factory()->create();

        $mahasiswa = Mahasiswa::factory()->create([
            'id_user' => $user->id_user,
        ]);

        $dokumen = Dokumen::factory()->create([
            'id_user' => $user->id_user,
        ]);

        $this->assertInstanceOf(Mahasiswa::class, $dokumen->mahasiswa);
        $this->assertEquals(
            $mahasiswa->id_mahasiswa,
            $dokumen->mahasiswa->id_mahasiswa
        );
    }
}