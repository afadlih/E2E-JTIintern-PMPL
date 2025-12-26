<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TEvaluasiSeeder extends Seeder
{
    public function run()
    {
        DB::table('t_evaluasi')->insert([
            [
                'id_magang' => 1,
                'nilai_perusahaan' => 85.50,
                'nilai_dosen' => 90.00,
                'nilai_akhir' => 87.75,
                'grade' => 'A',
                'catatan_dosen' => 'Sangat baik, aktif dalam setiap tugas dan disiplin.',
                'file_penilaian_perusahaan' => 'evaluasi_perusahaan_1.pdf',
                'status_evaluasi' => 'completed',
                'tanggal_submit_perusahaan' => now()->subDays(5),
                'tanggal_evaluasi_dosen' => now()->subDays(2),
                'created_at'=> now(),
                'updated_at'=> now(),
            ]
        ]);
    }
}