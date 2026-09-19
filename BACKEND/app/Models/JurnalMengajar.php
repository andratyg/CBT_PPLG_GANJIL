<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalMengajar extends Model
{
    use HasFactory;

    protected $table = 'jurnal_mengajar';

    public $timestamps = false;

    protected $fillable = [
        'guru_id',
        'pertemuan_id',
        'tanggal',
        'pertemuan_ke',
        'topik',
        'uraian_kegiatan',
        'hambatan',
    ];

    protected function casts(): array
    {
        return [
            'guru_id' => 'integer',
            'pertemuan_id' => 'integer',
            'tanggal' => 'date',
            'pertemuan_ke' => 'integer',
        ];
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id', 'id');
    }

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id', 'id');
    }
}
