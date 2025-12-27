<?php

namespace Tests\Unit\Models;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserModelTest extends TestCase
{
     use RefreshDatabase;

    /** @test */
    public function user_menggunakan_table_dan_primary_key_yang_benar()
    {
        $user = new User();

        $this->assertEquals('m_user', $user->getTable());
        $this->assertEquals('id_user', $user->getKeyName());
    }

    /** @test */
    public function user_dapat_dibuat_dengan_fillable_attributes()
    {
        $user = User::factory()->create([
            'name' => 'Fabian',
            'email' => 'fabian@test.com',
            'password' => Hash::make('secret'),
            'role' => 'mahasiswa',
        ]);

        $this->assertEquals('Fabian', $user->name);
        $this->assertEquals('fabian@test.com', $user->email);
        $this->assertEquals('mahasiswa', $user->role);
    }

    /** @test */
    public function password_disembunyikan_saat_serialisasi()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret'),
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
    }

    /** @test */
    public function email_verified_at_di_cast_ke_datetime()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->assertInstanceOf(
            \Illuminate\Support\Carbon::class,
            $user->email_verified_at
        );
    }

    /** @test */
    public function user_memiliki_relasi_mahasiswa()
    {
        $user = User::factory()->create();

        $mahasiswa = Mahasiswa::factory()->create([
            'id_user' => $user->id_user,
        ]);

        $this->assertInstanceOf(Mahasiswa::class, $user->mahasiswa);
        $this->assertEquals($mahasiswa->id_mahasiswa, $user->mahasiswa->id_mahasiswa);
    }

    /** @test */
    public function user_memiliki_relasi_dosen()
    {
        $user = User::factory()->create();

        $dosen = Dosen::factory()->create([
            'user_id' => $user->id_user,
        ]);

        $this->assertInstanceOf(Dosen::class, $user->dosen);
        $this->assertEquals($dosen->id_dosen, $user->dosen->id_dosen);
    }

    /** @test */
    public function user_dapat_memiliki_banyak_skill_dengan_pivot_lama_skill()
    {
        $user = User::factory()->create();
        $skill = Skill::factory()->create();

        $user->skills()->attach($skill->id, [
            'lama_skill' => 3,
        ]);

        $this->assertTrue($user->skills->contains($skill));
        $this->assertEquals(3, $user->skills->first()->pivot->lama_skill);
    }

}
