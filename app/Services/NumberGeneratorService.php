<?php

namespace App\Services;

use App\Models\LaporanInspeksi;
use App\Models\Temuan;
use Illuminate\Support\Str;

class NumberGeneratorService
{
    public function generateNomorLaporan(): string
    {
        $prefix = 'LPI-' . now()->format('Ymd');
        $last = LaporanInspeksi::where('nomor_laporan', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('nomor_laporan');

        $sequence = 1;
        if ($last) {
            $sequence = (int) Str::afterLast($last, '-') + 1;
        }

        return sprintf('%s-%03d', $prefix, $sequence);
    }

    public function generateKodeTemuan(): string
    {
        $prefix = 'TMN-' . now()->format('Ymd');
        $last = Temuan::where('kode_temuan', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('kode_temuan');

        $sequence = 1;
        if ($last) {
            $sequence = (int) Str::afterLast($last, '-') + 1;
        }

        return sprintf('%s-%03d', $prefix, $sequence);
    }
}
