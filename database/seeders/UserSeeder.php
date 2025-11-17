<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

         $mahasiswa_users = [
        [
            'name' => 'Moch Ammar',
            'email' => 'mahasiswa1@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ],
        [
            'name' => 'Indira Nafa',
            'email' => 'mahasiswa2@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ],
        [
            'name' => 'Austriech',
            'email' => 'mahasiswa3@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ],
        [
            'name' => 'Moch Fauzi',
            'email' => 'mahasiswa4@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ]
    ];
        // Membuat user admin
        // DB::table('m_user')->insert([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('admin'), // Ganti 'password' dengan password yang lebih kuat
        //     'role' => 'admin',
        //     'remember_token' => Str::random(10),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // // Membuat user dosen
        // DB::table('m_user')->insert([
        //     'name' => 'Dosen User',
        //     'email' => 'dosen@example.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('dosen'),  // Ganti 'password' dengan password yang lebih kuat
        //     'role' => 'dosen',
        //     'remember_token' => Str::random(10),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // // Membuat user mahasiswa
        // DB::table('m_user')->insert([
        //     'name' => 'Mahasiswa User',
        //     'email' => 'mahasiswa@example.com',
        //     'email_verified_at' => now(),
        //     'password' => Hash::make('mahasiswa'),  // Ganti 'password' dengan password yang lebih kuat
        //     'role' => 'mahasiswa',
        //     'remember_token' => Str::random(10),
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

         foreach ($mahasiswa_users as $user) {
        DB::table('m_user')->insert([
            'name' => $user['name'],
            'email' => $user['email'],
            'email_verified_at' => now(),
            'password' => $user['password'],
            'role' => $user['role'],
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    }
}
