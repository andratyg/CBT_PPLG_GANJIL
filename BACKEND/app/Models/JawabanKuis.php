<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanKuis extends Model
{
    use HasFactory;

    protected $table = 'jawaban_kuis';

    public $timestamps = false;

    protected $fillable = [
        'kuis_id',
        'siswa_id',
        'soal_id',
        'jawaban',
        'skor',
    ];

    protected function casts(): array
    {
        return [
            'kuis_id' => 'integer',
            'siswa_id' => 'integer',
            'soal_id' => 'integer',
            'skor' => 'integer',
        ];
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class, 'kuis_id', 'id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }

    public function soalKuis(): BelongsTo
    {
        return $this->belongsTo(SoalKuis::class, 'soal_id', 'id');
    }
}
