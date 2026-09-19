<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TugasController extends Controller
{
    public function index(): JsonResponse
    {
        $tugas = Tugas::withCount('pengumpulanTugas')->orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar tugas berhasil diambil',
            'data' => $tugas,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'instruksi' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $tugas = Tugas::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data tugas berhasil ditambahkan',
            'data' => $tugas,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $tugas = Tugas::with(['pengumpulanTugas.siswa'])->find($id);

        if (!$tugas) {
            return response()->json([
                'success' => false,
                'message' => 'Data tugas tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data tugas berhasil diambil',
            'data' => $tugas,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $tugas = Tugas::find($id);

        if (!$tugas) {
            return response()->json([
                'success' => false,
                'message' => 'Data tugas tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'judul' => 'sometimes|required|string|max:200',
            'instruksi' => 'nullable|string',
            'deadline' => 'nullable|date',
        ]);

        $tugas->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data tugas berhasil diperbarui',
            'data' => $tugas,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $tugas = Tugas::find($id);

        if (!$tugas) {
            return response()->json([
                'success' => false,
                'message' => 'Data tugas tidak ditemukan',
            ], 404);
        }

        $tugas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data tugas berhasil dihapus',
        ]);
    }
}
