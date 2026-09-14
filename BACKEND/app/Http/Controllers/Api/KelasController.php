<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        return response()->json(Kelas::withCount('siswa')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:20',
        ]);

        return response()->json(Kelas::create($validated), 201);
    }

    public function show(Kelas $kelas)
    {
        return response()->json(
            $kelas->load(['siswa:id,name,email', 'jadwal'])
        );
    }

    public function update(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'tahun_ajaran' => 'sometimes|string|max:20',
        ]);

        $kelas->update($validated);

        return response()->json($kelas);
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();

        return response()->json(['message' => 'Kelas dihapus']);
    }

    // Manajemen siswa dalam kelas
    public function tambahSiswa(Request $request, Kelas $kelas)
    {
        // Mendukung opsi menambahkan seluruh siswa sekaligus
        if ($request->boolean('semua_siswa') || (is_array($request->siswa_ids) && in_array('all', $request->siswa_ids))) {
            $allSiswaIds = User::where('role', 'siswa')->pluck('id')->toArray();
            $kelas->siswa()->syncWithoutDetaching($allSiswaIds);
            return response()->json([
                'message' => 'Seluruh siswa (' . count($allSiswaIds) . ' siswa) berhasil didaftarkan ke kelas.',
                'siswa' => $kelas->siswa()->get()
            ]);
        }

        $validated = $request->validate([
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'exists:users,id',
        ]);

        $kelas->siswa()->syncWithoutDetaching($validated['siswa_ids']);

        return response()->json([
            'message' => 'Siswa berhasil didaftarkan ke kelas.',
            'siswa' => $kelas->siswa()->get()
        ]);
    }

    // Mendaftarkan seluruh siswa yang ada di sistem sekaligus ke kelas ini
    public function tambahSemuaSiswa(Request $request, Kelas $kelas)
    {
        $allSiswaIds = User::where('role', 'siswa')->pluck('id')->toArray();
        $kelas->siswa()->syncWithoutDetaching($allSiswaIds);

        return response()->json([
            'message' => 'Berhasil mendaftarkan seluruh siswa (' . count($allSiswaIds) . ' siswa) ke kelas ' . $kelas->nama,
            'total_ditambahkan' => count($allSiswaIds),
            'siswa' => $kelas->siswa()->get()
        ]);
    }

    // Membuat akun siswa baru dan langsung memasukkannya ke kelas
    public function daftarSiswaBaru(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:4',
        ]);

        $siswa = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password'] ?? 'password'),
            'role' => 'siswa',
        ]);

        $kelas->siswa()->syncWithoutDetaching([$siswa->id]);

        return response()->json([
            'message' => 'Siswa baru berhasil dibuat dan didaftarkan ke kelas ' . $kelas->nama,
            'siswa' => $siswa
        ], 201);
    }

    public function hapusSiswa(Kelas $kelas, User $siswa)
    {
        $kelas->siswa()->detach($siswa->id);

        return response()->json(['message' => 'Siswa dikeluarkan dari kelas']);
    }
}
