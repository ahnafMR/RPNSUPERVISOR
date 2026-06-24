<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilTemuan extends Model
{
    protected $table = 'hasil_temuan';

    protected $fillable = [
        'temuan_id',
        'tanggal_selesai',
        'hasil_perbaikan',
        'catatan_akhir',
        'foto_hasil',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_selesai' => 'date',
        ];
    }

    public function temuan(): BelongsTo
    {
        return $this->belongsTo(Temuan::class, 'temuan_id');
    }
}
