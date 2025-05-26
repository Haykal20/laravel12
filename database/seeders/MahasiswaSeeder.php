<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run()
    {
        Mahasiswa::create([
            'nim' => '12345678',
            'nama' => 'Dewi',
            'semester' => 3,
            'mata_kuliah' => 'Pemrograman Web'
        ]);
        
        // Tambahkan data lain sesuai kebutuhan
    }
}