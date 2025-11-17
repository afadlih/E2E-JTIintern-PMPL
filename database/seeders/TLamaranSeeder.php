<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TLamaranSeeder extends Seeder
{
    public function run()
    {
        DB::table('t_lamaran')->insert([
            [
                'id_lowongan'      => 1, // Pastikan id_lowongan 1 ada di m_lowongan
                'id_mahasiswa'     => 1, // Pastikan id_mahasiswa 1 ada di m_mahasiswa
                'id_dosen'         => 1, // Pastikan id_dosen 1 ada di m_dosen (atau null jika tidak ada)
                'id_dokumen'       => null, // Isi jika ada tabel dokumen
                'auth'             => 'menunggu',
                'tanggal_lamaran'  => now()->toDateString(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ],
        ]);
    }
}