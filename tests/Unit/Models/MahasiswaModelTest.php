<?php

namespace Tests\Unit\Models;

use App\Models\Jenis;
use App\Models\Kelas;
use Tests\TestCase;
use App\Models\Mahasiswa;
use App\Models\Lowongan;
use App\Models\Lamaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Unit Test: Mahasiswa Model Business Logic
 *
 * Test business logic dan relationships di Model Mahasiswa
 *
 * Command untuk menjalankan:
 * php artisan test --filter MahasiswaModelTest
 * php artisan test tests/Unit/Models/MahasiswaModelTest.php
 */
class MahasiswaModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Case 1: Mahasiswa dapat memiliki banyak lamaran (hasMany)
     */
    public function test_mahasiswa_memiliki_relasi_lamaran()
    {
        // Arrange
        $jenis = Jenis::factory()->create();
        $mahasiswa = Mahasiswa::factory()->create();
        $lowongan = Lowongan::factory()->create([
            'jenis_id' => $jenis->jenis_id
        ]);

        // Buat 3 lamaran untuk mahasiswa ini
        Lamaran::factory()->count(3)->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowongan->id_lowongan,
        ]);

        // Act
        $lamaranCount = $mahasiswa->lamaran()->count();

        // Assert
        $this->assertEquals(3, $lamaranCount);
    }

    /**
     * Test Case 2: Check mahasiswa sudah pernah apply lowongan tertentu
     */
    public function test_check_mahasiswa_sudah_apply_lowongan()
    {
        // Arrange
        $jenis = Jenis::factory()->create();
        $mahasiswa = Mahasiswa::factory()->create();
        $lowongan1 = Lowongan::factory()->create(['jenis_id' => $jenis->jenis_id]);
        $lowongan2 = Lowongan::factory()->create(['jenis_id' => $jenis->jenis_id]);

        // Mahasiswa sudah apply lowongan1
        Lamaran::factory()->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'id_lowongan' => $lowongan1->id_lowongan,
        ]);

        // Act & Assert
        $sudahApply1 = $mahasiswa->lamaran()
            ->where('id_lowongan', $lowongan1->id_lowongan)
            ->exists();

        $sudahApply2 = $mahasiswa->lamaran()
            ->where('id_lowongan', $lowongan2->id_lowongan)
            ->exists();

        $this->assertTrue($sudahApply1); // Sudah apply
        $this->assertFalse($sudahApply2); // Belum apply
    }

    /**
     * Test Case 3: Get mahasiswa by IPK minimal
     */
    public function test_filter_mahasiswa_by_ipk_minimal()
    {
        // Arrange
        Mahasiswa::factory()->create(['ipk' => 3.0]);
        Mahasiswa::factory()->create(['ipk' => 3.5]);
        Mahasiswa::factory()->create(['ipk' => 3.8]);
        Mahasiswa::factory()->create(['ipk' => 2.5]);

        // Act: Get mahasiswa dengan IPK >= 3.5
        $mahasiswa = Mahasiswa::where('ipk', '>=', 3.5)->get();

        // Assert
        $this->assertEquals(2, $mahasiswa->count());
        $this->assertTrue($mahasiswa->every(fn($m) => $m->ipk >= 3.5));
    }

    /**
     * Test Case 4: Mahasiswa full name accessor (jika ada)
     *
     * Expected: Jika ada accessor getFullNameAttribute()
     */
    // public function test_mahasiswa_full_name_accessor()
    // {
    //     // Arrange
    //     $mahasiswa = Mahasiswa::factory()->create([
    //         'nama' => 'John Doe',
    //     ]);

    //     // Act
    //     $fullName = $mahasiswa->nama; // Atau $mahasiswa->full_name jika ada accessor

    //     // Assert
    //     $this->assertEquals('John Doe', $fullName);
    // }

    /**
     * Test Case 5: Check mahasiswa eligible untuk apply (IPK >= 3.0)
     */
    public function test_mahasiswa_eligible_untuk_apply()
    {
        // Arrange
        $mahasiswaEligible = Mahasiswa::factory()->create(['ipk' => 3.5]);
        $mahasiswaTidakEligible = Mahasiswa::factory()->create(['ipk' => 2.5]);

        // Act & Assert
        $this->assertTrue($mahasiswaEligible->ipk >= 3.0);
        $this->assertFalse($mahasiswaTidakEligible->ipk >= 3.0);
    }

    /**
     * Test Case 6: Count total lamaran pending per mahasiswa
     */
    public function test_count_lamaran_pending_mahasiswa()
    {
        // Arrange
        $mahasiswa = Mahasiswa::factory()->create();
        $lowongan = Lowongan::factory()->create();

        // Buat 2 pending, 1 diterima, 1 ditolak
        Lamaran::factory()->count(2)->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'auth' => 'menunggu',
        ]);

        Lamaran::factory()->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'auth' => 'diterima',
        ]);

        Lamaran::factory()->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'auth' => 'ditolak',
        ]);

        // Act
        $pendingCount = $mahasiswa->lamaran()
            ->where('auth', 'menunggu')
            ->count();

        // Assert
        $this->assertEquals(2, $pendingCount);
    }

    /**
     * Test Case 7: Mahasiswa dengan status magang aktif
     */
    public function test_mahasiswa_dengan_status_magang_aktif()
    {
        $mahasiswa = Mahasiswa::factory()->create();

        Lamaran::factory()->create([
            'id_mahasiswa' => $mahasiswa->id_mahasiswa,
            'auth' => 'diterima',
        ]);

        $this->assertTrue($mahasiswa->memilikiMagangAktif());
    }

    /**
     * Test Case 8: Get mahasiswa by kelas
     */
    public function test_filter_mahasiswa_by_kelas()
    {
        // Arrange: buat kelas (PARENT)
        $kelas1 = Kelas::factory()->create();
        $kelas2 = Kelas::factory()->create();

        // Buat mahasiswa berdasarkan kelas
        Mahasiswa::factory()->count(3)->create([
            'id_kelas' => $kelas1->id_kelas,
        ]);

        Mahasiswa::factory()->count(2)->create([
            'id_kelas' => $kelas2->id_kelas,
        ]);

        // Act
        $mahasiswaKelas1Result = Mahasiswa::where('id_kelas', $kelas1->id_kelas)->get();

        // Assert
        $this->assertCount(3, $mahasiswaKelas1Result);
    }

    /**
     * Test Case 9: Validate NIM format (10 digit)
     */
    public function test_nim_format_validation()
    {
        // Arrange
        $validNIM = '2141720001';
        $invalidNIM = '214172'; // Kurang dari 10 digit

        // Act & Assert
        $this->assertEquals(10, strlen($validNIM));
        $this->assertLessThan(10, strlen($invalidNIM));
    }

    // /**
    //  * Test Case 10: Mahasiswa dapat di-soft delete (jika menggunakan SoftDeletes)
    //  */
    // public function test_mahasiswa_dapat_di_soft_delete()
    // {
    //     // Arrange
    //     $mahasiswa = Mahasiswa::factory()->create();
    //     $mahasiswaId = $mahasiswa->id_mahasiswa;

    //     // Act: Soft delete (jika model menggunakan SoftDeletes trait)
    //     $mahasiswa->delete();

    //     // Assert
    //     // Jika menggunakan soft delete, masih bisa ditemukan dengan withTrashed()
    //     $deletedMahasiswa = Mahasiswa::withTrashed()->find($mahasiswaId);
    //     $this->assertNotNull($deletedMahasiswa);

    //     // Tidak bisa ditemukan dengan query biasa
    //     $mahasiswa = Mahasiswa::find($mahasiswaId);
    //     $this->assertNull($mahasiswa);
    // }
}
