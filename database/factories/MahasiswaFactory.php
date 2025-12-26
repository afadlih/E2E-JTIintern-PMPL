<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    protected $model = Mahasiswa::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user' => User::factory()->mahasiswa(),
            'nim' => fake()->unique()->numberBetween(2141720001, 2141729999),
            'id_kelas' => \App\Models\Kelas::factory(),
            'alamat' => fake()->streetAddress(),
            'ipk' => fake()->randomFloat(2, 2.5, 4.0),
        ];
    }
}
