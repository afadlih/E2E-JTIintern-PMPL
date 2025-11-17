<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MMahasiswaSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_mahasiswa')->insert([
            // [
            //     'id_user'    => 3, // Pastikan id_user=3 ada di m_user
            //     'kode_prodi' => 'TI', // Pastikan kode_prodi ini ada di m_prodi
            //     'skill_id'   => 2, // Pastikan skill_id ini ada di m_skill
            //     'jenis_id'   => 1, // Pastikan jenis_id ini ada di m_jenis
            //     'nim'        => 12345678,
            //     'alamat'     => 'Jl. Merdeka No. 1',
            //     'ipk'        => 3.75,
            //     'telp'       => '081234567890',
            //     'cv'         => 'cv_mahasiswa1.pdf',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            [
                'id_user'    => 4,
                'kode_prodi' => 'TI',
                'nim'        => '2141720002',
                'alamat'     => 'Jl. Sudirman No. 10',
                'ipk'        => 3.85,
                'telp'       => '081234567891',
                'cv'         => 'cv_mahasiswa2.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user'    => 5,
                'kode_prodi' => 'TI',
                'nim'        => '2141720003',
                'alamat'     => 'Jl. Gatot Subroto No. 15',
                'ipk'        => 3.90,
                'telp'       => '081234567892',
                'cv'         => 'cv_mahasiswa3.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user'    => 6,
                'kode_prodi' => 'TI',
                'nim'        => '2141720004',
                'alamat'     => 'Jl. Ahmad Yani No. 20',
                'ipk'        => 3.70,
                'telp'       => '081234567893',
                'cv'         => 'cv_mahasiswa4.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user'    => 7,
                'kode_prodi' => 'TI',
                'nim'        => '2141720005',
                'alamat'     => 'Jl. Diponegoro No. 25',
                'ipk'        => 3.95,
                'telp'       => '081234567894',
                'cv'         => 'cv_mahasiswa5.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
