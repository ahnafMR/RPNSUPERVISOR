<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Temuan extends Model
{
    protected $table = 'temuan';

    protected $fillable = [
        'laporan_id',
        'kode_temuan',
        'judul_temuan',
        'deskripsi',
        'tingkat_risiko',
        'rekomendasi',
        'status',
    ];

    public function laporan(): BelongsTo
    {
        return $this->belongsTo(LaporanInspeksi::class, 'laporan_id');
    }

    public function fotoTemuans(): HasMany
    {
        return $this->hasMany(FotoTemuan::class, 'temuan_id');
    }

    public function prosesTemuan(): HasOne
    {
        return $this->hasOne(ProsesTemuan::class, 'temuan_id');
    }

    public function hasilTemuan(): HasOne
    {
        return $this->hasOne(HasilTemuan::class, 'temuan_id');
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

    public function risikoLabel(): string
    {
        return match ($this->tingkat_risiko) {
            'rendah' => 'Rendah',
            'sedang' => 'Sedang',
            'tinggi' => 'Tinggi',
            default => $this->tingkat_risiko,
        };
    }
}
