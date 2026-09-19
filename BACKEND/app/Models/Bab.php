<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bab extends Model
{
    use HasFactory;

    protected $table = 'bab';

    public $timestamps = false;

    protected $fillable = [
        'nama_bab',
    ];

    public function pertemuan(): HasMany
    {
        return $this->hasMany(Pertemuan::class, 'bab_id', 'id');
    }

    public function materi(): HasMany
    {
        return $this->hasMany(Materi::class, 'bab_id', 'id');
    }
}
