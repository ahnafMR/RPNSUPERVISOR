<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoLaporan extends Model
{
    protected $table = 'foto_laporan';

    protected $fillable = [
        'laporan_id',
        'foto',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanInspeksi::class, 'laporan_id');
    }
}
