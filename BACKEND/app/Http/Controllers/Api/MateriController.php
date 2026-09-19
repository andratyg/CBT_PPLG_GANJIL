<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Materi::with(['bab', 'pertemuan']);

        if ($request->has('bab_id')) {
            $query->where('bab_id', $request->query('bab_id'));
        }

        if ($request->has('pertemuan_id')) {
            $query->where('pertemuan_id', $request->query('pertemuan_id'));
        }

        $materi = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar materi berhasil diambil',
            'data' => $materi,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bab_id' => 'nullable|integer|exists:bab,id',
            'pertemuan_id' => 'nullable|integer|exists:pertemuan,id',
            'judul' => 'required|string|max:200',
            'jenis_file' => 'nullable|string|max:20',
            'file' => 'nullable|file|max:20480', // max 20MB
            'path_file' => 'nullable|string|max:255',
            'url_eksternal' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materi', 'public');
            $validated['path_file'] = $path;
            if (empty($validated['jenis_file'])) {
                $validated['jenis_file'] = $request->file('file')->getClientOriginalExtension();
            }
        }

        unset($validated['file']);

        $materi = Materi::create($validated);
        $materi->load(['bab', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Data materi berhasil ditambahkan',
            'data' => $materi,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $materi = Materi::with(['bab', 'pertemuan'])->find($id);

        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Data materi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail data materi berhasil diambil',
            'data' => $materi,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $materi = Materi::find($id);

        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Data materi tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'bab_id' => 'nullable|integer|exists:bab,id',
            'pertemuan_id' => 'nullable|integer|exists:pertemuan,id',
            'judul' => 'sometimes|required|string|max:200',
            'jenis_file' => 'nullable|string|max:20',
            'file' => 'nullable|file|max:20480',
            'path_file' => 'nullable|string|max:255',
            'url_eksternal' => 'nullable|url|max:255',
        ]);

        if ($request->hasFile('file')) {
            if ($materi->path_file && Storage::disk('public')->exists($materi->path_file)) {
                Storage::disk('public')->delete($materi->path_file);
            }
            $path = $request->file('file')->store('materi', 'public');
            $validated['path_file'] = $path;
            if (empty($validated['jenis_file'])) {
                $validated['jenis_file'] = $request->file('file')->getClientOriginalExtension();
            }
        }

        unset($validated['file']);

        $materi->update($validated);
        $materi->load(['bab', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Data materi berhasil diperbarui',
            'data' => $materi,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $materi = Materi::find($id);

        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Data materi tidak ditemukan',
            ], 404);
        }

        if ($materi->path_file && Storage::disk('public')->exists($materi->path_file)) {
            Storage::disk('public')->delete($materi->path_file);
        }

        $materi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data materi berhasil dihapus',
        ]);
    }
}
