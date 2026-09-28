<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rombel extends Model
{
    use HasFactory;

    protected $table = 'rombel';

    protected $fillable = [
        'nama',
    ];

    /**
     * Relasi ke data siswa yang berada di rombel ini.
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'rombel', 'nama');
    }
}
