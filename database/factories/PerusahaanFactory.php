<?php

namespace Database\Factories;

use App\Models\Perusahaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Perusahaan>
 */
class PerusahaanFactory extends Factory
{
    protected $model = Perusahaan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_perusahaan' => fake()->company(),
            'alamat_perusahaan' => fake()->streetAddress(),
            'wilayah_id' => fake()->numberBetween(1, 100),
            'contact_person' => fake()->name(),
            'email' => fake()->companyEmail(),
            'instagram' => '@' . fake()->userName(),
            'website' => fake()->url(),
            'deskripsi' => fake()->paragraph(),
            'gmaps' => fake()->url(),
            'logo' => null,
        ];
    }
}
