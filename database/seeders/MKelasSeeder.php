<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MKelasSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = [
            [
                'nama_kelas' => 'TI-3A',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3B',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3C',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3D',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3E',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3F',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3G',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3H',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'TI-3I',
                'kode_prodi' => 'TI',
                'tahun_masuk' => 2022,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('m_kelas')->insert($kelas);
    }
}