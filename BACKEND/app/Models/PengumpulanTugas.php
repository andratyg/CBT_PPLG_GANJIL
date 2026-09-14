<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['tugas_id', 'siswa_id', 'file_path', 'catatan', 'nilai', 'feedback', 'dikumpulkan_at', 'dinilai_at'])]
class PengumpulanTugas extends Model
{
    protected $table = 'pengumpulan_tugas';

    protected function casts(): array
    {
        return [
            'dikumpulkan_at' => 'datetime',
            'dinilai_at' => 'datetime',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }
}
