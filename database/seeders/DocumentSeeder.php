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
                'id_user' => 3, // Pastikan user dengan ID 1 ada di tabel users
                'file_name' => 'cv_admin.pdf',
                'file_path' => 'uploads/documents/cv_admin.pdf',
                'file_type' => 'CV',
                'description' => 'Curriculum Vitae for Admin User',
                'upload_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user' => 3, // Pastikan user dengan ID 2 ada di tabel users
                'file_name' => 'cover_letter_admin.pdf',
                'file_path' => 'uploads/documents/cover_letter_admin.pdf',
                'file_type' => 'Surat Pengantar',
                'description' => 'Cover Letter for Admin User',
                'upload_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_user' => 3, // Pastikan user dengan ID 3 ada di tabel users
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
