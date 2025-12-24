<?php

namespace Database\Factories;

use App\Models\Periode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Periode>
 */
class PeriodeFactory extends Factory
{
    protected $model = Periode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, '+6 months');

        return [
            'waktu' => 'Periode ' . fake()->randomElement(['Ganjil', 'Genap']) . ' ' . date('Y', $startDate->getTimestamp()),
            'tgl_mulai' => $startDate->format('Y-m-d'),
            'tgl_selesai' => $endDate->format('Y-m-d'),
            'status' => 'aktif',
        ];
    }

    /**
     * Indicate that the periode is active.
     */
    public function active(): static
    {
        $now = now();
        return $this->state(fn (array $attributes) => [
            'tgl_mulai' => $now->copy()->subMonth()->format('Y-m-d'),
            'tgl_selesai' => $now->copy()->addMonths(5)->format('Y-m-d'),
        ]);
    }
}
