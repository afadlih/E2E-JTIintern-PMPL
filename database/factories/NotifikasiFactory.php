<?php

namespace Database\Factories;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notifikasi>
 */
class NotifikasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Notifikasi::class;
    public function definition(): array
    {
        return [
            'id_user' => User::factory(),
            'judul' => $this->faker->sentence(),
            'pesan' => $this->faker->paragraph(),
            'jenis' => 'info',
            'kategori' => 'sistem',
            'data_terkait' => ['foo' => 'bar'],
            'is_read' => false,
            'is_important' => false,
            'expired_at' => null,
        ];
    }
}
