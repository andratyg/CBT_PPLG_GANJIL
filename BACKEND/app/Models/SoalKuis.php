<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SoalKuis extends Model
{
    use HasFactory;

    protected $table = 'soal_kuis';

    public $timestamps = false;

    protected $fillable = [
        'kuis_id',
        'jenis_soal',
        'pertanyaan',
        'pilihan_a',
        'pilihan_b',
        'pilihan_c',
        'pilihan_d',
        'kunci_jawaban',
    ];

    protected function casts(): array
    {
        return [
            'kuis_id' => 'integer',
        ];
    }

    public function kuis(): BelongsTo
    {
        return $this->belongsTo(Kuis::class, 'kuis_id', 'id');
    }

    public function jawabanKuis(): HasMany
    {
        return $this->hasMany(JawabanKuis::class, 'soal_id', 'id');
    }
}
