<?php

namespace Database\Factories;

use App\Models\Lowongan;
use App\Models\Perusahaan;
use App\Models\Periode;
use App\Models\Jenis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lowongan>
 */
class LowonganFactory extends Factory
{
    protected $model = Lowongan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'judul_lowongan' => fake()->randomElement([
                'Web Developer',
                'Mobile Developer',
                'Data Analyst',
                'UI/UX Designer',
                'Network Engineer',
                'System Administrator'
            ]),
            'perusahaan_id' => Perusahaan::factory(),
            'periode_id' => Periode::factory(),
            'jenis_id' => fake()->numberBetween(1, 3), // Assuming 1-3 jenis exists
            'kapasitas' => fake()->numberBetween(1, 10),
            'min_ipk' => fake()->randomFloat(2, 2.5, 3.5),
            'deskripsi' => fake()->paragraph(3),
        ];
    }

    /**
     * Indicate that the lowongan is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'periode_id' => Periode::factory()->active(),
        ]);
    }
}
