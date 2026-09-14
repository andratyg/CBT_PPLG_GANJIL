<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Pertemuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(Pertemuan $pertemuan)
    {
        return response()->json($pertemuan->materi);
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
            'konten' => 'nullable|string',
            'file' => 'nullable|file|max:51200', // 50MB max
        ]);

        $data = [
            'pertemuan_id' => $pertemuan->id,
            'judul' => $validated['judul'],
            'konten' => $validated['konten'] ?? null,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = $file->store('materi', 'public');
            $data['tipe_file'] = $file->getClientOriginalExtension();
        }

        $materi = Materi::create($data);

        return response()->json($materi, 201);
    }

    public function show(Materi $materi)
    {
        return response()->json($materi->load('pertemuan'));
    }

    public function update(Request $request, Materi $materi)
    {
        $this->authorizeGuru($request, $materi->load('pertemuan')->pertemuan);
        $validated = $request->validate([
            'judul' => 'sometimes|string|max:255',
            'konten' => 'nullable|string',
            'file' => 'nullable|file|max:51200',
        ]);

        if ($request->hasFile('file')) {
            if ($materi->file_path) {
                Storage::disk('public')->delete($materi->file_path);
            }
            $file = $request->file('file');
            $validated['file_path'] = $file->store('materi', 'public');
            $validated['tipe_file'] = $file->getClientOriginalExtension();
        }

        unset($validated['file']);
        $materi->update($validated);

        return response()->json($materi);
    }

    public function destroy(Request $request, Materi $materi)
    {
        $this->authorizeGuru($request, $materi->load('pertemuan')->pertemuan);
        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }
        $materi->delete();

        return response()->json(['message' => 'Materi dihapus']);
    }
}
