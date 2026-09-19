<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JawabanKuis;
use App\Models\SoalKuis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JawabanKuisController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = JawabanKuis::with(['kuis', 'siswa', 'soalKuis']);

        if ($request->has('kuis_id')) {
            $query->where('kuis_id', $request->query('kuis_id'));
        }

        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->query('siswa_id'));
        }

        $jawaban = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar jawaban kuis berhasil diambil',
            'data' => $jawaban,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kuis_id' => 'required|integer|exists:kuis,id',
            'siswa_id' => 'required|integer|exists:siswa,id',
            'soal_id' => 'required|integer|exists:soal_kuis,id',
            'jawaban' => 'nullable|string',
            'skor' => 'nullable|integer',
        ]);

        // Auto-scoring jika soal pilihan ganda dan ada kunci jawaban
        if (!isset($validated['skor'])) {
            $soal = SoalKuis::find($validated['soal_id']);
            if ($soal && $soal->kunci_jawaban) {
                $validated['skor'] = (strcasecmp(trim($validated['jawaban'] ?? ''), trim($soal->kunci_jawaban)) === 0) ? 100 : 0;
            }
        }

        $jawaban = JawabanKuis::create($validated);
        $jawaban->load(['kuis', 'siswa', 'soalKuis']);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban kuis berhasil disimpan',
            'data' => $jawaban,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $jawaban = JawabanKuis::with(['kuis', 'siswa', 'soalKuis'])->find($id);

        if (!$jawaban) {
            return response()->json([
                'success' => false,
                'message' => 'Data jawaban kuis tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data jawaban kuis berhasil diambil',
            'data' => $jawaban,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $jawaban = JawabanKuis::find($id);

        if (!$jawaban) {
            return response()->json([
                'success' => false,
                'message' => 'Data jawaban kuis tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'kuis_id' => 'sometimes|required|integer|exists:kuis,id',
            'siswa_id' => 'sometimes|required|integer|exists:siswa,id',
            'soal_id' => 'sometimes|required|integer|exists:soal_kuis,id',
            'jawaban' => 'nullable|string',
            'skor' => 'nullable|integer',
        ]);

        $jawaban->update($validated);
        $jawaban->load(['kuis', 'siswa', 'soalKuis']);

        return response()->json([
            'success' => true,
            'message' => 'Data jawaban kuis berhasil diperbarui',
            'data' => $jawaban,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $jawaban = JawabanKuis::find($id);

        if (!$jawaban) {
            return response()->json([
                'success' => false,
                'message' => 'Data jawaban kuis tidak ditemukan',
            ], 404);
        }

        $jawaban->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data jawaban kuis berhasil dihapus',
        ]);
    }
}
