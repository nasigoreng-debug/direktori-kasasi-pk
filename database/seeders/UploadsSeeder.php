<?php

namespace Database\Seeders;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Database\Seeder;

class UploadsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        
        foreach ($users as $user) {
            // Buat 5 data dummy upload untuk setiap user
            for ($i = 1; $i <= 5; $i++) {
                $jenis = $i % 2 == 0 ? 'pk' : 'kasasi';
                
                Upload::create([
                    'nomor_perkara_pa' => "00{$i}/Pdt.G/2024/PA." . substr($user->pengadilan->kode, 3),
                    'nomor_perkara_banding' => "00{$i}/Pdt.G/2024/PTA." . substr($user->pengadilan->kode, 3),
                    'nomor_perkara_kasasi' => "00{$i}/K/AG/2024",
                    'nomor_perkara_pk' => $jenis == 'pk' ? "00{$i}/PK/AG/2024" : null,
                    'jenis_putusan' => $jenis,
                    'tanggal_putusan' => now()->subDays(rand(1, 30)),
                    'file_path' => 'uploads/test/dummy.pdf',
                    'original_filename' => 'putusan_' . $i . '.pdf',
                    'file_size' => rand(1000, 5000),
                    'status' => 'submitted',
                    'user_id' => $user->id,
                    'pengadilan_id' => $user->pengadilan_id,
                ]);
            }
        }
        
        echo "Created " . Upload::count() . " upload records\n";
    }
}