<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MProdiSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_prodi')->insert([
            [
                'kode_prodi' => 'TI',
                'nama_prodi' => 'Teknik Informatika',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_prodi' => 'SIB',
                'nama_prodi' => 'Sistem Informasi Bisnis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}