<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Siswa::query();

        if ($request->has('rombel')) {
            $query->where('rombel', $request->query('rombel'));
        }

        if ($request->has('rayon')) {
            $query->where('rayon', $request->query('rayon'));
        }

        if ($request->has('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $siswa = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar siswa berhasil diambil',
            'data' => $siswa,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20',
            'nama' => 'required|string|max:100',
            'rombel' => 'nullable|string|max:50',
            'rayon' => 'nullable|string|max:50',
        ]);

        $siswa = Siswa::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil ditambahkan',
            'data' => $siswa,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $siswa = Siswa::with(['presensi', 'pengumpulanTugas', 'keaktifan'])->find($id);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data siswa berhasil diambil',
            'data' => $siswa,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'nis' => 'sometimes|required|string|max:20',
            'nama' => 'sometimes|required|string|max:100',
            'rombel' => 'nullable|string|max:50',
            'rayon' => 'nullable|string|max:50',
        ]);

        $siswa->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $siswa,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $siswa = Siswa::find($id);

        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan',
            ], 404);
        }

        $siswa->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil dihapus',
        ]);
    }
}
