<?php

namespace Database\Factories;

use App\Models\Dokumen;
use App\Models\Dosen;
use App\Models\Lowongan;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lamaran>
 */
class LamaranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_mahasiswa' => Mahasiswa::factory(),
            'id_lowongan' => Lowongan::factory(),
            'id_dosen' => Dosen::factory(),
            'id_dokumen' => Dokumen::factory(),
            'auth' => $this->faker->randomElement([
                'menunggu',
                'diterima',
                'ditolak',
            ]),
            'tanggal_lamaran' => $this->faker->date(),
        ];
    }
}
