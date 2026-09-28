<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    /**
     * Menampilkan materi pembelajaran / dashboard siswa
     */
    public function index(Request $request)
    {
        // Cek apakah ada data di database
        $babList = [];
        try {
            if (class_exists(Bab::class)) {
                $dbBab = Bab::with('materi')->get();
                if ($dbBab->isNotEmpty()) {
                    $babList = $dbBab;
                }
            }
        } catch (\Throwable $e) {
            // Fallback jika database belum dimigrasi/ada kendala
            $babList = [];
        }

        // Default mock data sesuai visual jika database belum ada isinya
        $defaultBab = [
            [
                'kode' => 'BAB I',
                'judul' => 'Menyusun Teks Laporan Hasil Observasi',
                'deskripsi' => 'Mempelajari cara mengamati lingkungan secara objektif, merancang laporan sistematis, dan mengidentifikasi kaidah ilmiah bahasa Indonesia.',
                'materi' => [
                    [
                        'tipe' => 'PDF',
                        'judul' => 'Pengertian & Struktur Teks Laporan Hasil Observasi (LHO)',
                        'tanggal' => '12 Okt 2024',
                        'estimasi' => '15 mnt',
                        'aksi' => 'unduh',
                        'url' => '#',
                    ],
                    [
                        'tipe' => 'VIDEO',
                        'judul' => 'Video Penjelasan Menyimak Struktur Teks LHO Secara Interaktif',
                        'tanggal' => '15 Okt 2024',
                        'estimasi' => '15 mnt',
                        'aksi' => 'buka',
                        'url' => '#',
                    ],
                    [
                        'tipe' => 'DOKUMEN',
                        'judul' => 'Lembar Kerja Siswa Mandiri: Analisis Kaidah Kebahasaan',
                        'tanggal' => '16 Okt 2024',
                        'estimasi' => '15 mnt',
                        'aksi' => 'unduh',
                        'url' => '#',
                    ],
                    [
                        'tipe' => 'PDF',
                        'judul' => 'Contoh Nyata Laporan Hasil Observasi Eksosistem Sungai',
                        'tanggal' => '18 Okt 2024',
                        'estimasi' => '15 mnt',
                        'aksi' => 'unduh',
                        'url' => '#',
                    ],
                ],
            ],
            [
                'kode' => 'BAB II',
                'judul' => 'Mengungkapkan Kritik lewat Teks Eksposisi',
                'deskripsi' => 'Melatih berpikir kritis, menyusun tesis yang berlandaskan argumentasi logis, dan menyajikan fakta opini berimbang di media massa.',
                'materi' => [
                    [
                        'tipe' => 'PDF',
                        'judul' => 'Mengenal Opini & Fakta dalam Teks Eksposisi Media Massa',
                        'tanggal' => '24 Okt 2024',
                        'estimasi' => '15 mnt',
                        'aksi' => 'unduh',
                        'url' => '#',
                    ],
                    [
                        'tipe' => 'VIDEO',
                        'judul' => 'Strategi Menulis Argumen Eksposisi yang Kuat & Logis',
                        'tanggal' => '26 Okt 2024',
                        'estimasi' => '15 mnt',
                        'aksi' => 'buka',
                        'url' => '#',
                    ],
                    [
                        'tipe' => 'DOKUMEN',
                        'judul' => 'Template Kerangka Menulis Esai Teks Eksposisi Populer',
                        'tanggal' => '27 Okt 2024',
                        'estimasi' => '15 mnt',
                        'aksi' => 'unduh',
                        'url' => '#',
                    ],
                ],
            ],
        ];

        return view('siswa.materi', [
            'daftarBab' => !empty($babList) ? $babList : $defaultBab,
            'isDbData' => !empty($babList),
        ]);
    }
}
