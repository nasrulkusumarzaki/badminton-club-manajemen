<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan seeder utama.
     */
    public function run(): void
    {
        $this->call([
            RoleAndUserSeeder::class,
            AtletSeeder::class,
        ]);
    }
}