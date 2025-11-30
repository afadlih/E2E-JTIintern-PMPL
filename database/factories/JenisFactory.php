<?php

namespace Database\Factories;

use App\Models\Jenis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jenis>
 */
class JenisFactory extends Factory
{
    protected $model = Jenis::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_jenis' => fake()->randomElement([
                'Work from Office',
                'Work from Home',
                'Hybrid'
            ]),
        ];
    }
}
