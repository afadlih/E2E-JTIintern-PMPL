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
        $judul = fake()->randomElement([
            'Web Developer',
            'Mobile Developer',
            'Data Analyst',
            'UI/UX Designer',
            'Network Engineer',
            'System Administrator'
        ]);
        $kapasitas = fake()->numberBetween(1, 10);

        return [
            'judul_lowongan' => $judul,
            'perusahaan_id' => function () {
                return Perusahaan::factory()->create()->perusahaan_id;
            },
            'periode_id' => function () {
                return Periode::factory()->create()->periode_id;
            },
            'jenis_id' => function () {
                return Jenis::factory()->create()->jenis_id;
            },
            'kapasitas' => $kapasitas,
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
