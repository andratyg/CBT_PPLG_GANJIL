<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\JurnalMengajar;
use App\Models\PengumpulanTugas;
use App\Models\Pertemuan;
use App\Models\User;
use Illuminate\Http\Request;

class JurnalController extends Controller
{
    // Get / create jurnal utk pertemuan
    public function show(Pertemuan $pertemuan)
    {
        $jurnal = $pertemuan->jurnalMengajar()->first();

        return response()->json($jurnal ?: [
            'pertemuan_id' => $pertemuan->id,
            'uraian_kegiatan' => '',
            'hambatan' => '',
        ]);
    }

    public function update(Request $request, Pertemuan $pertemuan)
    {
        $validated = $request->validate([
            'uraian_kegiatan' => 'required|string',
            'hambatan' => 'nullable|string',
        ]);

        $jurnal = JurnalMengajar::updateOrCreate(
            ['pertemuan_id' => $pertemuan->id],
            $validated
        );

        return response()->json($jurnal);
    }
}
