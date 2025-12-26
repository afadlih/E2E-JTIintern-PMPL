<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TSkillMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('t_skill_mahasiswa')->insert([
            [
                'user_id' => 3,
                'skill_id' => 1,
                'lama_skill' => 12,
            ],
            [
                'user_id' => 4,
                'skill_id' => 3,
                'lama_skill' => 8,
            ],
            [
                'user_id' => 5,
                'skill_id' => 2,
                'lama_skill' => 6,
            ],
            [
                'user_id' => 6,
                'skill_id' => 4,
                'lama_skill' => 10,
            ],
            [
                'user_id' => 3,
                'skill_id' => 2,
                'lama_skill' => 6,
            ],
            [
                'user_id' => 4,
                'skill_id' => 1,
                'lama_skill' => 12,
            ]
        ]);
    }
}
