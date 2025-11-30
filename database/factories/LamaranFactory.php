<?php

namespace Database\Factories;

use App\Models\Lamaran;
use App\Models\Mahasiswa;
use App\Models\Lowongan;
use App\Models\Dosen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lamaran>
 */
class LamaranFactory extends Factory
{
    protected $model = Lamaran::class;

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
            'id_dosen' => null,
            'auth' => fake()->randomElement(['menunggu', 'diterima', 'ditolak']),
            'tanggal_lamaran' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the lamaran is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'auth' => 'menunggu',
        ]);
    }

    /**
     * Indicate that the lamaran is accepted.
     */
    public function diterima(): static
    {
        return $this->state(fn (array $attributes) => [
            'auth' => 'diterima',
            'id_dosen' => Dosen::factory(),
        ]);
    }

    /**
     * Indicate that the lamaran is rejected.
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'auth' => 'ditolak',
        ]);
    }
}
