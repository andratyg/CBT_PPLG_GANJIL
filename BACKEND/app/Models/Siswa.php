<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    public $timestamps = false;

    protected $fillable = [
        'nis',
        'nama',
        'rombel',
        'rayon',
    ];

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'siswa_id', 'id');
    }

    public function pengumpulanTugas(): HasMany
    {
        return $this->hasMany(PengumpulanTugas::class, 'siswa_id', 'id');
    }

    public function jawabanKuis(): HasMany
    {
        return $this->hasMany(JawabanKuis::class, 'siswa_id', 'id');
    }

    public function keaktifan(): HasMany
    {
        return $this->hasMany(Keaktifan::class, 'siswa_id', 'id');
    }
}
