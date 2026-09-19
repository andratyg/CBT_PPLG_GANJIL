<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BabController extends Controller
{
    public function index(): JsonResponse
    {
        $bab = Bab::with(['pertemuan', 'materi'])->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar bab berhasil diambil',
            'data' => $bab,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_bab' => 'required|string|max:150',
        ]);

        $bab = Bab::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data bab berhasil ditambahkan',
            'data' => $bab,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $bab = Bab::with(['pertemuan', 'materi'])->find($id);

        if (!$bab) {
            return response()->json([
                'success' => false,
                'message' => 'Data bab tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data bab berhasil diambil',
            'data' => $bab,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $bab = Bab::find($id);

        if (!$bab) {
            return response()->json([
                'success' => false,
                'message' => 'Data bab tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'nama_bab' => 'sometimes|required|string|max:150',
        ]);

        $bab->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data bab berhasil diperbarui',
            'data' => $bab,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $bab = Bab::find($id);

        if (!$bab) {
            return response()->json([
                'success' => false,
                'message' => 'Data bab tidak ditemukan',
            ], 404);
        }

        $bab->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data bab berhasil dihapus',
        ]);
    }
}
