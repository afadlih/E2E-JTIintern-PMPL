<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TLogSeeder extends Seeder
{
    public function run()
    {
        DB::table('t_log')->insert([
            [
                'id_magang'     => 1, // Pastikan id_magang 1 ada di m_magang
                'tanggal'       => now()->toDateString(),
                'log_aktivitas' => 'Membuat laporan harian dan update progress project.',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'id_magang'     => 1,
                'tanggal'       => now()->subDay()->toDateString(),
                'log_aktivitas' => 'Mengikuti meeting dengan supervisor.',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]
        ]);
    }
}