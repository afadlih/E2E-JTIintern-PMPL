<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_dokumen')->insert([
            [
                'id_user' => 3, // First mahasiswa
                'file_name' => 'cv_mahasiswa1.pdf',
                'file_path' => 'uploads/documents/cv_mahasiswa1.pdf',
                'file_type' => 'CV',
                'description' => 'Curriculum Vitae for Mahasiswa 1',
                'upload_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user' => 4, // Second mahasiswa
                'file_name' => 'cover_letter_mahasiswa2.pdf',
                'file_path' => 'uploads/documents/cover_letter_mahasiswa2.pdf',
                'file_type' => 'Surat Pengantar',
                'description' => 'Cover Letter for Mahasiswa 2',
                'upload_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user' => 5, // Third mahasiswa
                'file_name' => 'certificate_internship.pdf',
                'file_path' => 'uploads/documents/certificate_internship.pdf',
                'file_type' => 'Sertifikat',
                'description' => 'Internship Completion Certificate',
                'upload_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
