<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $query = MataPelajaran::with('guru:id,name');

        if ($request->guru_id) {
            $query->where('guru_id', $request->guru_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'guru_id' => 'required|exists:users,id',
        ]);

        return response()->json(MataPelajaran::create($validated), 201);
    }

    public function show(MataPelajaran $mataPelajaran)
    {
        return response()->json(
            $mataPelajaran->load('guru:id,name')
        );
    }

    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'deskripsi' => 'nullable|string',
            'guru_id' => 'sometimes|exists:users,id',
        ]);

        $mataPelajaran->update($validated);

        return response()->json($mataPelajaran);
    }

    public function destroy(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->delete();

        return response()->json(['message' => 'Mata pelajaran dihapus']);
    }

    // Mapel yg diajar guru yg sedang login
    public function saya(Request $request)
    {
        return response()->json(
            MataPelajaran::where('guru_id', $request->user()->id)->with('guru:id,name')->get()
        );
    }
}
