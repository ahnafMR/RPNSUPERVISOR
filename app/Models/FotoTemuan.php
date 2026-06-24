<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoTemuan extends Model
{
    protected $table = 'foto_temuan';

    protected $fillable = [
        'temuan_id',
        'foto',
    ];

    public function temuan(): BelongsTo
    {
        return $this->belongsTo(Temuan::class, 'temuan_id');
    }
}
