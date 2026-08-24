<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class AssignRolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['pelatih', 'asisten'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $map = [
            'nasrul@bmc.test' => ['role' => 'pelatih', 'level' => 'senior'],
            'zidan@bmc.test' => ['role' => 'asisten', 'level' => 'senior'],
            'amira@bmc.test' => ['role' => 'pelatih', 'level' => 'beginner'],
            'putri@bmc.test' => ['role' => 'asisten', 'level' => 'beginner'],
            'danur@bmc.test' => ['role' => 'pelatih', 'level' => 'pemula'],
            'candra@bmc.test' => ['role' => 'asisten', 'level' => 'pemula'],
        ];

        foreach ($map as $email => $data) {
            $u = User::where('email', $email)->first();
            if ($u) {
                $u->assignRole($data['role']);
                $u->level = $data['level'];
                $u->save();
            }
        }
    }
}
