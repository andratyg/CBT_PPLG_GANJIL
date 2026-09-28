<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bab;
use App\Models\Guru;
use App\Models\Rombel;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class MasterDataController extends Controller
{
    /**
     * Tampilkan halaman utama Data Master (dengan tab Siswa dan Bab).
     */
    public function index(Request $request)
    {
        $tab = in_array($request->query('tab'), ['siswa', 'bab']) ? $request->query('tab') : 'siswa';
        $rombel = $request->query('rombel', '');

        // Hitung total dan rombel untuk badge tab
        $totalSiswa = Siswa::count();
        $totalBab = Bab::count();
        $rombelsDb = Rombel::orderBy('nama')->pluck('nama');
        $siswaRombel = Siswa::whereNotNull('rombel')->where('rombel', '!=', '')->distinct()->pluck('rombel');
        $daftarRombel = $rombelsDb->merge($siswaRombel)->filter()->unique()->values();

        if ($daftarRombel->isEmpty()) {
            $daftarRombel = collect(['XI PPLG 1', 'XI PPLG 2']);
        }

        return view('admin.data.index', [
            'tab'          => $tab,
            'rombel'       => $rombel,
            'totalSiswa'   => $totalSiswa,
            'totalBab'     => $totalBab,
            'daftarRombel' => $daftarRombel,
            'guruNama'     => session('guru_nama', 'Pak Andi Wijaya'),
            'guruNip'      => '198204122009031002',
        ]);
    }

    /**
     * Endpoint DataTables Server-Side untuk Data Siswa.
     */
    public function dataSiswa(Request $request)
    {
        $query = Siswa::query();

        if ($request->filled('rombel')) {
            $query->where('rombel', $request->query('rombel'));
        }

        // Urutkan default berdasarkan abjad nama siswa (A-Z)
        if (!$request->has('order')) {
            $query->orderBy('nama', 'asc');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nis', function ($row) {
                return '<span class="font-semibold text-slate-800">' . e($row->nis) . '</span>';
            })
            ->editColumn('nama', function ($row) {
                return '<span class="font-bold text-slate-900">' . e($row->nama) . '</span>';
            })
            ->editColumn('rombel', function ($row) {
                return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">' . e($row->rombel) . '</span>';
            })
            ->editColumn('rayon', function ($row) {
                return '<span class="text-slate-600">' . e($row->rayon ?? '-') . '</span>';
            })
            ->addColumn('aksi', function ($row) {
                $btnEdit = '<button type="button" onclick="openModalEditSiswa(' . $row->id . ', \'' . addslashes(e($row->nis)) . '\', \'' . addslashes(e($row->nama)) . '\', \'' . addslashes(e($row->rombel)) . '\', \'' . addslashes(e($row->rayon ?? '')) . '\')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit Siswa">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </button>';

                $btnDelete = '<form action="' . route('admin.master.siswa.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Apakah Anda yakin ingin menghapus data siswa ' . addslashes(e($row->nama)) . '?\')">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Siswa">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </form>';

                return '<div class="inline-flex items-center justify-end gap-1.5 w-full">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['nis', 'nama', 'rombel', 'rayon', 'aksi'])
            ->make(true);
    }

    /**
     * Endpoint DataTables Server-Side untuk Data Bab Materi.
     */
    public function dataBab(Request $request)
    {
        $query = Bab::query();

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('nama_bab', function ($row) {
                return '<span class="font-bold text-slate-800">' . e($row->nama_bab) . '</span>';
            })
            ->addColumn('deskripsi', function ($row) {
                return '<span class="text-slate-500">' . e($row->deskripsi ?? 'Materi Pembelajaran Basis Data & Web') . '</span>';
            })
            ->addColumn('aksi', function ($row) {
                $btnEdit = '<button type="button" onclick="openModalEditBab(' . $row->id . ', \'' . addslashes(e($row->nama_bab)) . '\')" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit Bab">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </button>';

                $btnDelete = '<form action="' . route('admin.master.bab.destroy', $row->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Hapus bab materi ' . addslashes(e($row->nama_bab)) . '?\')">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <button type="submit" class="p-1.5 text-rose-500 hover:bg-rose-50 rounded-lg transition cursor-pointer" title="Hapus Bab">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                    </button>
                </form>';

                return '<div class="inline-flex items-center justify-end gap-1.5 w-full">' . $btnEdit . $btnDelete . '</div>';
            })
            ->rawColumns(['nama_bab', 'deskripsi', 'aksi'])
            ->make(true);
    }

    // ==========================================
    // ==========================================
    // CRUD DATA MASTER ROMBEL (KELAS)
    // ==========================================

    public function storeRombel(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50|unique:rombel,nama',
        ], [
            'nama.required' => 'Nama rombel/kelas wajib diisi.',
            'nama.unique' => 'Rombel ini sudah terdaftar.',
        ]);

        $rombel = Rombel::create([
            'nama' => trim($validated['nama']),
        ]);

        return redirect()->route('admin.master', ['tab' => 'siswa', 'rombel' => $rombel->nama])
            ->with('success', "Rombel {$rombel->nama} berhasil ditambahkan!");
    }

    // ==========================================
    // CRUD DATA MASTER SISWA
    // ==========================================

    public function storeSiswa(Request $request)
    {
        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis',
            'nama' => 'required|string|max:100',
            'rombel' => 'required|string|max:50',
            'rayon' => 'required|string|max:50',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah terdaftar untuk siswa lain.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'rombel.required' => 'Rombel/Kelas wajib diisi.',
            'rayon.required' => 'Rayon siswa wajib diisi.',
        ]);

        // Pastikan rombel tersimpan di master rombel
        Rombel::firstOrCreate(['nama' => trim($validated['rombel'])]);

        $siswa = Siswa::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Siswa {$siswa->nama} berhasil ditambahkan!",
                'data' => $siswa,
            ]);
        }

        return redirect()->route('admin.master', ['tab' => 'siswa', 'rombel' => $siswa->rombel])->with('success', "Siswa {$siswa->nama} berhasil ditambahkan!");
    }

    public function updateSiswa(Request $request, int $id)
    {
        $siswa = Siswa::findOrFail($id);

        $validated = $request->validate([
            'nis' => 'required|string|max:20|unique:siswa,nis,' . $id,
            'nama' => 'required|string|max:100',
            'rombel' => 'required|string|max:50',
            'rayon' => 'required|string|max:50',
        ], [
            'nis.required' => 'NIS wajib diisi.',
            'nis.unique' => 'NIS sudah digunakan oleh siswa lain.',
            'nama.required' => 'Nama siswa wajib diisi.',
            'rombel.required' => 'Rombel wajib diisi.',
            'rayon.required' => 'Rayon siswa wajib diisi.',
        ]);

        // Pastikan rombel tersimpan di master rombel
        Rombel::firstOrCreate(['nama' => trim($validated['rombel'])]);

        $siswa->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data siswa {$siswa->nama} berhasil diperbarui!",
                'data' => $siswa,
            ]);
        }

        return redirect()->route('admin.master', ['tab' => 'siswa'])->with('success', "Data siswa {$siswa->nama} berhasil diperbarui!");
    }

    public function destroySiswa(int $id)
    {
        $siswa = Siswa::findOrFail($id);
        $nama = $siswa->nama;
        $siswa->delete();

        return redirect()->route('admin.master', ['tab' => 'siswa'])->with('success', "Data siswa {$nama} berhasil dihapus.");
    }

    // ==========================================
    // CRUD DATA MASTER GURU
    // ==========================================

    public function storeGuru(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:guru,email',
            'password' => 'required|string|min:6|max:255',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'email.required' => 'Email guru wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email guru sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal :min karakter.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $guru = Guru::create($validated);

        return redirect()->route('admin.master', ['tab' => 'guru'])->with('success', "Data guru {$guru->nama} berhasil ditambahkan!");
    }

    public function updateGuru(Request $request, int $id)
    {
        $guru = Guru::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|string|email|max:100|unique:guru,email,' . $id,
            'password' => 'nullable|string|min:6|max:255',
        ], [
            'nama.required' => 'Nama guru wajib diisi.',
            'email.required' => 'Email guru wajib diisi.',
            'email.unique' => 'Email sudah digunakan guru lain.',
            'password.min' => 'Password minimal :min karakter.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $guru->update($validated);

        return redirect()->route('admin.master', ['tab' => 'guru'])->with('success', "Data guru {$guru->nama} berhasil diperbarui!");
    }

    public function destroyGuru(int $id)
    {
        $guru = Guru::findOrFail($id);
        $nama = $guru->nama;
        $guru->delete();

        return redirect()->route('admin.master', ['tab' => 'guru'])->with('success', "Data guru {$nama} berhasil dihapus.");
    }

    // ==========================================
    // CRUD DATA MASTER BAB
    // ==========================================

    public function storeBab(Request $request)
    {
        $validated = $request->validate([
            'nama_bab' => 'required|string|max:150',
        ], [
            'nama_bab.required' => 'Judul/Nama bab materi wajib diisi.',
        ]);

        $bab = Bab::create($validated);

        return redirect()->route('admin.master', ['tab' => 'bab'])->with('success', "Data bab {$bab->nama_bab} berhasil ditambahkan!");
    }

    public function updateBab(Request $request, int $id)
    {
        $bab = Bab::findOrFail($id);

        $validated = $request->validate([
            'nama_bab' => 'required|string|max:150',
        ], [
            'nama_bab.required' => 'Judul/Nama bab materi wajib diisi.',
        ]);

        $bab->update($validated);

        return redirect()->route('admin.master', ['tab' => 'bab'])->with('success', "Data bab berhasil diperbarui!");
    }

    public function destroyBab(int $id)
    {
        $bab = Bab::findOrFail($id);
        $nama = $bab->nama_bab;
        $bab->delete();

        return redirect()->route('admin.master', ['tab' => 'bab'])->with('success', "Data bab {$nama} berhasil dihapus.");
    }
}
