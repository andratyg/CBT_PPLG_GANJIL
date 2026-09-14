<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\PengumpulanTugas;
use App\Models\Pertemuan;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function publicDashboard()
    {
        return response()->json([
            'total_kelas' => Kelas::count(),
            'total_mapel' => MataPelajaran::count(),
            'total_pertemuan' => Pertemuan::count(),
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isGuru()) {
            $mapelIds = $user->mataPelajaran()->pluck('id');

            $data = [
                'total_mapel' => $mapelIds->count(),
                'total_pertemuan' => Pertemuan::whereHas('jadwal', fn ($q) => $q->whereIn('mapel_id', $mapelIds))->count(),
                'total_siswa' => User::where('role', 'siswa')->count(),
                'total_tugas_belum_dinilai' => PengumpulanTugas::whereNull('nilai')->count(),
                'mapel' => MataPelajaran::whereIn('id', $mapelIds)->withCount('jadwal')->get(),
            ];
        } else {
            $data = [
                'kelas' => $user->kelas()->with('jadwal.mapel')->get(),
                'tugas_pending' => Tugas::whereHas('pengumpulan', fn ($q) => $q->where('siswa_id', $user->id))
                    ->where('deadline', '>=', now())->count(),
                'tugas_terkirim' => PengumpulanTugas::where('siswa_id', $user->id)->count(),
                'nilai_rata' => round(PengumpulanTugas::where('siswa_id', $user->id)->whereNotNull('nilai')->avg('nilai'), 1),
            ];
        }

        return response()->json($data);
    }
}

