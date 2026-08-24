<?php

namespace Database\Seeders;

use App\Models\Atlet;
use Illuminate\Database\Seeder;

class AtletSeeder extends Seeder
{
    public function run(): void
    {
        $dummy = [
            ['nama' => 'Afham',      'umur' => 10, 'jenis_kelamin' => 'Laki-laki', 'level' => 'pemula'],
            ['nama' => 'Alfian',         'umur' => 11, 'jenis_kelamin' => 'Laki-laki', 'level' => 'pemula'],
            ['nama' => 'Tisya',    'umur' => 9,  'jenis_kelamin' => 'Perempuan', 'level' => 'pemula'],
            ['nama' => 'Khalifi',      'umur' => 13, 'jenis_kelamin' => 'Laki-laki', 'level' => 'beginner'],
            ['nama' => 'Khansa',    'umur' => 14, 'jenis_kelamin' => 'Perempuan', 'level' => 'beginner'],
            ['nama' => 'Ara', 'umur' => 13, 'jenis_kelamin' => 'Perempuan', 'level' => 'beginner'],
            ['nama' => 'Arsya',     'umur' => 13, 'jenis_kelamin' => 'Laki-laki', 'level' => 'beginner'],
            ['nama' => 'Abid aqil',    'umur' => 13, 'jenis_kelamin' => 'Laki-laki', 'level' => 'senior'],
            ['nama' => 'Azza',  'umur' => 15, 'jenis_kelamin' => 'Perempuan', 'level' => 'senior'],
            ['nama' => 'Nadya',      'umur' => 15, 'jenis_kelamin' => 'Perempuan', 'level' => 'senior'],
        ];

        foreach ($dummy as $data) {
            Atlet::firstOrCreate(
                ['nama' => $data['nama']],
                $data
            );
        }

        $this->command->info('10 data atlet dummy berhasil ditambahkan.');
    }
}