<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kuis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KuisController extends Controller
{
    public function index(): JsonResponse
    {
        $kuis = Kuis::withCount('soalKuis')->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar kuis berhasil diambil',
            'data' => $kuis,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'token' => 'nullable|string|max:20',
            'durasi_menit' => 'nullable|integer|min:1',
            'waktu_mulai' => 'nullable|date',
        ]);

        if (empty($validated['token'])) {
            $validated['token'] = strtoupper(Str::random(6));
        }

        $kuis = Kuis::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kuis berhasil dibuat',
            'data' => $kuis,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $kuis = Kuis::with(['soalKuis', 'jawabanKuis.siswa'])->find($id);

        if (!$kuis) {
            return response()->json([
                'success' => false,
                'message' => 'Data kuis tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data kuis berhasil diambil',
            'data' => $kuis,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $kuis = Kuis::find($id);

        if (!$kuis) {
            return response()->json([
                'success' => false,
                'message' => 'Data kuis tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:200',
            'token' => 'nullable|string|max:20',
            'durasi_menit' => 'nullable|integer|min:1',
            'waktu_mulai' => 'nullable|date',
        ]);

        $kuis->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data kuis berhasil diperbarui',
            'data' => $kuis,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $kuis = Kuis::find($id);

        if (!$kuis) {
            return response()->json([
                'success' => false,
                'message' => 'Data kuis tidak ditemukan',
            ], 404);
        }

        $kuis->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data kuis berhasil dihapus',
        ]);
    }
}
