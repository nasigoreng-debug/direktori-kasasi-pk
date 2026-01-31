<?php

namespace Database\Seeders;

use App\Models\Pengadilan;
use Illuminate\Database\Seeder;

class PengadilanSeeder extends Seeder
{
    public function run(): void
    {
        $pengadilan = [
            // Wilayah Bandung Raya & Sekitarnya
            ['kode' => 'PA-BDG', 'nama' => 'Pengadilan Agama Bandung', 'wilayah' => 'Bandung Raya', 'kelas' => 'IA'],
            ['kode' => 'PA-CMH', 'nama' => 'Pengadilan Agama Cimahi', 'wilayah' => 'Bandung Raya', 'kelas' => null],
            ['kode' => 'PA-SRG', 'nama' => 'Pengadilan Agama Soreang', 'wilayah' => 'Bandung Raya', 'kelas' => null],
            ['kode' => 'PA-SMD', 'nama' => 'Pengadilan Agama Sumedang', 'wilayah' => 'Bandung Raya', 'kelas' => null],
            ['kode' => 'PA-NGP', 'nama' => 'Pengadilan Agama Ngamprah', 'wilayah' => 'Bandung Raya', 'kelas' => null],

            // Wilayah Bodebekkar
            ['kode' => 'PA-BGR', 'nama' => 'Pengadilan Agama Bogor', 'wilayah' => 'Bodebekkar', 'kelas' => null],
            ['kode' => 'PA-CBN', 'nama' => 'Pengadilan Agama Cibinong', 'wilayah' => 'Bodebekkar', 'kelas' => null],
            ['kode' => 'PA-DPK', 'nama' => 'Pengadilan Agama Depok', 'wilayah' => 'Bodebekkar', 'kelas' => null],
            ['kode' => 'PA-BKS', 'nama' => 'Pengadilan Agama Bekasi', 'wilayah' => 'Bodebekkar', 'kelas' => null],
            ['kode' => 'PA-CKR', 'nama' => 'Pengadilan Agama Cikarang', 'wilayah' => 'Bodebekkar', 'kelas' => null],
            ['kode' => 'PA-KRW', 'nama' => 'Pengadilan Agama Karawang', 'wilayah' => 'Bodebekkar', 'kelas' => null],
            ['kode' => 'PA-PWK', 'nama' => 'Pengadilan Agama Purwakarta', 'wilayah' => 'Bodebekkar', 'kelas' => null],

            // Wilayah Sukabumi & Cianjur
            ['kode' => 'PA-SKB', 'nama' => 'Pengadilan Agama Sukabumi', 'wilayah' => 'Sukabumi & Cianjur', 'kelas' => null],
            ['kode' => 'PA-CBD', 'nama' => 'Pengadilan Agama Cibadak', 'wilayah' => 'Sukabumi & Cianjur', 'kelas' => null],
            ['kode' => 'PA-CJR', 'nama' => 'Pengadilan Agama Cianjur', 'wilayah' => 'Sukabumi & Cianjur', 'kelas' => null],

            // Wilayah Ciayumajakuning
            ['kode' => 'PA-CRB', 'nama' => 'Pengadilan Agama Cirebon', 'wilayah' => 'Ciayumajakuning', 'kelas' => null],
            ['kode' => 'PA-SMR', 'nama' => 'Pengadilan Agama Sumber', 'wilayah' => 'Ciayumajakuning', 'kelas' => null],
            ['kode' => 'PA-IDM', 'nama' => 'Pengadilan Agama Indramayu', 'wilayah' => 'Ciayumajakuning', 'kelas' => null],
            ['kode' => 'PA-MJK', 'nama' => 'Pengadilan Agama Majalengka', 'wilayah' => 'Ciayumajakuning', 'kelas' => null],
            ['kode' => 'PA-KNG', 'nama' => 'Pengadilan Agama Kuningan', 'wilayah' => 'Ciayumajakuning', 'kelas' => null],

            // Wilayah Priangan Timur
            ['kode' => 'PA-CMS', 'nama' => 'Pengadilan Agama Ciamis', 'wilayah' => 'Priangan Timur', 'kelas' => null],
            ['kode' => 'PA-TSK', 'nama' => 'Pengadilan Agama Tasikmalaya', 'wilayah' => 'Priangan Timur', 'kelas' => null],
            ['kode' => 'PA-TSK-K', 'nama' => 'Pengadilan Agama Kota Tasikmalaya', 'wilayah' => 'Priangan Timur', 'kelas' => null],
            ['kode' => 'PA-GRT', 'nama' => 'Pengadilan Agama Garut', 'wilayah' => 'Priangan Timur', 'kelas' => null],
            ['kode' => 'PA-BJR', 'nama' => 'Pengadilan Agama Kota Banjar', 'wilayah' => 'Priangan Timur', 'kelas' => null],
        ];

        foreach ($pengadilan as $data) {
            Pengadilan::create($data);
        }
    }
}
