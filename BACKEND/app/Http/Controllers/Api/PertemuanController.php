<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pertemuan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PertemuanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Pertemuan::with('bab');

        if ($request->has('bab_id')) {
            $query->where('bab_id', $request->query('bab_id'));
        }

        $pertemuan = $query->orderBy('pertemuan_ke')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pertemuan berhasil diambil',
            'data' => $pertemuan,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tanggal' => 'nullable|date',
            'pertemuan_ke' => 'nullable|integer',
            'bab_id' => 'nullable|integer|exists:bab,id',
            'topik' => 'nullable|string|max:200',
        ]);

        $pertemuan = Pertemuan::create($validated);
        $pertemuan->load('bab');

        return response()->json([
            'success' => true,
            'message' => 'Data pertemuan berhasil ditambahkan',
            'data' => $pertemuan,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $pertemuan = Pertemuan::with(['bab', 'presensi.siswa', 'materi', 'jurnalMengajar', 'keaktifan.siswa'])->find($id);

        if (!$pertemuan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pertemuan tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data pertemuan berhasil diambil',
            'data' => $pertemuan,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $pertemuan = Pertemuan::find($id);

        if (!$pertemuan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pertemuan tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'tanggal' => 'nullable|date',
            'pertemuan_ke' => 'nullable|integer',
            'bab_id' => 'nullable|integer|exists:bab,id',
            'topik' => 'nullable|string|max:200',
        ]);

        $pertemuan->update($validated);
        $pertemuan->load('bab');

        return response()->json([
            'success' => true,
            'message' => 'Data pertemuan berhasil diperbarui',
            'data' => $pertemuan,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $pertemuan = Pertemuan::find($id);

        if (!$pertemuan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pertemuan tidak ditemukan',
            ], 404);
        }

        $pertemuan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pertemuan berhasil dihapus',
        ]);
    }
}
