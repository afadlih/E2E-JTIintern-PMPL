<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MPeriodeSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_periode')->insert([
            [
                'waktu' => '3 Bulan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'waktu' => '6 Bulan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'waktu' => '12 Bulan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}