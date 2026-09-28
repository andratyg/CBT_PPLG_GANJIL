<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
    /**
     * Halaman Dashboard Utama Admin Guru.
     */
    public function dashboard()
    {
        return view('admin.dashboard', [
            'guruNama' => session('guru_nama', 'Pak Andi Wijaya'),
            'guruNip' => '198204122009031002',
        ]);
    }

    /**
     * Halaman Kelola Presensi Siswa (Berbasis Database & Multi-Rombel).
     */
    public function index(Request $request)
    {
        try {
            // Ambil daftar rombel unik dari master rombel dan tabel siswa
            $rombelsDb = \App\Models\Rombel::orderBy('nama')->pluck('nama');
            $siswaRombel = Siswa::whereNotNull('rombel')->where('rombel', '!=', '')->distinct()->pluck('rombel');
            $daftarRombel = $rombelsDb->merge($siswaRombel)->filter()->unique()->values();

            if ($daftarRombel->isEmpty()) {
                $daftarRombel = collect(['XI PPLG 1', 'XI PPLG 2']);
            }

            // Tentukan rombel aktif dari parameter URL (default ke rombel pertama)
            $activeRombel = $request->query('rombel', $daftarRombel->first());

            // Pastikan jika rombel baru belum punya siswa tapi dipilih lewat query, tetap masuk di list
            if (!$daftarRombel->contains($activeRombel)) {
                $daftarRombel->push($activeRombel);
            }

            // Ambil daftar pertemuan dari database
            $daftarPertemuan = Pertemuan::orderBy('pertemuan_ke')->get();
            if ($daftarPertemuan->isEmpty()) {
                $bab = Bab::firstOrCreate(['nama_bab' => 'Basis Data & Pemrograman Web']);
                $p12 = Pertemuan::create([
                    'pertemuan_ke' => 12,
                    'tanggal' => '2026-09-18',
                    'bab_id' => $bab->id,
                    'topik' => 'Perancangan Database & Query SQL Relasional',
                ]);
                $daftarPertemuan = collect([$p12]);
            }

            // Tentukan pertemuan aktif
            $pertemuanId = $request->query('pertemuan_id', $daftarPertemuan->last()->id);
            $activePertemuan = Pertemuan::find($pertemuanId) ?? $daftarPertemuan->last();

            // Ambil siswa untuk rombel terpilih diurutkan otomatis sesuai abjad nama (A-Z)
            $siswaDb = Siswa::where('rombel', $activeRombel)->orderBy('nama', 'asc')->get();

            // Ambil presensi yang sudah tersimpan untuk pertemuan ini
            $presensiMap = Presensi::where('pertemuan_id', $activePertemuan->id)
                ->whereIn('siswa_id', $siswaDb->pluck('id'))
                ->pluck('status', 'siswa_id');

            $daftarSiswa = [];
            foreach ($siswaDb as $index => $s) {
                $status = $presensiMap[$s->id] ?? 'hadir';
                $daftarSiswa[] = [
                    'id' => $s->id,
                    'no' => str_pad($index + 1, 2, '0', STR_PAD_LEFT),
                    'nama' => $s->nama,
                    'nis' => $s->nis,
                    'rombel' => $s->rombel,
                    'rayon' => $s->rayon,
                    'status' => $status,
                    'keterangan' => isset($presensiMap[$s->id]) ? 'Tersimpan' : 'Belum diabsen',
                ];
            }
        } catch (\Throwable $e) {
            $daftarRombel = collect(['XI PPLG 1', 'XI PPLG 2']);
            $activeRombel = 'XI PPLG 1';
            $daftarPertemuan = collect([]);
            $activePertemuan = null;
            $daftarSiswa = [];
        }

        return view('admin.presensi.dashboard', [
            'daftarRombel' => $daftarRombel,
            'activeRombel' => $activeRombel,
            'daftarPertemuan' => $daftarPertemuan,
            'activePertemuan' => $activePertemuan,
            'daftarSiswa' => $daftarSiswa,
            'guruNama' => session('guru_nama', 'Pak Andi Wijaya'),
            'guruNip' => '198204122009031002',
        ]);
    }

    /**
     * Simpan Presensi ke Database MySQL.
     * Validasi dipindahkan dari routes/web.php ke controller ini.
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'pertemuan_id' => 'required|integer',
            'presensi' => 'required|array',
        ], [
            'pertemuan_id.required' => 'Pertemuan wajib dipilih.',
            'presensi.required' => 'Data presensi wajib diisi.',
        ]);

        $pertemuanId = $request->input('pertemuan_id');
        $presensiData = $request->input('presensi');

        foreach ($presensiData as $siswaId => $status) {
            Presensi::updateOrCreate(
                [
                    'siswa_id' => $siswaId,
                    'pertemuan_id' => $pertemuanId,
                ],
                [
                    'status' => $status,
                ]
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil disimpan ke database!',
                'count' => count($presensiData),
            ]);
        }

        return back()->with('success', 'Data presensi berhasil disimpan ke database!');
    }

    /**
     * Tambah Pertemuan Baru ke Database.
     * Validasi dipindahkan dari routes/web.php ke controller ini.
     */
    public function tambahPertemuan(Request $request)
    {
        $validated = $request->validate([
            'pertemuan_ke' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'topik' => 'required|string|max:200',
            'bab_id' => 'nullable|integer',
        ], [
            'pertemuan_ke.required' => 'Nomor pertemuan wajib diisi.',
            'tanggal.required' => 'Tanggal pertemuan wajib diisi.',
            'topik.required' => 'Topik/judul materi pertemuan wajib diisi.',
        ]);

        if (empty($validated['bab_id'])) {
            $bab = Bab::firstOrCreate(['nama_bab' => 'Basis Data & Pemrograman Web']);
            $validated['bab_id'] = $bab->id;
        }

        $pertemuan = Pertemuan::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pertemuan ' . $pertemuan->pertemuan_ke . ' berhasil dibuat!',
                'data' => $pertemuan,
            ]);
        }

        return redirect()->route('admin.presensi', [
            'rombel' => $request->input('rombel', 'XI PPLG 1'),
            'pertemuan_id' => $pertemuan->id,
        ])->with('success', 'Pertemuan berhasil ditambahkan!');
    }
}
