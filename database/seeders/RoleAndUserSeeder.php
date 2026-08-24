<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder; // <-- Sudah diperbaiki (LL)
use Illuminate\Support\Facades\Hash; // <-- Sudah diperbaiki (LL)
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        // 1. Buat role
        $pelatihRole = Role::firstOrCreate(['name' => 'pelatih']);
        $asistenRole = Role::firstOrCreate(['name' => 'asisten']);

        // 2. Data 3 Pelatih Utama
        $pelatihUtama = [
            ['name' => 'Danur', 'email' => 'danur@bmc.test'],
            ['name' => 'Amira', 'email' => 'amira@bmc.test'],
            ['name' => 'Nasrul', 'email' => 'nasrul@bmc.test'],
        ];

        foreach ($pelatihUtama as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );
            $user->assignRole($pelatihRole);
        }

        // 3. Data 3 Asisten Pelatih
        $asistenPelatih = [
            ['name' => 'Candra', 'email' => 'candra@bmc.test'],
            ['name' => 'Putri', 'email' => 'putri@bmc.test'],
            ['name' => 'Zidan', 'email' => 'zidan@bmc.test'],
        ];

        foreach ($asistenPelatih as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                ]
            );
            $user->assignRole($asistenRole);
        }

        $this->command->info('6 akun staff berhasil dibuat (3 pelatih, 3 asisten). Password default: password');
    }
}