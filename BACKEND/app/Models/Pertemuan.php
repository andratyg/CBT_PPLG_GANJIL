<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pertemuan extends Model
{
    use HasFactory;

    protected $table = 'pertemuan';

    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'pertemuan_ke',
        'bab_id',
        'topik',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'pertemuan_ke' => 'integer',
            'bab_id' => 'integer',
        ];
    }

    public function bab(): BelongsTo
    {
        return $this->belongsTo(Bab::class, 'bab_id', 'id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'pertemuan_id', 'id');
    }

    public function materi(): HasMany
    {
        return $this->hasMany(Materi::class, 'pertemuan_id', 'id');
    }

    public function jurnalMengajar(): HasMany
    {
        return $this->hasMany(JurnalMengajar::class, 'pertemuan_id', 'id');
    }

    public function keaktifan(): HasMany
    {
        return $this->hasMany(Keaktifan::class, 'pertemuan_id', 'id');
    }
}
