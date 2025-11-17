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
                'perusahaan_id'   => 1, // Pastikan perusahaan_id 1 ada di m_perusahaan
                'periode_id'      => 1, // Pastikan periode_id 1 ada di m_periode
                'skill_id'        => 1, // Pastikan skill_id 1 ada di m_skill
                'jenis_id'        => 1, // Pastikan jenis_id 1 ada di m_jenis
                'judul_lowongan'  => 'Backend Developer Intern',
                'kapasitas'       => 3,
                'deskripsi'       => 'Membantu pengembangan API dan backend aplikasi.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'perusahaan_id'   => 2,
                'periode_id'      => 2,
                'skill_id'        => 2,
                'jenis_id'        => 2,
                'judul_lowongan'  => 'Frontend Developer Intern',
                'kapasitas'       => 2,
                'deskripsi'       => 'Membantu pengembangan UI/UX aplikasi web.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'perusahaan_id'   => 3,
                'periode_id'      => 3,
                'skill_id'        => 3,
                'jenis_id'        => 3,
                'judul_lowongan'  => 'Data Analyst Intern',
                'kapasitas'       => 1,
                'deskripsi'       => 'Menganalisis data dan membuat laporan bisnis.',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
        ]);
    }
}