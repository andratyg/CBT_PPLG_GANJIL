<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Pertemuan;
use App\Models\User;
use Illuminate\Http\Request;

class PertemuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pertemuan::with(['jadwal.kelas', 'jadwal.mapel', 'jadwal.guru'])
            ->orderBy('tanggal', 'desc');

        if ($request->jadwal_id) {
            $query->where('jadwal_id', $request->jadwal_id);
        }

        if ($request->kelas_id) {
            $query->whereHas('jadwal', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        if ($request->pertemuan_ke) {
            $query->where('pertemuan_ke', $request->pertemuan_ke);
        }

        return response()->json($query->get());
    }

    // List pertemuan + materi + tugas utk siswa by kelas
    public function indexBySiswa(Request $request)
    {
        $user = $request->user();
        $kelasId = $request->kelas_id;

        $pertemuan = Pertemuan::whereHas('jadwal', function ($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        })->with([
            'jadwal.mapel',
            'jadwal.guru',
            'materi',
            'tugas',
            'absensi' => fn ($q) => $q->where('siswa_id', auth()->id()),
        ])->orderBy('tanggal', 'desc')->get();

        return response()->json($pertemuan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal' => 'required|date',
            'pertemuan_ke' => 'required|integer|min:1',
            'topik' => 'required|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        return response()->json(Pertemuan::create($validated), 201);
    }

    public function show(Pertemuan $pertemuan)
    {
        return response()->json(
            $pertemuan->load(['jadwal.kelas', 'jadwal.mapel', 'materi', 'tugas', 'absensi.siswa:id,name'])
        );
    }

    public function update(Request $request, Pertemuan $pertemuan)
    {
        $validated = $request->validate([
            'tanggal' => 'sometimes|date',
            'pertemuan_ke' => 'sometimes|integer|min:1',
            'topik' => 'sometimes|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        $pertemuan->update($validated);

        return response()->json($pertemuan);
    }

    public function destroy(Pertemuan $pertemuan)
    {
        $pertemuan->delete();

        return response()->json(['message' => 'Pertemuan dihapus']);
    }

    // Daftar siswa siap absen utk pertemuan
    public function siswaKelas(Pertemuan $pertemuan)
    {
        $kelasId = $pertemuan->jadwal?->kelas_id;
        $siswa = collect();

        if ($kelasId) {
            $siswa = User::where('role', 'siswa')
                ->whereHas('kelas', fn ($q) => $q->where('kelas_id', $kelasId))
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'kelas']);
        }

        if ($siswa->isEmpty()) {
            $siswa = User::where('role', 'siswa')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'kelas']);
        }

        return response()->json($siswa);
    }
}
