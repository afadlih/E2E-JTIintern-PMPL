<?php

namespace Database\Factories;

use App\Models\Wilayah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wilayah>
 */
class WilayahFactory extends Factory
{
    protected $model = Wilayah::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_kota' => fake()->city(),
            'provinsi' => fake()->randomElement(['Jawa Timur', 'Jawa Tengah', 'Jawa Barat', 'DKI Jakarta']),
            'latitude' => fake()->latitude(-8, -6),
            'longitude' => fake()->longitude(110, 115),
        ];
    }
}
