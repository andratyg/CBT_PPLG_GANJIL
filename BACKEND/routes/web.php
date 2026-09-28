<?php

use App\Http\Controllers\Admin\MasterDataController;
use App\Http\Controllers\Admin\PresensiController;
use App\Http\Controllers\Siswa\MateriController;
use App\Http\Controllers\Siswa\TugasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// ROUTE SISWA / GUEST (PORTAL BELAJAR & TUGAS MANDIRI)
// =========================================================================
Route::name('siswa.')->group(function () {
    Route::get('/', [MateriController::class, 'index'])->name('materi');
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/tugas', [TugasController::class, 'index'])->name('tugas');
    Route::post('/tugas/kumpulkan', [TugasController::class, 'kumpulkan'])->name('tugas.kumpul');
});

// =========================================================================
// KELOMPOK ROUTE YANG HANYA BISA DIAKSES OLEH USER SEBELUM LOGIN (isGuest)
// =========================================================================
Route::middleware(['isGuest'])->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [UserController::class, 'login'])->name('login.store');

    Route::get('/register', function () {
        return view('login');
    })->name('register');

    Route::post('/register', [UserController::class, 'register'])->name('register.store');
});

// =========================================================================
// KELOMPOK ROUTE YANG HANYA BISA DIAKSES SETELAH LOGIN GURU (guru.auth)
// =========================================================================
Route::middleware(['guru.auth'])->group(function () {
    // Logout bisa via GET maupun POST
    Route::match(['get', 'post'], '/logout', [UserController::class, 'logout'])->name('logout');

    // Prefix untuk mengelompokkan route admin guru yang path-nya diawali dengan /admin
    Route::prefix('admin')->name('admin.')->group(function () {
        // Route Dashboard Utama Admin Guru
        Route::get('/', [PresensiController::class, 'dashboard'])->name('dashboard');

        // Route Kelola Presensi Siswa (Berbasis Database & Multi-Rombel)
        Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi');
        Route::post('/presensi/simpan', [PresensiController::class, 'simpan'])->name('presensi.simpan');
        Route::post('/pertemuan/tambah', [PresensiController::class, 'tambahPertemuan'])->name('pertemuan.tambah');

        // CRUD Data Master (Siswa, Rombel, Guru, Bab)
        Route::get('/master', [MasterDataController::class, 'index'])->name('master');
        Route::get('/master/siswa/data', [MasterDataController::class, 'dataSiswa'])->name('master.siswa.data');
        Route::get('/master/bab/data', [MasterDataController::class, 'dataBab'])->name('master.bab.data');
        Route::post('/master/siswa', [MasterDataController::class, 'storeSiswa'])->name('master.siswa.store');
        Route::put('/master/siswa/{id}', [MasterDataController::class, 'updateSiswa'])->name('master.siswa.update');
        Route::delete('/master/siswa/{id}', [MasterDataController::class, 'destroySiswa'])->name('master.siswa.destroy');
        Route::post('/master/rombel', [MasterDataController::class, 'storeRombel'])->name('master.rombel.store');

        Route::post('/master/guru', [MasterDataController::class, 'storeGuru'])->name('master.guru.store');
        Route::put('/master/guru/{id}', [MasterDataController::class, 'updateGuru'])->name('master.guru.update');
        Route::delete('/master/guru/{id}', [MasterDataController::class, 'destroyGuru'])->name('master.guru.destroy');

        Route::post('/master/bab', [MasterDataController::class, 'storeBab'])->name('master.bab.store');
        Route::put('/master/bab/{id}', [MasterDataController::class, 'updateBab'])->name('master.bab.update');
        Route::delete('/master/bab/{id}', [MasterDataController::class, 'destroyBab'])->name('master.bab.destroy');
    });
});
