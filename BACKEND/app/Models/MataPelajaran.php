<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'deskripsi', 'guru_id'])]
class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class, 'mapel_id');
    }
}
