<?php

namespace Database\Factories;

use App\Models\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdiFactory extends Factory
{
    protected $model = Prodi::class;

    public function definition(): array
    {
        $kodeProdis = ['TI', 'SI', 'TE', 'TM', 'AK'];
        $namaMapping = [
            'TI' => 'D4 Teknik Informatika',
            'SI' => 'D4 Sistem Informasi Bisnis',
            'TE' => 'D4 Teknik Elektronika',
            'TM' => 'D4 Teknik Mesin',
            'AK' => 'D3 Akuntansi',
        ];

        $kode = fake()->randomElement($kodeProdis);

        return [
            'kode_prodi' => $kode,
            'nama_prodi' => $namaMapping[$kode],
        ];
    }
}
