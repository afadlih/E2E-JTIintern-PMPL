<?php

namespace Database\Factories;

use App\Models\Jenis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Jenis>
 */
class JenisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Jenis::class;

    public function definition(): array
    {
        return [
            'nama_jenis' => $this->faker->word(),
        ];
    }
}
