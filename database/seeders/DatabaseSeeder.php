<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password123'), // Passwordnya: password123
            'role' => 'admin',
        ]);

        // 2. Buat Akun Pimpinan
        User::create([
            'name' => 'Kepala BPS PALI',
            'username' => 'pimpinan',
            'password' => Hash::make('password123'), // Passwordnya: password123
            'role' => 'pimpinan',
        ]);

        // 3. Buat 5 Kecamatan PALI (Sesuai Skripsi Anda)
        $kecamatans = ['Talang Ubi', 'Penukal', 'Penukal Utara', 'Abab', 'Tanah Abang'];

        foreach ($kecamatans as $kecamatan) {
            Kecamatan::create([
                'nama_kecamatan' => $kecamatan,
                'status_validasi' => 'draft',
            ]);
        }

        // 4. Buat beberapa Data Desa Dummy
        \App\Models\Desa::create([
            'kecamatan_id' => 1, // ID 1 = Talang Ubi
            'nama_desa' => 'Desa Talang Akar' // Ini akan mendapat ID 1 di tabel desa
        ]);

        \App\Models\Desa::create([
            'kecamatan_id' => 1,
            'nama_desa' => 'Desa Pendopo' // Ini akan mendapat ID 2 di tabel desa
        ]);
    }

    // use WithoutModelEvents;

    // /**
    //  * Seed the application's database.
    //  */
    // public function run(): void
    // {
    //     // User::factory(10)->create();

    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    // }
}
