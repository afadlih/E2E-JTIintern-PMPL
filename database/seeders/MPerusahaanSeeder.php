<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MPerusahaanSeeder extends Seeder
{
    public function run()
    {
        DB::table('m_perusahaan')->insert([
            [
                'nama_perusahaan' => 'PT Telkom Indonesia',
                'alamat_perusahaan' => 'Jl. Japati No.1, Bandung',
                'kota' => 'Bandung',
                'contact_person' => '0221234567',
                'email' => 'info@telkom.co.id',
                'instagram' => 'telkomindonesia',
                'website' => 'https://www.telkom.co.id',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT Gojek Indonesia',
                'alamat_perusahaan' => 'Pasaraya Blok M, Jakarta Selatan',
                'kota' => 'Jakarta',
                'contact_person' => '0217654321',
                'email' => 'info@gojek.com',
                'instagram' => 'gojekindonesia',
                'website' => 'https://www.gojek.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT Bukalapak',
                'alamat_perusahaan' => 'Jl. Kemang Timur No.22, Jakarta Selatan',
                'kota' => 'Jakarta',
                'contact_person' => '0219876543',
                'email' => 'info@bukalapak.com',
                'instagram' => 'bukalapak',
                'website' => 'https://www.bukalapak.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT Tokopedia',
                'alamat_perusahaan' => 'Jl. Karet Pasar Baru Barat No.5, Jakarta Selatan',
                'kota' => 'Jakarta',
                'contact_person' => '0218765432',
                'email' => 'info@tokopedia.com',
                'instagram' => 'tokopedia',
                'website' => 'https://www.tokopedia.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_perusahaan' => 'PT Traveloka Indonesia',
                'alamat_perusahaan' => 'Jl. Kebon Sirih No.17-19, Jakarta Pusat',
                'kota' => 'Jakarta',
                'contact_person' => '0216543210',
                'email' => 'info@traveloka.com',
                'instagram' => 'traveloka',
                'website' => 'https://www.traveloka.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}