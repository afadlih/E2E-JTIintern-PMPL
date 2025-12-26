<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MMahasiswaSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_mahasiswa')->insert([
            [
                'id_user'    => 3,
                'id_kelas'   => 1,
                'nim'        => '2141720001',
                'alamat'     => 'Jl. Sudirman No. 10',
                'ipk'        => 3.85,
                'telp'       => '081234567891',
                'cv'         => 'cv_mahasiswa1.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user'    => 4,
                'id_kelas'   => 1,
                'nim'        => '2141720002',
                'alamat'     => 'Jl. Gatot Subroto No. 15',
                'ipk'        => 3.90,
                'telp'       => '081234567892',
                'cv'         => 'cv_mahasiswa2.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user'    => 5,
                'id_kelas'   => 2,
                'nim'        => '2141720003',
                'alamat'     => 'Jl. Ahmad Yani No. 20',
                'ipk'        => 3.70,
                'telp'       => '081234567893',
                'cv'         => 'cv_mahasiswa3.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user'    => 6,
                'id_kelas'   => 2,
                'nim'        => '2141720004',
                'alamat'     => 'Jl. Diponegoro No. 25',
                'ipk'        => 3.95,
                'telp'       => '081234567894',
                'cv'         => 'cv_mahasiswa4.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
