<?php

namespace Database\Factories;

use App\Models\Dokumen;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dokumen>
 */
class DokumenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Dokumen::class;

    public function definition(): array
    {
        return [
            'id_user' => User::factory(),

            'file_name' => $this->faker->lexify('dokumen_????') . '.pdf',
            'file_path' => 'uploads/dokumen/' . $this->faker->lexify('file_????'). 'pdf',
            'file_type' => 'pdf',

            'description' => $this->faker->sentence(),
            'upload_date' => $this->faker->date(),
        ];
    }
}
