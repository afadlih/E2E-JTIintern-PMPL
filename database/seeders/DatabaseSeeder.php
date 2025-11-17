<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            MSkillSeeder::class,
            MProdiSeeder::class,
            MJenisSeeder::class,
            MMahasiswaSeeder::class,
            MDosenSeeder::class,
            MPerusahaanSeeder::class,
            MPeriodeSeeder::class,
            MLowonganSeeder::class,
            TLamaranSeeder::class,
            TSkillMahasiswaSeeder::class,
            MMagangSeeder::class,
            TLogSeeder::class,
            TEvaluasiSeeder::class,
            DocumentSeeder::class,
            WilayahSeeder::class
        ]);
    }
}
