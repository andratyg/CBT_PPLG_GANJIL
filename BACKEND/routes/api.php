<?php

use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\JadwalController;
use App\Http\Controllers\Api\JurnalController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\MapelController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\PertemuanController;
use App\Http\Controllers\Api\TugasController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. PUBLIC ROUTES (Siswa / Publik tanpa login)
// ==========================================

Route::get('/public-dashboard', [DashboardController::class, 'publicDashboard']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Akses Baca untuk Siswa & Publik (Tanpa Login)
Route::get('/kelas', [KelasController::class, 'index']);
Route::get('/kelas/{kelas}', [KelasController::class, 'show']);

Route::get('/mapel', [MapelController::class, 'index']);
Route::get('/mapel/{mataPelajaran}', [MapelController::class, 'show']);

Route::get('/jadwal', [JadwalController::class, 'index']);
Route::get('/jadwal/{jadwal}', [JadwalController::class, 'show']);

Route::get('/pertemuan', [PertemuanController::class, 'index']);
Route::get('/pertemuan/{pertemuan}', [PertemuanController::class, 'show']);
Route::get('/pertemuan/{pertemuan}/siswa', [PertemuanController::class, 'siswaKelas']);

Route::get('/pertemuan/{pertemuan}/materi', [MateriController::class, 'index']);
Route::get('/materi/{materi}', [MateriController::class, 'show']);

Route::get('/pertemuan/{pertemuan}/tugas', [TugasController::class, 'index']);
Route::get('/tugas/{tugas}', [TugasController::class, 'show']);
Route::post('/tugas/{tugas}/kumpulkan', [TugasController::class, 'kumpulkan']); // Pengumpulan tugas tanpa login

Route::get('/pertemuan/{pertemuan}/absensi', [AbsensiController::class, 'index']);
Route::get('/absensi/rekap', [AbsensiController::class, 'rekap']);
Route::get('/absensi/matrix', [AbsensiController::class, 'matrix']);

Route::get('/pertemuan/{pertemuan}/jurnal', [JurnalController::class, 'show']);

// Data Siswa & Pengguna (Akses Cepat)
Route::get('/users', [UserController::class, 'index']);
Route::get('/siswa', [UserController::class, 'index']);

// ==========================================
// 2. PROTECTED ROUTES (Sanctum / Guru)
// ==========================================

Route::middleware('auth:sanctum')->group(function () {
    // Auth info & logout
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard overview
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/mapel/saya', [MapelController::class, 'saya']);
    Route::get('/pertemuan/saya', [PertemuanController::class, 'indexBySiswa']);

    // Modul Guru (Role: guru)
    Route::middleware('role:guru')->group(function () {
        // Kelas & Siswa
        Route::post('/kelas', [KelasController::class, 'store']);
        Route::put('/kelas/{kelas}', [KelasController::class, 'update']);
        Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy']);
        Route::post('/kelas/{kelas}/siswa', [KelasController::class, 'tambahSiswa']);
        Route::post('/kelas/{kelas}/tambah-semua-siswa', [KelasController::class, 'tambahSemuaSiswa']);
        Route::post('/kelas/{kelas}/daftar-siswa-baru', [KelasController::class, 'daftarSiswaBaru']);
        Route::delete('/kelas/{kelas}/siswa/{siswa}', [KelasController::class, 'hapusSiswa']);

        // User management
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);

        // Mata pelajaran
        Route::post('/mapel', [MapelController::class, 'store']);
        Route::put('/mapel/{mataPelajaran}', [MapelController::class, 'update']);
        Route::delete('/mapel/{mataPelajaran}', [MapelController::class, 'destroy']);

        // Jadwal
        Route::post('/jadwal', [JadwalController::class, 'store']);
        Route::put('/jadwal/{jadwal}', [JadwalController::class, 'update']);
        Route::delete('/jadwal/{jadwal}', [JadwalController::class, 'destroy']);

        // Pertemuan
        Route::post('/pertemuan', [PertemuanController::class, 'store']);
        Route::put('/pertemuan/{pertemuan}', [PertemuanController::class, 'update']);
        Route::delete('/pertemuan/{pertemuan}', [PertemuanController::class, 'destroy']);

        // Materi
        Route::post('/pertemuan/{pertemuan}/materi', [MateriController::class, 'store']);
        Route::put('/materi/{materi}', [MateriController::class, 'update']);
        Route::delete('/materi/{materi}', [MateriController::class, 'destroy']);

        // Tugas & Penilaian
        Route::post('/pertemuan/{pertemuan}/tugas', [TugasController::class, 'store']);
        Route::put('/tugas/{tugas}', [TugasController::class, 'update']);
        Route::delete('/tugas/{tugas}', [TugasController::class, 'destroy']);
        Route::put('/pengumpulan/{pengumpulan}/nilai', [TugasController::class, 'nilai']);

        // Absensi
        Route::post('/pertemuan/{pertemuan}/absensi', [AbsensiController::class, 'store']);
        Route::post('/absensi/batch', [AbsensiController::class, 'storeBatch']);

        // Jurnal mengajar
        Route::put('/pertemuan/{pertemuan}/jurnal', [JurnalController::class, 'update']);

        // Rekap nilai
        Route::get('/nilai/rekap', [TugasController::class, 'rekapNilai']);
    });
});