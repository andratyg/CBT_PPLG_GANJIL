<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materi';

    public $timestamps = false;

    protected $fillable = [
        'bab_id',
        'pertemuan_id',
        'judul',
        'jenis_file',
        'path_file',
        'url_eksternal',
    ];

    protected function casts(): array
    {
        return [
            'bab_id' => 'integer',
            'pertemuan_id' => 'integer',
        ];
    }

    public function bab(): BelongsTo
    {
        return $this->belongsTo(Bab::class, 'bab_id', 'id');
    }

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class, 'pertemuan_id', 'id');
    }
}
