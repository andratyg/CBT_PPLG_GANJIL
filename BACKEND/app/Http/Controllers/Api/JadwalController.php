<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with(['kelas', 'mapel', 'guru:id,name']);

        if ($request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }
        if ($request->guru_id) {
            $query->where('guru_id', $request->guru_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:users,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        return response()->json(Jadwal::create($validated), 201);
    }

    public function show(Jadwal $jadwal)
    {
        return response()->json(
            $jadwal->load(['kelas', 'mapel', 'guru:id,name'])
        );
    }

    public function update(Request $request, Jadwal $jadwal)
    {
        $validated = $request->validate([
            'kelas_id' => 'sometimes|exists:kelas,id',
            'mapel_id' => 'sometimes|exists:mata_pelajaran,id',
            'guru_id' => 'sometimes|exists:users,id',
            'hari' => 'sometimes|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_mulai' => 'sometimes|date_format:H:i',
            'jam_selesai' => 'sometimes|date_format:H:i',
        ]);

        $jadwal->update($validated);

        return response()->json($jadwal);
    }

    public function destroy(Jadwal $jadwal)
    {
        $jadwal->delete();

        return response()->json(['message' => 'Jadwal dihapus']);
    }
}
