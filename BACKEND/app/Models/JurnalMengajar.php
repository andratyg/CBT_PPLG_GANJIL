<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pertemuan_id', 'uraian_kegiatan', 'hambatan'])]
class JurnalMengajar extends Model
{
    protected $table = 'jurnal_mengajar';

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class);
    }
}
