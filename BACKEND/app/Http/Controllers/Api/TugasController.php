<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanTugas;
use App\Models\Tugas;
use App\Models\Pertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index(Pertemuan $pertemuan)
    {
        return response()->json(
            $pertemuan->tugas()->withCount('pengumpulan')->get()
        );
    }

    private function authorizeGuru(Request $request, Pertemuan $pertemuan): void
    {
        abort_unless($pertemuan->loadMissing('jadwal')->jadwal->guru_id === $request->user()->id, 403, 'Bukan pemilik kelas/mapel.');
    }

    public function store(Request $request, Pertemuan $pertemuan)
    {
        $this->authorizeGuru($request, $pertemuan);
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date|after:now',
            'nilai_maksimal' => 'sometimes|integer|min:1|max:100',
        ]);

        $validated['pertemuan_id'] = $pertemuan->id;
        $tugas = Tugas::create($validated);

        return response()->json($tugas, 201);
    }

    public function show(Tugas $tugas)
    {
        return response()->json(
            $tugas->load(['pertemuan', 'pengumpulan.siswa'])
        );
    }

    public function update(Request $request, Tugas $tugas)
    {
        $this->authorizeGuru($request, $tugas->load('pertemuan')->pertemuan);
        $validated = $request->validate([
            'judul' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'deadline' => 'sometimes|date',
            'nilai_maksimal' => 'sometimes|integer|min:1|max:100',
        ]);

        $tugas->update($validated);

        return response()->json($tugas);
    }

    public function destroy(Request $request, Tugas $tugas)
    {
        $this->authorizeGuru($request, $tugas->load('pertemuan')->pertemuan);
        $tugas->delete();

        return response()->json(['message' => 'Tugas dihapus']);
    }

    // Pengumpulan
    public function kumpulkan(Request $request, Tugas $tugas)
    {
        $request->validate([
            'file' => 'required|file|max:51200',
            'catatan' => 'nullable|string',
            'siswa_id' => 'nullable|exists:users,id',
        ]);

        $siswaId = $request->user() ? $request->user()->id : $request->input('siswa_id');
        if (!$siswaId) {
            return response()->json([
                'message' => 'Pilih nama siswa pengumpul untuk mengirimkan tugas.'
            ], 422);
        }

        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->update([
                'file_path' => $request->file('file')->store('tugas', 'public'),
                'catatan' => $request->catatan,
                'dikumpulkan_at' => now(),
            ]);

            return response()->json($existing);
        }

        $pengumpulan = PengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'siswa_id' => $siswaId,
            'file_path' => $request->file('file')->store('tugas', 'public'),
            'catatan' => $request->catatan,
            'dikumpulkan_at' => now(),
        ]);

        return response()->json($pengumpulan, 201);
    }

    public function nilai(Request $request, PengumpulanTugas $pengumpulan)
    {
        $this->authorizeGuru($request, $pengumpulan->load('tugas.pertemuan')->tugas->pertemuan);
        $validated = $request->validate([
            'nilai' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan->update([
            ...$validated,
            'dinilai_at' => now(),
        ]);

        return response()->json($pengumpulan);
    }

    public function rekapNilai(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
        ]);

        $pengumpulan = PengumpulanTugas::whereHas('tugas.pertemuan.jadwal', function ($q) use ($request) {
            $q->where('kelas_id', $request->kelas_id)
              ->where('mapel_id', $request->mapel_id);
        })->with(['siswa:id,name', 'tugas:id,judul,nilai_maksimal'])->get();

        $rekap = $pengumpulan->groupBy('siswa_id')->map(function ($items) {
            return [
                'siswa' => $items->first()->siswa,
                'tugas' => $items->map(fn ($p) => [
                    'tugas' => $p->tugas->judul,
                    'nilai' => $p->nilai,
                    'maksimal' => $p->tugas->nilai_maksimal,
                ]),
                'rata_rata' => round($items->avg('nilai'), 1),
            ];
        })->values();

        return response()->json($rekap);
    }
}
