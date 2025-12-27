<?php

namespace Database\Factories;

use App\Models\Dosen;
use App\Models\Lowongan;
use App\Models\Magang;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Magang>
 */
class MagangFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Magang::class;

    public function definition(): array
    {
        return [
            'id_lowongan' => Lowongan::factory(),
            'id_mahasiswa' => Mahasiswa::factory(),
            'id_dosen' => Dosen::factory(),
            'status' => 'aktif',
            'tgl_mulai' => now()->subDays(5),
            'tgl_selesai' => now()->addDays(5),
        ];
    }
}
