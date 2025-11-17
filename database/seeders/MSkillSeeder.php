<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_skill')->insert([
            ['nama' => 'PHP', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'JavaScript', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'UI/UX Design', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Project Management', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Data Analysis', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}