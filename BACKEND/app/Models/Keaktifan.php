<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keaktifan extends Model
{
    use HasFactory;

    protected $table = 'keaktifan';

    public $timestamps = false;

    protected $fillable = [
        'siswa_id',
        'pertemuan_id',
        'poin',
        'keterangan',
        'tanggal',
    ];

    protected function casts(): array
    {
        return [
            'siswa_id' => 'integer',
            'pertemuan_id' => 'integer',
            'poin' => 'integer',
            'tanggal' => 'date',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id', 'id');
    }
}
