<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProsesTemuan extends Model
{
    protected $table = 'proses_temuan';

    protected $fillable = [
        'temuan_id',
        'tanggal_proses',
        'pic',
        'tindakan',
        'catatan',
        'foto_proses',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_proses' => 'date',
        ];
    }

    public function temuan(): BelongsTo
    {
        return $this->belongsTo(Temuan::class, 'temuan_id');
    }
}
