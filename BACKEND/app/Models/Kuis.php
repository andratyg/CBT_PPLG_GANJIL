<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kuis extends Model
{
    use HasFactory;

    protected $table = 'kuis';

    public $timestamps = false;

    protected $fillable = [
        'judul',
        'token',
        'durasi_menit',
        'waktu_mulai',
    ];

    protected function casts(): array
    {
        return [
            'durasi_menit' => 'integer',
            'waktu_mulai' => 'datetime',
        ];
    }

    public function soalKuis(): HasMany
    {
        return $this->hasMany(SoalKuis::class, 'kuis_id', 'id');
    }

    public function jawabanKuis(): HasMany
    {
        return $this->hasMany(JawabanKuis::class, 'kuis_id', 'id');
    }
}
