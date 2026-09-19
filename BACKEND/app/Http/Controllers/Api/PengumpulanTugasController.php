<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengumpulanTugasController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = PengumpulanTugas::with(['tugas', 'siswa']);

        if ($request->has('tugas_id')) {
            $query->where('tugas_id', $request->query('tugas_id'));
        }

        if ($request->has('siswa_id')) {
            $query->where('siswa_id', $request->query('siswa_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $pengumpulan = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengumpulan tugas berhasil diambil',
            'data' => $pengumpulan,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tugas_id' => 'required|integer|exists:tugas,id',
            'siswa_id' => 'required|integer|exists:siswa,id',
            'file' => 'nullable|file|max:20480',
            'path_file' => 'nullable|string|max:255',
            'skor' => 'nullable|integer',
            'catatan' => 'nullable|string',
            'status' => 'nullable|string|max:20',
            'waktu_kumpul' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('pengumpulan_tugas', 'public');
            $validated['path_file'] = $path;
        }
        unset($validated['file']);

        if (empty($validated['waktu_kumpul'])) {
            $validated['waktu_kumpul'] = now();
        }

        if (empty($validated['status'])) {
            $validated['status'] = 'dikumpulkan';
        }

        $pengumpulan = PengumpulanTugas::create($validated);
        $pengumpulan->load(['tugas', 'siswa']);

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dikumpulkan',
            'data' => $pengumpulan,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $pengumpulan = PengumpulanTugas::with(['tugas', 'siswa'])->find($id);

        if (!$pengumpulan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengumpulan tugas tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail pengumpulan tugas berhasil diambil',
            'data' => $pengumpulan,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $pengumpulan = PengumpulanTugas::find($id);

        if (!$pengumpulan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengumpulan tugas tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'tugas_id' => 'nullable|integer|exists:tugas,id',
            'siswa_id' => 'nullable|integer|exists:siswa,id',
            'file' => 'nullable|file|max:20480',
            'path_file' => 'nullable|string|max:255',
            'skor' => 'nullable|integer|min:0|max:100',
            'catatan' => 'nullable|string',
            'status' => 'nullable|string|max:20',
            'waktu_kumpul' => 'nullable|date',
        ]);

        if ($request->hasFile('file')) {
            if ($pengumpulan->path_file && Storage::disk('public')->exists($pengumpulan->path_file)) {
                Storage::disk('public')->delete($pengumpulan->path_file);
            }
            $path = $request->file('file')->store('pengumpulan_tugas', 'public');
            $validated['path_file'] = $path;
        }
        unset($validated['file']);

        $pengumpulan->update($validated);
        $pengumpulan->load(['tugas', 'siswa']);

        return response()->json([
            'success' => true,
            'message' => 'Data pengumpulan tugas berhasil diperbarui',
            'data' => $pengumpulan,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $pengumpulan = PengumpulanTugas::find($id);

        if (!$pengumpulan) {
            return response()->json([
                'success' => false,
                'message' => 'Data pengumpulan tugas tidak ditemukan',
            ], 404);
        }

        if ($pengumpulan->path_file && Storage::disk('public')->exists($pengumpulan->path_file)) {
            Storage::disk('public')->delete($pengumpulan->path_file);
        }

        $pengumpulan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengumpulan tugas berhasil dihapus',
        ]);
    }
}
