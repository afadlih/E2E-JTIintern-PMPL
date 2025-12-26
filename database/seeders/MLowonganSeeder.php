<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MLowonganSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_lowongan')->insert([
            [
                'perusahaan_id'   => 1,
                'periode_id'      => 1,
                'judul_lowongan'  => 'Backend Developer Intern',
                'kapasitas'       => 3,
                'deskripsi'       => 'Membantu pengembangan API dan backend aplikasi.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'perusahaan_id'   => 2,
                'periode_id'      => 2,
                'judul_lowongan'  => 'Frontend Developer Intern',
                'kapasitas'       => 2,
                'deskripsi'       => 'Membantu pengembangan UI/UX aplikasi web.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'perusahaan_id'   => 3,
                'periode_id'      => 3,
                'judul_lowongan'  => 'Data Analyst Intern',
                'kapasitas'       => 1,
                'deskripsi'       => 'Menganalisis data dan membuat laporan bisnis.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);

        // Insert skill requirements for lowongan into pivot table
        DB::table('t_skill_lowongan')->insert([
            ['id_lowongan' => 1, 'id_skill' => 1], // Backend needs PHP
            ['id_lowongan' => 2, 'id_skill' => 2], // Frontend needs JavaScript
            ['id_lowongan' => 3, 'id_skill' => 3], // Data Analyst needs Python
        ]);
    }
}
