<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MMagangSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_magang')->insert([
            [
                'id_lowongan'  => 2, // Pastikan id_lowongan 1 ada di m_lowongan
                'id_mahasiswa' => 1, // Pastikan id_mahasiswa 1 ada di m_mahasiswa
                'id_dosen'     => 1, // Pastikan id_dosen 1 ada di m_dosen (atau null jika tidak ada)
                'status'       => 'tidak aktif',
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);
    }
}