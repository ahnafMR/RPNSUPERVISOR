<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanInspeksi extends Model
{
    protected $table = 'laporan_inspeksi';

    protected $fillable = [
        'nomor_laporan',
        'user_id',
        'lokasi_id',
        'checkin_id',
        'tanggal_inspeksi',
        'area',
        'kategori',
        'deskripsi',
        'status',
        'catatan_approval',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_inspeksi' => 'date',
            'approved_at' => 'datetime',
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

    public function checkin(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class, 'checkin_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function fotoLaporans(): HasMany
    {
        return $this->hasMany(FotoLaporan::class, 'laporan_id');
    }

    public function temuans(): HasMany
    {
        return $this->hasMany(Temuan::class, 'laporan_id');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'menunggu_review' => 'Menunggu Review',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }
}
