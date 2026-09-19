<?php

use App\Http\Controllers\Api\BabController;
use App\Http\Controllers\Api\GuruController;
use App\Http\Controllers\Api\JawabanKuisController;
use App\Http\Controllers\Api\JurnalMengajarController;
use App\Http\Controllers\Api\KeaktifanController;
use App\Http\Controllers\Api\KuisController;
use App\Http\Controllers\Api\MateriController;
use App\Http\Controllers\Api\PengumpulanTugasController;
use App\Http\Controllers\Api\PertemuanController;
use App\Http\Controllers\Api\PresensiController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\SoalKuisController;
use App\Http\Controllers\Api\TugasController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Resource API Routes CBT PPLG
Route::apiResource('guru', GuruController::class);
Route::apiResource('siswa', SiswaController::class);
Route::apiResource('bab', BabController::class);
Route::apiResource('pertemuan', PertemuanController::class);
Route::apiResource('presensi', PresensiController::class);
Route::apiResource('materi', MateriController::class);
Route::apiResource('tugas', TugasController::class);
Route::apiResource('pengumpulan-tugas', PengumpulanTugasController::class);
Route::apiResource('kuis', KuisController::class);
Route::apiResource('soal-kuis', SoalKuisController::class);
Route::apiResource('jawaban-kuis', JawabanKuisController::class);
Route::apiResource('jurnal-mengajar', JurnalMengajarController::class);
Route::apiResource('keaktifan', KeaktifanController::class);
