<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WilayahSeeder extends Seeder
{
    
    public function run(): void
    {
        $wilayah = [
            ['nama_kota' => 'Surabaya'],
            ['nama_kota' => 'Malang'],
            ['nama_kota' => 'Sidoarjo'],
            ['nama_kota' => 'Gresik'],
            ['nama_kota' => 'Pasuruan'],
            ['nama_kota' => 'Mojokerto'],
            ['nama_kota' => 'Kediri'],
            ['nama_kota' => 'Madiun'],
            ['nama_kota' => 'Blitar'],
            ['nama_kota' => 'Probolinggo'],
            ['nama_kota' => 'Batu'],
            ['nama_kota' => 'Jember'],
            ['nama_kota' => 'Banyuwangi'],
            ['nama_kota' => 'Tulungagung'],
            ['nama_kota' => 'Trenggalek'],
            ['nama_kota' => 'Bondowoso'],
            ['nama_kota' => 'Lumajang'],
            ['nama_kota' => 'Nganjuk'],
            ['nama_kota' => 'Bojonegoro'],
            ['nama_kota' => 'Tuban'],
            ['nama_kota' => 'Lamongan'],
            ['nama_kota' => 'Pamekasan'],
            ['nama_kota' => 'Sampang'],
            ['nama_kota' => 'Sumenep'],
            ['nama_kota' => 'Bangkalan'],
            ['nama_kota' => 'Situbondo'],
            ['nama_kota' => 'Jombang'],
            ['nama_kota' => 'Ngawi'],
            ['nama_kota' => 'Magetan'],
            ['nama_kota' => 'Ponorogo'],
            ['nama_kota' => 'Pacitan']
        ];

        foreach ($wilayah as $w) {
            $w['created_at'] = now();
            $w['updated_at'] = now();
            DB::table('m_wilayah')->insert($w);
        }
    }
}
