<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keaktifan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KeaktifanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Keaktifan::with(['siswa', 'pertemuan']);

        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->query('siswa_id'));
        }

        if ($request->has('pertemuan_id')) {
            $query->where('pertemuan_id', $request->query('pertemuan_id'));
        }

        $keaktifan = $query->orderByDesc('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar catatan keaktifan berhasil diambil',
            'data' => $keaktifan,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'pertemuan_id' => 'nullable|integer|exists:pertemuan,id',
            'poin' => 'required|integer',
            'keterangan' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
        ]);

        if (empty($validated['tanggal'])) {
            $validated['tanggal'] = now()->toDateString();
        }

        $keaktifan = Keaktifan::create($validated);
        $keaktifan->load(['siswa', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Data keaktifan berhasil ditambahkan',
            'data' => $keaktifan,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $keaktifan = Keaktifan::with(['siswa', 'pertemuan'])->find($id);

        if (!$keaktifan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keaktifan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data keaktifan berhasil diambil',
            'data' => $keaktifan,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $keaktifan = Keaktifan::find($id);

        if (!$keaktifan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keaktifan tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'siswa_id' => 'sometimes|required|integer|exists:siswa,id',
            'pertemuan_id' => 'nullable|integer|exists:pertemuan,id',
            'poin' => 'sometimes|required|integer',
            'keterangan' => 'nullable|string|max:255',
            'tanggal' => 'nullable|date',
        ]);

        $keaktifan->update($validated);
        $keaktifan->load(['siswa', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Data keaktifan berhasil diperbarui',
            'data' => $keaktifan,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $keaktifan = Keaktifan::find($id);

        if (!$keaktifan) {
            return response()->json([
                'success' => false,
                'message' => 'Data keaktifan tidak ditemukan',
            ], 404);
        }

        $keaktifan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data keaktifan berhasil dihapus',
        ]);
    }
}
