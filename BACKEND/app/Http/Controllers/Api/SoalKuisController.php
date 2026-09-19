<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SoalKuis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SoalKuisController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SoalKuis::with('kuis');

        if ($request->has('kuis_id')) {
            $query->where('kuis_id', $request->query('kuis_id'));
        }

        $soal = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar soal kuis berhasil diambil',
            'data' => $soal,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kuis_id' => 'required|integer|exists:kuis,id',
            'jenis_soal' => 'nullable|string|max:20',
            'pertanyaan' => 'required|string',
            'pilihan_a' => 'nullable|string|max:255',
            'pilihan_b' => 'nullable|string|max:255',
            'pilihan_c' => 'nullable|string|max:255',
            'pilihan_d' => 'nullable|string|max:255',
            'kunci_jawaban' => 'nullable|string|max:255',
        ]);

        $soal = SoalKuis::create($validated);
        $soal->load('kuis');

        return response()->json([
            'success' => true,
            'message' => 'Soal kuis berhasil ditambahkan',
            'data' => $soal,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $soal = SoalKuis::with('kuis')->find($id);

        if (!$soal) {
            return response()->json([
                'success' => false,
                'message' => 'Data soal kuis tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data soal kuis berhasil diambil',
            'data' => $soal,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $soal = SoalKuis::find($id);

        if (!$soal) {
            return response()->json([
                'success' => false,
                'message' => 'Data soal kuis tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'kuis_id' => 'sometimes|required|integer|exists:kuis,id',
            'jenis_soal' => 'nullable|string|max:20',
            'pertanyaan' => 'sometimes|required|string',
            'pilihan_a' => 'nullable|string|max:255',
            'pilihan_b' => 'nullable|string|max:255',
            'pilihan_c' => 'nullable|string|max:255',
            'pilihan_d' => 'nullable|string|max:255',
            'kunci_jawaban' => 'nullable|string|max:255',
        ]);

        $soal->update($validated);
        $soal->load('kuis');

        return response()->json([
            'success' => true,
            'message' => 'Data soal kuis berhasil diperbarui',
            'data' => $soal,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $soal = SoalKuis::find($id);

        if (!$soal) {
            return response()->json([
                'success' => false,
                'message' => 'Data soal kuis tidak ditemukan',
            ], 404);
        }

        $soal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data soal kuis berhasil dihapus',
        ]);
    }
}
