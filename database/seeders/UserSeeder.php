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
            'email' => 'sample_mahasiswa1@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ],
        [
            'name' => 'Indira Nafa',
            'email' => 'sample_mahasiswa2@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ],
        [
            'name' => 'Austriech',
            'email' => 'sample_mahasiswa3@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ],
        [
            'name' => 'Moch Fauzi',
            'email' => 'sample_mahasiswa4@example.com',
            'password' => Hash::make('mahasiswa'),
            'role' => 'mahasiswa',
        ]
    ];
        // Membuat atau update user admin, dosen, dan contoh mahasiswa secara idempotent
        // Menggunakan kredensial yang umum dipakai di environment test / developer
        DB::table('m_user')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('admin'), // plain password: 'admin'
                'role' => 'admin',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('m_user')->updateOrInsert(
            ['email' => '1980031@gmail.com'],
            [
                'name' => 'Dosen User',
                'email_verified_at' => now(),
                'password' => Hash::make('1980031'), // plain password: '1980031'
                'role' => 'dosen',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('m_user')->updateOrInsert(
            ['email' => '2341720074@student.com'],
            [
                'name' => 'Mahasiswa User',
                'email_verified_at' => now(),
                'password' => Hash::make('2341720074'), // plain password: '2341720074'
                'role' => 'mahasiswa',
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        foreach ($mahasiswa_users as $user) {
            DB::table('m_user')->insert([
                'name' => $user['name'],
                'email' => $user['email'],
                'email_verified_at' => now(),
                // override to use 'secret' so tests are consistent
                'password' => Hash::make('secret'),
                'role' => $user['role'],
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
