<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MDosenSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_dosen')->insert([
            [
                'user_id' => 2, // Pastikan user dengan id_user=2 ada di m_user
                'nip' => 1987654321,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}