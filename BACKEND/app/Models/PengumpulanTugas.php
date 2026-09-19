<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumpulanTugas extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan_tugas';

    public $timestamps = false;

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'path_file',
        'skor',
        'catatan',
        'status',
        'waktu_kumpul',
    ];

    protected function casts(): array
    {
        return [
            'tugas_id' => 'integer',
            'siswa_id' => 'integer',
            'skor' => 'integer',
            'waktu_kumpul' => 'datetime',
        ];
    }

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id', 'id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }
}
