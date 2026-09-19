<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JurnalMengajar;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JurnalMengajarController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = JurnalMengajar::with(['guru', 'pertemuan']);

        if ($request->has('guru_id')) {
            $query->where('guru_id', $request->query('guru_id'));
        }

        if ($request->has('pertemuan_id')) {
            $query->where('pertemuan_id', $request->query('pertemuan_id'));
        }

        $jurnal = $query->orderByDesc('tanggal')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar jurnal mengajar berhasil diambil',
            'data' => $jurnal,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guru_id' => 'nullable|integer|exists:guru,id',
            'pertemuan_id' => 'nullable|integer|exists:pertemuan,id',
            'tanggal' => 'nullable|date',
            'pertemuan_ke' => 'nullable|integer',
            'topik' => 'nullable|string|max:200',
            'uraian_kegiatan' => 'nullable|string',
            'hambatan' => 'nullable|string',
        ]);

        if (empty($validated['tanggal'])) {
            $validated['tanggal'] = now()->toDateString();
        }

        $jurnal = JurnalMengajar::create($validated);
        $jurnal->load(['guru', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Jurnal mengajar berhasil ditambahkan',
            'data' => $jurnal,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $jurnal = JurnalMengajar::with(['guru', 'pertemuan'])->find($id);

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Data jurnal mengajar tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail jurnal mengajar berhasil diambil',
            'data' => $jurnal,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $jurnal = JurnalMengajar::find($id);

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Data jurnal tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'guru_id' => 'nullable|integer|exists:guru,id',
            'pertemuan_id' => 'nullable|integer|exists:pertemuan,id',
            'tanggal' => 'nullable|date',
            'pertemuan_ke' => 'nullable|integer',
            'topik' => 'nullable|string|max:200',
            'uraian_kegiatan' => 'nullable|string',
            'hambatan' => 'nullable|string',
        ]);

        $jurnal->update($validated);
        $jurnal->load(['guru', 'pertemuan']);

        return response()->json([
            'success' => true,
            'message' => 'Data jurnal mengajar berhasil diperbarui',
            'data' => $jurnal,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $jurnal = JurnalMengajar::find($id);

        if (!$jurnal) {
            return response()->json([
                'success' => false,
                'message' => 'Data jurnal tidak ditemukan',
            ], 404);
        }

        $jurnal->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data jurnal mengajar berhasil dihapus',
        ]);
    }
}
