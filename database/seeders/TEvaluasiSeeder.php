<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TEvaluasiSeeder extends Seeder
{
    public function run()
    {
        DB::table('t_evaluasi')->insert([
            [
                'id_magang' => 13, // Pastikan id_magang 1 ada di m_magang
                'nilai'     => 90,
                'eval'      => 'Sangat baik, aktif dalam setiap tugas dan disiplin.',
                'created_at'=> now(),
                'updated_at'=> now(),
            ]
        ]);
    }
}