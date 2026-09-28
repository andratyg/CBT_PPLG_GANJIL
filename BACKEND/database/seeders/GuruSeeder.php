<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guruData = [
            [
                'nama' => 'Pak Budi Santoso, S.Kom.',
                'email' => 'budi.santoso@sekolah.sch.id',
                'password' => Hash::make('password123'),
            ],
            [
                'nama' => 'Ibu Yayu Sri Rahayu, S.Pd.',
                'email' => 'yayu.srirahayu@sekolah.sch.id',
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($guruData as $data) {
            // Seed ke tabel guru
            Guru::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            // Seed juga ke tabel users agar bisa login via auth() Laravel standar
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['nama'],
                    'password' => $data['password'],
                ]
            );
        }
    }
}
