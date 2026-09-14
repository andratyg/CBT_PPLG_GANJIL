<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Pertemuan;
use App\Models\Jadwal;
use App\Models\User;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\Absensi;
use App\Models\JurnalMengajar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama dengan aman tanpa melanggar foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Absensi::truncate();
        JurnalMengajar::truncate();
        Tugas::truncate();
        Materi::truncate();
        Pertemuan::truncate();
        Jadwal::truncate();
        MataPelajaran::truncate();
        DB::table('kelas_siswa')->truncate();
        Kelas::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Guru Pengampu (Bu Yayu)
        $guru = User::create([
            'name' => 'Bu Yayu',
            'email' => 'guru@cbt.test',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        // Akun guru kedua / alias cbt.com agar sesi login tetap kompatibel
        $guru2 = User::create([
            'name' => 'Bu Yayu',
            'email' => 'guru@cbt.com',
            'password' => bcrypt('password123'),
            'role' => 'guru',
        ]);

        // 2. Daftar Siswa Lintas Program Keahlian (PPLG, TKJ, DKV, MPLB)
        $siswaData = [
            // PPLG
            ['name' => 'Andra Pratama', 'email' => 'siswa@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Ahmad Fadhil Rahman', 'email' => 'fadhil@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Budi Santoso Wibowo', 'email' => 'budi@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Citra Dewi Anggraini', 'email' => 'citra@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Dinda Ayu Lestari', 'email' => 'dinda@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Eko Prasetyo Utomo', 'email' => 'eko@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Farhan Maulana Malik', 'email' => 'farhan@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Gita Permata Sari', 'email' => 'gita@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Haikal Rasyid Ansori', 'email' => 'haikal@cbt.test', 'jurusan' => 'PPLG'],
            ['name' => 'Intan Putri Maharani', 'email' => 'intan@cbt.test', 'jurusan' => 'PPLG'],
            // TKJ
            ['name' => 'Jihan Nur Aini', 'email' => 'jihan@cbt.test', 'jurusan' => 'TKJ'],
            ['name' => 'Kevin Ardiansyah', 'email' => 'kevin@cbt.test', 'jurusan' => 'TKJ'],
            ['name' => 'Larasati Wulandari', 'email' => 'laras@cbt.test', 'jurusan' => 'TKJ'],
            ['name' => 'Muhammad Rizky Pratama', 'email' => 'rizky@cbt.test', 'jurusan' => 'TKJ'],
            ['name' => 'Nadia Amanda Putri', 'email' => 'nadia@cbt.test', 'jurusan' => 'TKJ'],
            // DKV
            ['name' => 'Oscar Pratama', 'email' => 'oscar@cbt.test', 'jurusan' => 'DKV'],
            ['name' => 'Putri Maharani', 'email' => 'putri@cbt.test', 'jurusan' => 'DKV'],
            ['name' => 'Qori Sandi Maulana', 'email' => 'qori@cbt.test', 'jurusan' => 'DKV'],
            ['name' => 'Rania Zahra Salsabila', 'email' => 'rania@cbt.test', 'jurusan' => 'DKV'],
            // MPLB
            ['name' => 'Raditya Bagus Saputra', 'email' => 'radit@cbt.test', 'jurusan' => 'MPLB'],
            ['name' => 'Siti Rahmawati', 'email' => 'siti@cbt.test', 'jurusan' => 'MPLB'],
            ['name' => 'Teguh Wicaksono', 'email' => 'teguh@cbt.test', 'jurusan' => 'MPLB'],
        ];

        $usersByEmail = [];
        foreach ($siswaData as $s) {
            $userObj = User::create([
                'name' => $s['name'],
                'email' => $s['email'],
                'password' => bcrypt('password'),
                'role' => 'siswa',
            ]);
            $usersByEmail[$s['email']] = $userObj;
        }

        // 3. Rombongan Belajar (Kelas) Lintas Kejuruan — Tahun Ajaran 2026/2027
        $kelas12PPLG = Kelas::create(['nama' => 'XII PPLG 1', 'tahun_ajaran' => '2026/2027']);
        $kelas11PPLG = Kelas::create(['nama' => 'XI PPLG 1', 'tahun_ajaran' => '2026/2027']);
        $kelas10PPLG = Kelas::create(['nama' => 'X PPLG 1', 'tahun_ajaran' => '2026/2027']);

        $kelas12TKJ  = Kelas::create(['nama' => 'XII TKJ 1', 'tahun_ajaran' => '2026/2027']);
        $kelas11TKJ  = Kelas::create(['nama' => 'XI TKJ 1', 'tahun_ajaran' => '2026/2027']);

        $kelas11DKV  = Kelas::create(['nama' => 'XI DKV 1', 'tahun_ajaran' => '2026/2027']);

        $kelas10MPLB = Kelas::create(['nama' => 'X MPLB 1', 'tahun_ajaran' => '2026/2027']);

        // Hubungkan siswa ke rombel masing-masing
        $pplgAllIds = [
            $usersByEmail['siswa@cbt.test']->id,
            $usersByEmail['fadhil@cbt.test']->id,
            $usersByEmail['budi@cbt.test']->id,
            $usersByEmail['citra@cbt.test']->id,
            $usersByEmail['dinda@cbt.test']->id,
            $usersByEmail['eko@cbt.test']->id,
            $usersByEmail['farhan@cbt.test']->id,
            $usersByEmail['gita@cbt.test']->id,
            $usersByEmail['haikal@cbt.test']->id,
            $usersByEmail['intan@cbt.test']->id,
        ];
        $kelas12PPLG->siswa()->sync($pplgAllIds);
        $kelas11PPLG->siswa()->sync(array_slice($pplgAllIds, 0, 5));
        $kelas10PPLG->siswa()->sync(array_slice($pplgAllIds, 5, 5));

        $tkjIds = [
            $usersByEmail['jihan@cbt.test']->id,
            $usersByEmail['kevin@cbt.test']->id,
            $usersByEmail['laras@cbt.test']->id,
            $usersByEmail['rizky@cbt.test']->id,
            $usersByEmail['nadia@cbt.test']->id,
        ];
        $kelas12TKJ->siswa()->sync($tkjIds);
        $kelas11TKJ->siswa()->sync(array_slice($tkjIds, 0, 4));

        $dkvIds = [
            $usersByEmail['oscar@cbt.test']->id,
            $usersByEmail['putri@cbt.test']->id,
            $usersByEmail['qori@cbt.test']->id,
            $usersByEmail['rania@cbt.test']->id,
        ];
        $kelas11DKV->siswa()->sync($dkvIds);

        $mplbIds = [
            $usersByEmail['radit@cbt.test']->id,
            $usersByEmail['siti@cbt.test']->id,
            $usersByEmail['teguh@cbt.test']->id,
        ];
        $kelas10MPLB->siswa()->sync($mplbIds);

        // 4. Mata Pelajaran Bahasa Indonesia & Pendukung
        $mapelBindo = MataPelajaran::create([
            'nama' => 'Bahasa Indonesia (Fase F Tingkat Lanjut)',
            'deskripsi' => 'Penguasaan struktur teks ilmiah, teks negosiasi kejuruan, laporan hasil observasi, dan penulisan dokumen teknis sesuai kaidah EYD V lintas program keahlian.',
            'guru_id' => $guru->id,
        ]);

        $mapelKorespondensi = MataPelajaran::create([
            'nama' => 'Korespondensi & Komunikasi Bisnis',
            'deskripsi' => 'Penyusunan surat dinas resmi, proposal penawaran proyek industri, portofolio profesional, dan etika komunikasi dunia kerja.',
            'guru_id' => $guru->id,
        ]);

        $mapelLiterasi = MataPelajaran::create([
            'nama' => 'Literasi Digital & Apresiasi Sastra',
            'deskripsi' => 'Penulisan artikel opini teknologi, resensi buku dan media digital, serta penelaahan nilai moral dan karakter dalam karya sastra kontemporer.',
            'guru_id' => $guru2->id,
        ]);

        // 5. Jadwal Mengajar Bahasa Indonesia Bu Yayu (Semua Jurusan: PPLG, TKJ, DKV, MPLB)
        // Hari Ini: Selasa, 8 September 2026

        // SENIN
        $jSenin1 = Jadwal::create([
            'kelas_id' => $kelas12PPLG->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'senin',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:30',
        ]);
        $jSenin2 = Jadwal::create([
            'kelas_id' => $kelas12TKJ->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'senin',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:45',
        ]);

        // SELASA (HARI INI)
        $jSelasa1 = Jadwal::create([
            'kelas_id' => $kelas12PPLG->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'selasa', // Hari ini
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:30',
        ]);
        $jSelasa2 = Jadwal::create([
            'kelas_id' => $kelas11TKJ->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'selasa', // Hari ini
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:45',
        ]);

        // RABU
        $jRabu1 = Jadwal::create([
            'kelas_id' => $kelas12TKJ->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'rabu',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:30',
        ]);
        $jRabu2 = Jadwal::create([
            'kelas_id' => $kelas11PPLG->id,
            'mapel_id' => $mapelKorespondensi->id,
            'guru_id' => $guru->id,
            'hari' => 'rabu',
            'jam_mulai' => '09:45',
            'jam_selesai' => '11:45',
        ]);

        // KAMIS
        $jKamis1 = Jadwal::create([
            'kelas_id' => $kelas10PPLG->id,
            'mapel_id' => $mapelLiterasi->id,
            'guru_id' => $guru2->id,
            'hari' => 'kamis',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
        ]);
        $jKamis2 = Jadwal::create([
            'kelas_id' => $kelas11DKV->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'kamis',
            'jam_mulai' => '09:45',
            'jam_selesai' => '11:45',
        ]);

        // JUMAT
        $jJumat1 = Jadwal::create([
            'kelas_id' => $kelas10MPLB->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'jumat',
            'jam_mulai' => '07:30',
            'jam_selesai' => '09:00',
        ]);
        $jJumat2 = Jadwal::create([
            'kelas_id' => $kelas12PPLG->id,
            'mapel_id' => $mapelBindo->id,
            'guru_id' => $guru->id,
            'hari' => 'jumat',
            'jam_mulai' => '09:15',
            'jam_selesai' => '10:45',
        ]);

        // 6. Pertemuan Sesi Mengajar — Kelas XII PPLG 1 (Jadwal Selasa: jSelasa1)
        $p1 = Pertemuan::create([
            'jadwal_id' => $jSelasa1->id,
            'tanggal' => '2026-08-18',
            'pertemuan_ke' => 1,
            'topik' => 'Kaidah Kebahasaan & Struktur Teks Laporan Hasil Observasi (LHO)',
            'catatan' => 'Menganalisis kalimat definisi, deskripsi bagian, dan istilah teknis pada observasi sistem laboratorium komputer.',
        ]);

        $p2 = Pertemuan::create([
            'jadwal_id' => $jSelasa1->id,
            'tanggal' => '2026-08-25',
            'pertemuan_ke' => 2,
            'topik' => 'Trik Negosiasi Persuasif & Santun dalam Presentasi Proyek IT',
            'catatan' => 'Simulasi bermain peran (roleplay) tawar-menawar spesifikasi dan anggaran sistem CBT antara vendor software dengan sekolah.',
        ]);

        $p3 = Pertemuan::create([
            'jadwal_id' => $jSelasa1->id,
            'tanggal' => '2026-09-01',
            'pertemuan_ke' => 3,
            'topik' => 'Sistematika Surat Lamaran Pekerjaan & CV Profesional Bidang IT',
            'catatan' => 'Menerapkan kaidah EYD V dalam menyusun surat resmi lamaran kerja dan portofolio kompetensi kejuruan PPLG.',
        ]);

        $p4 = Pertemuan::create([
            'jadwal_id' => $jSelasa1->id,
            'tanggal' => '2026-09-08', // HARI INI (Selasa, 8 September 2026)
            'pertemuan_ke' => 4,
            'topik' => 'Kajian Unsur Intrinsik & Nilai Karakter dalam Cerita Pendek (Cerpen)',
            'catatan' => 'Mengupas alur, penokohan, dan amanat cerpen bertema inovasi teknologi anak bangsa serta evaluasi formatif tengah bab.',
        ]);

        // Pertemuan Sesi Mengajar — Kelas XI TKJ 1 (Jadwal Selasa: jSelasa2) - HARI INI JUGA!
        $pTKJ1 = Pertemuan::create([
            'jadwal_id' => $jSelasa2->id,
            'tanggal' => '2026-08-25',
            'pertemuan_ke' => 1,
            'topik' => 'Penyusunan Teks Prosedur Standar Operasional Jaringan Komputer',
            'catatan' => 'Menuliskan langkah demi langkah instalasi server dan konfigurasi router dengan kalimat imperatif baku.',
        ]);
        $pTKJ2 = Pertemuan::create([
            'jadwal_id' => $jSelasa2->id,
            'tanggal' => '2026-09-01',
            'pertemuan_ke' => 2,
            'topik' => 'Format Laporan Insiden Jaringan & Dokumentasi Troubleshooting',
            'catatan' => 'Menyusun kronologi gangguan koneksi dan rekomendasi pemulihan sistem jaringan.',
        ]);
        $pTKJ3 = Pertemuan::create([
            'jadwal_id' => $jSelasa2->id,
            'tanggal' => '2026-09-08', // HARI INI
            'pertemuan_ke' => 3,
            'topik' => 'Etika Komunikasi Teknis Layanan Helpdesk & Komunikasi Pelanggan',
            'catatan' => 'Sesi KBM hari ini: simulasi merespons keluhan klien infrastruktur IT dengan kalimat santun dan solutif.',
        ]);

        // Pertemuan Kelas XI PPLG 1 (Jadwal Rabu: jRabu2)
        $pXI_1 = Pertemuan::create([
            'jadwal_id' => $jRabu2->id,
            'tanggal' => '2026-08-19',
            'pertemuan_ke' => 1,
            'topik' => 'Prinsip Dasar Korespondensi & Komunikasi Bisnis Industri',
            'catatan' => 'Mempelajari etika pengiriman email bisnis resmi dan perumusan memo teknis antardivisi.',
        ]);
        $pXI_2 = Pertemuan::create([
            'jadwal_id' => $jRabu2->id,
            'tanggal' => '2026-08-26',
            'pertemuan_ke' => 2,
            'topik' => 'Penyusunan Proposal Kerja Sama & Penawaran Software Solution',
            'catatan' => 'Praktik membuat dokumen proposal penawaran aplikasi CBT ke klien.',
        ]);
        $pXI_3 = Pertemuan::create([
            'jadwal_id' => $jRabu2->id,
            'tanggal' => '2026-09-02',
            'pertemuan_ke' => 3,
            'topik' => 'Pembuatan Portofolio & CV Profesional Pengembang Aplikasi',
            'catatan' => 'Format portofolio proyek perangkat lunak dan standarisasi bahasa formal profil LinkedIn.',
        ]);
        $pXI_4 = Pertemuan::create([
            'jadwal_id' => $jRabu2->id,
            'tanggal' => '2026-09-09',
            'pertemuan_ke' => 4,
            'topik' => 'Simulasi Negosiasi Kontrak & Service Level Agreement (SLA)',
            'catatan' => 'Sesi KBM besok mengenai tawar-menawar klausul dukungan teknis dan garansi software.',
        ]);

        // Pertemuan Kelas X PPLG 1 (Jadwal Kamis: jKamis1)
        $pX_1 = Pertemuan::create([
            'jadwal_id' => $jKamis1->id,
            'tanggal' => '2026-08-20',
            'pertemuan_ke' => 1,
            'topik' => 'Dasar Literasi Digital & Etika Komunikasi Media Online',
            'catatan' => 'Pengenalan Undang-Undang ITE, etika bermedia sosial, dan verifikasi fakta berita teknologi.',
        ]);
        $pX_2 = Pertemuan::create([
            'jadwal_id' => $jKamis1->id,
            'tanggal' => '2026-08-27',
            'pertemuan_ke' => 2,
            'topik' => 'Penulisan Artikel Opini Perkembangan Kecerdasan Buatan (AI)',
            'catatan' => 'Teknik menyusun paragraf argumentatif bertema dampak AI bagi dunia pendidikan.',
        ]);
        $pX_3 = Pertemuan::create([
            'jadwal_id' => $jKamis1->id,
            'tanggal' => '2026-09-03',
            'pertemuan_ke' => 3,
            'topik' => 'Apresiasi Nilai Moral Cerpen Bertema Teknologi & Inovasi',
            'catatan' => 'Membedah pesan moral dan integritas generasi muda dalam cerpen kontemporer.',
        ]);
        $pX_4 = Pertemuan::create([
            'jadwal_id' => $jKamis1->id,
            'tanggal' => '2026-09-10',
            'pertemuan_ke' => 4,
            'topik' => 'Analisis Kritis & Bedah Karya Tulis Digital',
            'catatan' => 'Sesi KBM lusa mengenai telaah karya esai teknologi siswa kelas X.',
        ]);

        // Pertemuan Kelas XI DKV 1 (Jadwal Kamis: jKamis2)
        $pDKV1 = Pertemuan::create([
            'jadwal_id' => $jKamis2->id,
            'tanggal' => '2026-08-27',
            'pertemuan_ke' => 1,
            'topik' => 'Prinsip Dasar Copywriting Kreatif & Storytelling Iklan Komersial',
            'catatan' => 'Membedah hook kalimat promosi dan psikologi kata dalam materi kampanye visual.',
        ]);
        $pDKV2 = Pertemuan::create([
            'jadwal_id' => $jKamis2->id,
            'tanggal' => '2026-09-03',
            'pertemuan_ke' => 2,
            'topik' => 'Penyusunan Naskah Video Edukasi & Storyboard Konten Multimedia',
            'catatan' => 'Menulis naskah dialog dan petunjuk audio visual dalam bahasa Indonesia yang menarik.',
        ]);

        // Pertemuan Kelas X MPLB 1 (Jadwal Jumat: jJumat1)
        $pMPLB1 = Pertemuan::create([
            'jadwal_id' => $jJumat1->id,
            'tanggal' => '2026-08-28',
            'pertemuan_ke' => 1,
            'topik' => 'Format Standar Tata Naskah Dinas & Surat Resmi Perkantoran',
            'catatan' => 'Format kop surat, penomoran naskah dinas, dan penulisan salam pembuka serta penutup.',
        ]);
        $pMPLB2 = Pertemuan::create([
            'jadwal_id' => $jJumat1->id,
            'tanggal' => '2026-09-04',
            'pertemuan_ke' => 2,
            'topik' => 'Komunikasi Telepon Bisnis & Pelayanan Prima Berbahasa Indonesia',
            'catatan' => 'Latihan kalimat sapaan formal, penanganan pesan telepon, dan etika menerima tamu kantor.',
        ]);

        // 7. Modul & Materi Ajar Bahasa Indonesia
        Materi::create([
            'pertemuan_id' => $p1->id,
            'judul' => 'Modul Ajar Teks LHO Berbasis Kejuruan',
            'konten' => 'Panduan lengkap penyusunan teks LHO mencakup pernyataan umum, deskripsi bagian, dan deskripsi manfaat dengan studi kasus observasi laboratorium komputer dan aplikasi sistem ujian CBT.',
            'tipe_file' => 'pdf',
        ]);

        Materi::create([
            'pertemuan_id' => $p1->id,
            'judul' => 'Pedoman Ejaan Bahasa Indonesia yang Disempurnakan (EYD Edisi V)',
            'konten' => 'Tata cara penggunaan huruf kapital, penulisan tanda baca koma dan titik dua, serta standarisasi kata serapan istilah kejuruan dalam bahasa Indonesia formal.',
            'tipe_file' => 'pdf',
        ]);

        Materi::create([
            'pertemuan_id' => $p2->id,
            'judul' => 'Slide Presentasi Strategi Negosiasi & Komunikasi Efektif',
            'konten' => 'Tahapan negosiasi terstruktur: orientasi, pengajuan, penawaran harga, kesepakatan tertulis, dan teknik menggunakan kalimat persuasif yang santun.',
            'tipe_file' => 'doc',
        ]);

        Materi::create([
            'pertemuan_id' => $p3->id,
            'judul' => 'Format Baku Surat Lamaran Pekerjaan dan Template CV ATS-Friendly',
            'konten' => 'Standar format penulisan surat dinas/pekerjaan, etika kalimat pembuka dan penutup, serta lampiran riwayat hidup bagi lulusan kejuruan SMK.',
            'tipe_file' => 'doc',
        ]);

        Materi::create([
            'pertemuan_id' => $p4->id,
            'judul' => 'Antologi Cerpen Inspiratif & Panduan Analisis Nilai Moral Teknologi',
            'konten' => 'Kumpulan cerpen sastra modern berlatar belakang dunia digital serta panduan praktis membedah tema, sudut pandang, konflik batin, dan pesan moral.',
            'tipe_file' => 'pdf',
        ]);

        Materi::create([
            'pertemuan_id' => $pTKJ3->id,
            'judul' => 'Panduan Komunikasi Helpdesk IT & Standar Tiket Layanan',
            'konten' => 'Contoh template respons tiket insiden pelanggan, penulisan eskalasi teknis, dan etika komunikasi verbal.',
            'tipe_file' => 'pdf',
        ]);

        // 8. Tugas Siswa Bahasa Indonesia
        Tugas::create([
            'pertemuan_id' => $p1->id,
            'judul' => 'Menyusun Teks Laporan Hasil Observasi Fasilitas Lab & Sistem KBM',
            'deskripsi' => 'Lakukan observasi terstruktur terhadap fasilitas lab komputer dan performa aplikasi sistem CBT. Buat laporan minimal 4 paragraf dengan struktur lengkap sesuai kaidah EYD V.',
            'deadline' => '2026-08-25 23:59:00',
            'nilai_maksimal' => 100,
        ]);

        Tugas::create([
            'pertemuan_id' => $p2->id,
            'judul' => 'Menulis Naskah Dialog Teks Negosiasi Proyek Pengadaan Sistem Sekolah',
            'deskripsi' => 'Tuliskan naskah dialog tawar-menawar antara vendor sistem dengan perwakilan sekolah mengenai fitur tambahan dan tenggat waktu pengerjaan. Gunakan kalimat persuasif dan santun.',
            'deadline' => '2026-09-01 23:59:00',
            'nilai_maksimal' => 100,
        ]);

        Tugas::create([
            'pertemuan_id' => $p3->id,
            'judul' => 'Pembuatan Surat Lamaran Kerja dan Resume Portofolio Profesional',
            'deskripsi' => 'Ketik surat lamaran pekerjaan formal ditujukan kepada HRD PT Teknologi Nusantara Solusindo. Cantumkan kualifikasi teknis dan portofolio proyek yang pernah dibuat.',
            'deadline' => '2026-09-10 23:59:00', // Kamis lusa
            'nilai_maksimal' => 100,
        ]);

        Tugas::create([
            'pertemuan_id' => $p4->id,
            'judul' => 'Resensi Analisis Karakter & Nilai Moral Cerpen Teknologi',
            'deskripsi' => 'Tuliskan telaah kritis mengenai konflik tokoh utama dalam cerpen pilihan. Ungkapkan bagaimana nilai kejujuran dan etika digital digambarkan.',
            'deadline' => '2026-09-15 23:59:00',
            'nilai_maksimal' => 100,
        ]);

        // 9. Presensi Kehadiran Siswa Bahasa Indonesia
        // XII PPLG 1 (P1 - P4 Lengkap)
        $pplgUsers = array_map(fn($id) => User::find($id), $pplgAllIds);
        $p1Statuses = [
            0 => ['status' => 'hadir', 'ket' => 'Tepat waktu'],
            1 => ['status' => 'hadir', 'ket' => 'Tepat waktu'],
            2 => ['status' => 'hadir', 'ket' => 'Tepat waktu'],
            3 => ['status' => 'izin', 'ket' => 'Izin mengikuti lomba debat bahasa Indonesia tingkat kota'],
            4 => ['status' => 'hadir', 'ket' => 'Tepat waktu'],
            5 => ['status' => 'dispen', 'ket' => 'Dispensasi persiapan olimpiade literasi nasional di dinas'],
            6 => ['status' => 'sakit', 'ket' => 'Sakit demam dan flu, surat dokter terlampir'],
            7 => ['status' => 'hadir', 'ket' => 'Tepat waktu'],
            8 => ['status' => 'hadir', 'ket' => 'Tepat waktu'],
            9 => ['status' => 'hadir', 'ket' => 'Tepat waktu'],
        ];

        foreach ($pplgUsers as $idx => $sw) {
            // P1
            $st1 = $p1Statuses[$idx] ?? ['status' => 'hadir', 'ket' => 'Tepat waktu'];
            Absensi::create([
                'pertemuan_id' => $p1->id,
                'siswa_id' => $sw->id,
                'status' => $st1['status'],
                'keterangan' => $st1['ket'],
            ]);

            // P2
            Absensi::create([
                'pertemuan_id' => $p2->id,
                'siswa_id' => $sw->id,
                'status' => $idx === 5 ? 'dispen' : ($idx === 3 ? 'hadir' : ($idx === 6 ? 'hadir' : 'hadir')),
                'keterangan' => $idx === 5 ? 'Dispensasi tim kejuaraan LKS Web Technology' : 'Hadir aktif berdiskusi',
            ]);

            // P3
            Absensi::create([
                'pertemuan_id' => $p3->id,
                'siswa_id' => $sw->id,
                'status' => $idx === 1 ? 'izin' : ($idx === 8 ? 'sakit' : 'hadir'),
                'keterangan' => $idx === 1 ? 'Izin keperluan keluarga mendesak' : ($idx === 8 ? 'Sakit radang tenggorokan' : 'Hadir tepat waktu'),
            ]);

            // P4 (HARI INI)
            Absensi::create([
                'pertemuan_id' => $p4->id,
                'siswa_id' => $sw->id,
                'status' => $idx === 5 ? 'dispen' : 'hadir',
                'keterangan' => $idx === 5 ? 'Dispensasi pendampingan pameran karya kejuruan' : 'Hadir tepat waktu sesi hari ini',
            ]);
        }

        // XI TKJ 1 (P_TKJ_1 - P_TKJ_3)
        $tkjUsers = array_map(fn($id) => User::find($id), array_slice($tkjIds, 0, 4));
        foreach ($tkjUsers as $idx => $sw) {
            Absensi::create([
                'pertemuan_id' => $pTKJ1->id,
                'siswa_id' => $sw->id,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu',
            ]);
            Absensi::create([
                'pertemuan_id' => $pTKJ2->id,
                'siswa_id' => $sw->id,
                'status' => $idx === 2 ? 'sakit' : 'hadir',
                'keterangan' => $idx === 2 ? 'Sakit surat dokter' : 'Hadir aktif berdiskusi',
            ]);
            Absensi::create([
                'pertemuan_id' => $pTKJ3->id,
                'siswa_id' => $sw->id,
                'status' => 'hadir',
                'keterangan' => 'Hadir tepat waktu sesi hari ini',
            ]);
        }

        // XI PPLG 1
        $siswaXI = array_slice($pplgUsers, 0, 5);
        foreach ($siswaXI as $idx => $sw) {
            Absensi::create(['pertemuan_id' => $pXI_1->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Hadir tepat waktu']);
            Absensi::create(['pertemuan_id' => $pXI_2->id, 'siswa_id' => $sw->id, 'status' => $idx === 1 ? 'izin' : 'hadir', 'keterangan' => $idx === 1 ? 'Izin kegiatan keluarga' : 'Hadir aktif berdiskusi']);
            Absensi::create(['pertemuan_id' => $pXI_3->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Hadir tepat waktu']);
            Absensi::create(['pertemuan_id' => $pXI_4->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Terdaftar sesi KBM']);
        }

        // X PPLG 1
        $siswaX = array_slice($pplgUsers, 5, 5);
        foreach ($siswaX as $idx => $sw) {
            Absensi::create(['pertemuan_id' => $pX_1->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Hadir tepat waktu']);
            Absensi::create(['pertemuan_id' => $pX_2->id, 'siswa_id' => $sw->id, 'status' => $idx === 2 ? 'sakit' : 'hadir', 'keterangan' => $idx === 2 ? 'Sakit surat dokter' : 'Hadir aktif']);
            Absensi::create(['pertemuan_id' => $pX_3->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Hadir tepat waktu']);
            Absensi::create(['pertemuan_id' => $pX_4->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Terdaftar sesi KBM']);
        }

        // XI DKV 1
        $dkvUsers = array_map(fn($id) => User::find($id), $dkvIds);
        foreach ($dkvUsers as $idx => $sw) {
            Absensi::create(['pertemuan_id' => $pDKV1->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Hadir tepat waktu']);
            Absensi::create(['pertemuan_id' => $pDKV2->id, 'siswa_id' => $sw->id, 'status' => $idx === 1 ? 'izin' : 'hadir', 'keterangan' => $idx === 1 ? 'Izin pameran karya visual' : 'Hadir aktif']);
        }

        // X MPLB 1
        $mplbUsers = array_map(fn($id) => User::find($id), $mplbIds);
        foreach ($mplbUsers as $idx => $sw) {
            Absensi::create(['pertemuan_id' => $pMPLB1->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Hadir tepat waktu']);
            Absensi::create(['pertemuan_id' => $pMPLB2->id, 'siswa_id' => $sw->id, 'status' => 'hadir', 'keterangan' => 'Hadir tepat waktu']);
        }

        // 10. Jurnal Mengajar Guru Bahasa Indonesia (Bu Yayu)
        JurnalMengajar::create([
            'pertemuan_id' => $p1->id,
            'uraian_kegiatan' => "1. Pembukaan dan doa bersama.\n2. Apersepsi tentang peranan penting kemahiran bahasa Indonesia dan penulisan teks teknis bagi lulusan kejuruan.\n3. Pemaparan struktur teks Laporan Hasil Observasi (LHO): pernyataan umum, deskripsi bagian, dan deskripsi manfaat.\n4. Siswa melakukan observasi langsung terhadap sarana prasarana sekolah dan sistem pembelajaran CBT.\n5. Refleksi dan penutupan dengan tugas observasi mandiri.",
            'hambatan' => "Beberapa siswa masih tertukar dalam merumuskan kalimat definisi (menggunakan 'adalah/merupakan') dengan kalimat deskripsi bagian; telah diberikan perbandingan studi kasus yang lebih konkret.",
        ]);

        JurnalMengajar::create([
            'pertemuan_id' => $p2->id,
            'uraian_kegiatan' => "1. Review materi teks negosiasi dan etika berkomunikasi santun di lingkungan kerja.\n2. Praktik simulasi roleplay berpasangan negosiasi pengadaan perangkat antara vendor dan sekolah.\n3. Evaluasi kosa kata persuasif dan bahasa tubuh selama negosiasi.\n4. Pembahasan kesepakatan klausul kontrak kerja sama.",
            'hambatan' => "Alokasi waktu simulasi roleplay sedikit terbatas karena tingginya antusiasme tanya jawab dan argumentasi siswa.",
        ]);

        JurnalMengajar::create([
            'pertemuan_id' => $p3->id,
            'uraian_kegiatan' => "1. Pembukaan dan motivasi persiapan karier lulusan SMK di dunia usaha dan dunia industri (DUDI).\n2. Pembahasan format resmi surat lamaran kerja sesuai kaidah EYD V dan penyusunan resume/CV ATS-friendly.\n3. Praktik mandiri menyusun surat lamaran posisi profesional di lab komputer.\n4. Reviu silang antar teman sebaya (peer review) untuk memeriksa ketepatan ejaan dan tanda baca.",
            'hambatan' => "Sebagian kecil siswa perlu penyesuaian dalam memilih kata pengantar yang formal tanpa terkesan kaku.",
        ]);

        JurnalMengajar::create([
            'pertemuan_id' => $p4->id,
            'uraian_kegiatan' => "1. Pembukaan sesi belajar hari ini (Selasa, 8 September 2026) dan pengecekan presensi kelas XII PPLG 1.\n2. Pengantar apresiasi sastra: menganalisis unsur intrinsik (alur, perwatakan, sudut pandang, amanat) dalam cerpen inovasi teknologi.\n3. Diskusi interaktif mengenai etika dan nilai kejujuran intelektual dalam rekayasa teknologi modern.\n4. Penjelasan penugasan resensi cerpen dan persiapan evaluasi berkala.",
            'hambatan' => "KBM berlangsung sangat tertib dan interaktif. Satu siswa dispensasi kegiatan pameran sekolah.",
        ]);

        JurnalMengajar::create([
            'pertemuan_id' => $pTKJ3->id,
            'uraian_kegiatan' => "1. Sesi KBM hari ini di kelas XI TKJ 1 (Selasa, 8 September 2026).\n2. Pengenalan etika komunikasi teknis helpdesk jaringan komputer.\n3. Praktik merespons komplain pelanggan dengan bahasa baku, santun, dan solutif.\n4. Pengecekan presensi dan tindak lanjut laporan insiden jaringan.",
            'hambatan' => "Seluruh siswa hadir lengkap dan aktif mempraktikkan skenario simulasi panggilan helpdesk.",
        ]);
    }
}
