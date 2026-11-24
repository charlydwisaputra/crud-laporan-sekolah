<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin / Guru Sarpras',
                'email' => 'admin@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'kelas_atau_unit' => 'Tim Sarpras Sekolah',
            ],
            [
                'name' => 'Budi Siswa',
                'email' => 'siswa@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'kelas_atau_unit' => 'XI RPL 1',
            ],
            [
                'name' => 'Siti Siswa',
                'email' => 'siti.siswa@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'siswa',
                'kelas_atau_unit' => 'XI TKJ 2',
            ],
            [
                'name' => 'Pak Anton Petugas',
                'email' => 'petugas@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'kelas_atau_unit' => 'Teknisi Umum & Listrik',
            ],
            [
                'name' => 'Pak Dedi Petugas',
                'email' => 'dedi.petugas@sekolah.sch.id',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'kelas_atau_unit' => 'Sarana & Bangunan',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}