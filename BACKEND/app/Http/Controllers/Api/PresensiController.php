<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Presensi::with(['siswa', 'pertemuan']);

        if ($request->has('pertemuan_id')) {
            $query->where('pertemuan_id', $request->query('pertemuan_id'));
        }

        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->query('siswa_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $presensi = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar presensi berhasil diambil',
            'data' => $presensi,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'siswa_id' => 'required|integer|exists:siswa,id',
            'pertemuan_id' => 'required|integer|exists:pertemuan,id',
            'status' => 'required|string|max:20',
        ]);

        $presensi = Presensi::create($validated);
        $presensi->load(['siswa', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Data presensi berhasil dicatat',
            'data' => $presensi,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $presensi = Presensi::with(['siswa', 'pertemuan'])->find($id);

        if (!$presensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data presensi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data presensi berhasil diambil',
            'data' => $presensi,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $presensi = Presensi::find($id);

        if (!$presensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data presensi tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'siswa_id' => 'sometimes|required|integer|exists:siswa,id',
            'pertemuan_id' => 'sometimes|required|integer|exists:pertemuan,id',
            'status' => 'sometimes|required|string|max:20',
        ]);

        $presensi->update($validated);
        $presensi->load(['siswa', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Data presensi berhasil diperbarui',
            'data' => $presensi,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $presensi = Presensi::find($id);

        if (!$presensi) {
            return response()->json([
                'success' => false,
                'message' => 'Data presensi tidak ditemukan',
            ], 404);
        }

        $presensi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data presensi berhasil dihapus',
        ]);
    }
}
