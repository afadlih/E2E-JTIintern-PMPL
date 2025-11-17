<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MJenisSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_jenis')->insert([
            [
                'nama_jenis' => 'Work from Office',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis' => 'Work from Home',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_jenis' => 'Hybrid',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}