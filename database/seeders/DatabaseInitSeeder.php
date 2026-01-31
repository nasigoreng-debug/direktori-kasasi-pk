<?php

namespace Database\Seeders;

use App\Models\Pengadilan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseInitSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat data pengadilan
        $pengadilanData = [
            ['kode' => 'PA-BDG', 'nama' => 'Pengadilan Agama Bandung', 'wilayah' => 'Bandung Raya', 'kelas' => 'IA'],
            ['kode' => 'PA-CMH', 'nama' => 'Pengadilan Agama Cimahi', 'wilayah' => 'Bandung Raya', 'kelas' => null],
            ['kode' => 'PA-SRG', 'nama' => 'Pengadilan Agama Soreang', 'wilayah' => 'Bandung Raya', 'kelas' => null],
        ];

        foreach ($pengadilanData as $data) {
            Pengadilan::create($data);
        }

        echo "Pengadilan created: " . Pengadilan::count() . "\n";

        // 2. Buat user admin
        $pengadilan = Pengadilan::first();

        User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'pengadilan_id' => $pengadilan->id
        ]);

        User::create([
            'name' => 'User Biasa',
            'email' => 'user@test.com',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'pengadilan_id' => $pengadilan->id
        ]);

        echo "Users created: " . User::count() . "\n";
        echo "\nLogin credentials:\n";
        echo "Admin: admin@test.com / password123\n";
        echo "User: user@test.com / password123\n";
    }
}
