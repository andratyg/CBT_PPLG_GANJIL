<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Pertemuan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function index(Pertemuan $pertemuan)
    {
        return response()->json(
            $pertemuan->absensi()->with('siswa')->get()
        );
    }

    public function store(Request $request, Pertemuan $pertemuan)
    {
        $validated = $request->validate([
            'absensi' => 'required|array',
            'absensi.*.siswa_id' => 'required|exists:users,id',
            'absensi.*.status' => 'required|in:hadir,izin,sakit,dispen,alpa',
            'absensi.*.keterangan' => 'nullable|string',
        ]);

        $now = now();
        $records = array_map(fn ($item) => [
            'pertemuan_id' => $pertemuan->id,
            'siswa_id' => $item['siswa_id'],
            'status' => $item['status'],
            'keterangan' => $item['keterangan'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $validated['absensi']);

        DB::transaction(function () use ($records) {
            Absensi::upsert($records, ['pertemuan_id', 'siswa_id'], ['status', 'keterangan', 'updated_at']);
        });

        $result = Absensi::where('pertemuan_id', $pertemuan->id)
            ->whereIn('siswa_id', array_column($validated['absensi'], 'siswa_id'))
            ->get();

        return response()->json($result, 201);
    }

    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.pertemuan_id' => 'required|exists:pertemuan,id',
            'items.*.siswa_id' => 'required|exists:users,id',
            'items.*.status' => 'required|in:hadir,izin,sakit,dispen,alpa',
            'items.*.keterangan' => 'nullable|string',
        ]);

        $now = now();
        $records = array_map(fn ($item) => [
            'pertemuan_id' => $item['pertemuan_id'],
            'siswa_id' => $item['siswa_id'],
            'status' => $item['status'],
            'keterangan' => $item['keterangan'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ], $validated['items']);

        DB::transaction(function () use ($records) {
            Absensi::upsert($records, ['pertemuan_id', 'siswa_id'], ['status', 'keterangan', 'updated_at']);
        });

        return response()->json([
            'message' => 'Presensi multi-pertemuan berhasil disimpan.',
            'saved_count' => count($records),
        ], 200);
    }

    public function matrix(Request $request)
    {
        $pertemuanQuery = Pertemuan::with(['jadwal.kelas', 'jadwal.mapel']);
        if ($request->filled('jadwal_id')) {
            $pertemuanQuery->where('jadwal_id', $request->jadwal_id);
        } elseif ($request->filled('kelas_id') && $request->filled('mapel_id')) {
            $pertemuanQuery->whereHas('jadwal', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id)
                  ->where('mapel_id', $request->mapel_id);
            });
        } elseif ($request->filled('kelas_id')) {
            $pertemuanQuery->whereHas('jadwal', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }
        $pertemuanList = $pertemuanQuery->orderBy('pertemuan_ke')->get();

        // Ambil daftar siswa kelas
        $kelasId = $request->kelas_id ?? $pertemuanList->first()?->jadwal?->kelas_id;
        $siswaList = [];
        if ($kelasId) {
            $kelas = Kelas::with('siswa')->find($kelasId);
            $siswaList = $kelas ? $kelas->siswa : [];
        }
        
        if (empty($siswaList) || count($siswaList) === 0) {
            $siswaList = User::where('role', 'siswa')->get();
        }

        $pertemuanIds = $pertemuanList->pluck('id');
        $absensiRecords = Absensi::whereIn('pertemuan_id', $pertemuanIds)->get();

        // Susun matriks [siswa_id][pertemuan_id]
        $matrix = [];
        foreach ($absensiRecords as $rec) {
            $matrix[$rec->siswa_id][$rec->pertemuan_id] = [
                'status' => $rec->status,
                'keterangan' => $rec->keterangan,
            ];
        }

        // Susun statistik kumulatif seluruh pertemuan per siswa
        $totalPertemuan = $pertemuanList->count();
        $rekapSiswa = [];
        foreach ($siswaList as $s) {
            $h = 0; $i = 0; $sk = 0; $d = 0; $a = 0;
            $detailPertemuan = [];
            foreach ($pertemuanList as $p) {
                $status = $matrix[$s->id][$p->id]['status'] ?? null;
                $ket = $matrix[$s->id][$p->id]['keterangan'] ?? null;
                if ($status === 'hadir') $h++;
                elseif ($status === 'izin') $i++;
                elseif ($status === 'sakit') $sk++;
                elseif ($status === 'dispen') $d++;
                elseif ($status === 'alpa') $a++;

                $detailPertemuan[] = [
                    'pertemuan_id' => $p->id,
                    'pertemuan_ke' => $p->pertemuan_ke,
                    'tanggal' => $p->tanggal,
                    'topik' => $p->topik,
                    'status' => $status,
                    'keterangan' => $ket,
                ];
            }
            $tercatat = $h + $i + $sk + $d + $a;
            $persentase = $totalPertemuan > 0 ? round((($h + $d) / $totalPertemuan) * 100, 1) : 0;

            $rekapSiswa[] = [
                'siswa' => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'email' => $s->email,
                    'kelas' => $s->kelas,
                ],
                'stats' => [
                    'hadir' => $h,
                    'izin' => $i,
                    'sakit' => $sk,
                    'dispen' => $d,
                    'alpa' => $a,
                    'tercatat' => $tercatat,
                    'total_pertemuan' => $totalPertemuan,
                    'persentase' => $persentase,
                ],
                'kehadiran' => $matrix[$s->id] ?? [],
                'detail_pertemuan' => $detailPertemuan,
            ];
        }

        return response()->json([
            'pertemuan' => $pertemuanList,
            'siswa' => $siswaList,
            'matrix' => $matrix,
            'rekap_siswa' => $rekapSiswa,
            'total_pertemuan' => $totalPertemuan,
        ]);
    }

    public function rekap(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mapel_id' => 'required|exists:mata_pelajaran,id',
        ]);

        $absensi = Absensi::whereHas('pertemuan.jadwal', function ($q) use ($request) {
            $q->where('kelas_id', $request->kelas_id)
              ->where('mapel_id', $request->mapel_id);
        })->with(['siswa:id,name', 'pertemuan:id,tanggal,pertemuan_ke'])->get();

        $rekap = $absensi->groupBy('siswa_id')->map(function ($items) {
            $siswa = $items->first()->siswa;

            return [
                'siswa' => $siswa,
                'hadir' => $items->where('status', 'hadir')->count(),
                'izin' => $items->where('status', 'izin')->count(),
                'sakit' => $items->where('status', 'sakit')->count(),
                'dispen' => $items->where('status', 'dispen')->count(),
                'alpa' => $items->where('status', 'alpa')->count(),
                'total' => $items->count(),
            ];
        })->values();

        return response()->json($rekap);
    }
}
