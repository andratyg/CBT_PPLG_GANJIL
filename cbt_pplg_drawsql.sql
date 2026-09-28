-- ==========================================================
-- DRAW SQL SCHEMA - CBT PPLG GANJIL (UPDATED)
-- Database Dialect: MySQL / MariaDB
-- Update Terakhir: Menambahkan tabel `rombel` (berelasi ke `siswa`),
-- tabel `users` (autentikasi Laravel), serta seluruh relasi lengkap.
-- ==========================================================

-- 1. TABEL PENGGUNA SISTEM (AUTH LARAVEL)
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
);

-- 2. TABEL DATA MASTER GURU
CREATE TABLE `guru` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(100) NULL,
    `email` VARCHAR(100) NULL,
    `password` VARCHAR(255) NULL,
    PRIMARY KEY (`id`)
);

-- 3. TABEL ROMBEL (BARU DI-MIGRASI)
CREATE TABLE `rombel` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama` VARCHAR(50) NOT NULL UNIQUE,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`),
    INDEX (`nama`)
);

-- 4. TABEL DATA MASTER SISWA
CREATE TABLE `siswa` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nis` VARCHAR(20) NULL,
    `nama` VARCHAR(100) NULL,
    `rombel` VARCHAR(50) NULL,
    `rayon` VARCHAR(50) NULL,
    PRIMARY KEY (`id`),
    INDEX (`rombel`),
    FOREIGN KEY (`rombel`) REFERENCES `rombel` (`nama`) ON UPDATE CASCADE ON DELETE SET NULL
);

-- 5. TABEL BAB PEMBELAJARAN
CREATE TABLE `bab` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `nama_bab` VARCHAR(150) NULL,
    PRIMARY KEY (`id`)
);

-- 6. TABEL TUGAS
CREATE TABLE `tugas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `judul` VARCHAR(200) NULL,
    `instruksi` TEXT NULL,
    `deadline` DATETIME NULL,
    PRIMARY KEY (`id`)
);

-- 7. TABEL KUIS & UJIAN
CREATE TABLE `kuis` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `judul` VARCHAR(200) NULL,
    `token` VARCHAR(20) NULL,
    `durasi_menit` INT NULL,
    `waktu_mulai` DATETIME NULL,
    PRIMARY KEY (`id`)
);

-- 8. TABEL PERTEMUAN KELAS
CREATE TABLE `pertemuan` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `tanggal` DATE NULL,
    `pertemuan_ke` INT NULL,
    `bab_id` INT NULL,
    `topik` VARCHAR(200) NULL,
    PRIMARY KEY (`id`),
    INDEX (`bab_id`),
    FOREIGN KEY (`bab_id`) REFERENCES `bab` (`id`) ON DELETE SET NULL
);

-- 9. TABEL PRESENSI SISWA
CREATE TABLE `presensi` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `siswa_id` INT NULL,
    `pertemuan_id` INT NULL,
    `status` VARCHAR(20) NULL,
    PRIMARY KEY (`id`),
    INDEX (`siswa_id`),
    INDEX (`pertemuan_id`),
    FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`pertemuan_id`) REFERENCES `pertemuan` (`id`) ON DELETE CASCADE
);

-- 10. TABEL MATERI PEMBELAJARAN
CREATE TABLE `materi` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `bab_id` INT NULL,
    `pertemuan_id` INT NULL,
    `judul` VARCHAR(200) NULL,
    `jenis_file` VARCHAR(20) NULL,
    `path_file` VARCHAR(255) NULL,
    `url_eksternal` VARCHAR(255) NULL,
    PRIMARY KEY (`id`),
    INDEX (`bab_id`),
    INDEX (`pertemuan_id`),
    FOREIGN KEY (`bab_id`) REFERENCES `bab` (`id`) ON DELETE SET NULL,
    FOREIGN KEY (`pertemuan_id`) REFERENCES `pertemuan` (`id`) ON DELETE SET NULL
);

-- 11. TABEL PENGUMPULAN TUGAS SISWA
CREATE TABLE `pengumpulan_tugas` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `tugas_id` INT NULL,
    `siswa_id` INT NULL,
    `path_file` VARCHAR(255) NULL,
    `skor` INT NULL,
    `catatan` TEXT NULL,
    `status` VARCHAR(20) NULL,
    `waktu_kumpul` DATETIME NULL,
    PRIMARY KEY (`id`),
    INDEX (`tugas_id`),
    INDEX (`siswa_id`),
    FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
);

-- 12. TABEL SOAL KUIS
CREATE TABLE `soal_kuis` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `kuis_id` INT NULL,
    `jenis_soal` VARCHAR(20) NULL,
    `pertanyaan` TEXT NULL,
    `pilihan_a` VARCHAR(255) NULL,
    `pilihan_b` VARCHAR(255) NULL,
    `pilihan_c` VARCHAR(255) NULL,
    `pilihan_d` VARCHAR(255) NULL,
    `kunci_jawaban` VARCHAR(255) NULL,
    PRIMARY KEY (`id`),
    INDEX (`kuis_id`),
    FOREIGN KEY (`kuis_id`) REFERENCES `kuis` (`id`) ON DELETE CASCADE
);

-- 13. TABEL JAWABAN KUIS SISWA
CREATE TABLE `jawaban_kuis` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `kuis_id` INT NULL,
    `siswa_id` INT NULL,
    `soal_id` INT NULL,
    `jawaban` TEXT NULL,
    `skor` INT NULL,
    PRIMARY KEY (`id`),
    INDEX (`kuis_id`),
    INDEX (`siswa_id`),
    INDEX (`soal_id`),
    FOREIGN KEY (`kuis_id`) REFERENCES `kuis` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`soal_id`) REFERENCES `soal_kuis` (`id`) ON DELETE CASCADE
);

-- 14. TABEL JURNAL MENGAJAR GURU
CREATE TABLE `jurnal_mengajar` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `guru_id` INT NULL,
    `pertemuan_id` INT NULL,
    `tanggal` DATE NULL,
    `pertemuan_ke` INT NULL,
    `topik` VARCHAR(200) NULL,
    `uraian_kegiatan` TEXT NULL,
    `hambatan` TEXT NULL,
    PRIMARY KEY (`id`),
    INDEX (`guru_id`),
    INDEX (`pertemuan_id`),
    FOREIGN KEY (`guru_id`) REFERENCES `guru` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`pertemuan_id`) REFERENCES `pertemuan` (`id`) ON DELETE SET NULL
);

-- 15. TABEL POIN KEAKTIFAN SISWA
CREATE TABLE `keaktifan` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `siswa_id` INT NULL,
    `pertemuan_id` INT NULL,
    `poin` INT NULL,
    `keterangan` VARCHAR(255) NULL,
    `tanggal` DATE NULL,
    PRIMARY KEY (`id`),
    INDEX (`siswa_id`),
    INDEX (`pertemuan_id`),
    FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
    FOREIGN KEY (`pertemuan_id`) REFERENCES `pertemuan` (`id`) ON DELETE SET NULL
);
