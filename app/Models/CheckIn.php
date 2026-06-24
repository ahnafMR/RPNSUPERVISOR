<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CheckIn extends Model
{
    protected $table = 'checkin';

    protected $fillable = [
        'user_id',
        'lokasi_id',
        'latitude',
        'longitude',
        'foto_selfie',
        'waktu_checkin',
        'waktu_checkout',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'waktu_checkin' => 'datetime',
            'waktu_checkout' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lokasi(): BelongsTo
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function laporans(): HasMany
    {
        return $this->hasMany(LaporanInspeksi::class, 'checkin_id');
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }
}
