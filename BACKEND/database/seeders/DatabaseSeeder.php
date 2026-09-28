<?php

namespace Database\Seeders;

use App\Models\Bab;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Data Guru & Akun Login (via GuruSeeder)
        $this->call(GuruSeeder::class);

        // 2. Seed Data Master Siswa
        $siswaData = [
            ['nis' => '1023001', 'nama' => 'Ahmad Fauzi', 'rombel' => 'PPLG X-3', 'rayon' => 'Ciawi 1'],
            ['nis' => '1023002', 'nama' => 'Ahmad Rizky', 'rombel' => 'PPLG X-3', 'rayon' => 'Cicurug 2'],
            ['nis' => '1023003', 'nama' => 'Ahmad Yusuf', 'rombel' => 'PPLG X-3', 'rayon' => 'Wikrama 1'],
            ['nis' => '1023004', 'nama' => 'Siti Ahmad', 'rombel' => 'PPLG X-3', 'rayon' => 'Cisarua 3'],
            ['nis' => '1023005', 'nama' => 'Budi Pratama', 'rombel' => 'PPLG X-3', 'rayon' => 'Tajur 1'],
            ['nis' => '1023006', 'nama' => 'Citra Lestari', 'rombel' => 'DKV XI-3', 'rayon' => 'Ciawi 2'],
            ['nis' => '1023007', 'nama' => 'Dimas Aditya', 'rombel' => 'PPLG XI-5', 'rayon' => 'Sukasari 1'],
            ['nis' => '1023008', 'nama' => 'Eka Ramadhan', 'rombel' => 'TKJ XI-3', 'rayon' => 'Cibedug 1'],
            ['nis' => '1023009', 'nama' => 'Fajar Nugraha', 'rombel' => 'PPLG X-3', 'rayon' => 'Ciawi 3'],
            ['nis' => '1023010', 'nama' => 'Gita Permata', 'rombel' => 'PPLG X-3', 'rayon' => 'Cicurug 1'],
        ];

        foreach ($siswaData as $item) {
            Siswa::firstOrCreate(['nis' => $item['nis']], $item);
        }

        // 3. Seed Data Master Bab
        $babData = [
            ['nama_bab' => 'BAB I — Menyusun Teks Laporan Hasil Observasi (LHO)'],
            ['nama_bab' => 'BAB II — Mengungkapkan Kritik lewat Teks Eksposisi'],
            ['nama_bab' => 'BAB III — Menyusuri Nilai dalam Cerita Lintas Zaman'],
            ['nama_bab' => 'BAB IV — Belajar Menjadi Negosiator Ulung'],
            ['nama_bab' => 'BAB V — Memetik Keteladanan dari Biografi Tokoh'],
        ];

        foreach ($babData as $item) {
            Bab::firstOrCreate(['nama_bab' => $item['nama_bab']]);
        }
    }
}
