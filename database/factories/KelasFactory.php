<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kelas>
 */
class KelasFactory extends Factory
{
    protected $model = Kelas::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get or create a Prodi
        $prodi = \App\Models\Prodi::firstOrCreate(
            ['kode_prodi' => 'TI'],
            ['nama_prodi' => 'D4 Teknik Informatika']
        );

        return [
            'nama_kelas' => 'TI-' . fake()->randomElement(['2A', '2B', '2C', '3A', '3B', '3C', '4A', '4B']),
            'kode_prodi' => $prodi->kode_prodi,
            'tahun_masuk' => fake()->randomElement([2020, 2021, 2022, 2023, 2024]),
        ];
    }
}
