<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\LaporanInspeksi;
use App\Models\Lokasi;
use App\Models\Temuan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_laporan' => LaporanInspeksi::count(),
            'total_temuan' => Temuan::count(),
            'temuan_diproses' => Temuan::where('status', 'diproses')->count(),
            'temuan_selesai' => Temuan::where('status', 'selesai')->count(),
            'temuan_risiko_tinggi' => Temuan::where('tingkat_risiko', 'tinggi')->count(),
            'total_checkin' => CheckIn::count(),
            'total_lokasi' => Lokasi::where('status', 'aktif')->count(),
        ];

        $chartLaporan = LaporanInspeksi::select(
            DB::raw('DATE_FORMAT(tanggal_inspeksi, "%Y-%m") as bulan'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->limit(6)
            ->get();

        $chartTemuan = Temuan::select('tingkat_risiko', DB::raw('COUNT(*) as total'))
            ->groupBy('tingkat_risiko')
            ->get();

        $recentLaporans = LaporanInspeksi::with(['user', 'lokasi'])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'chartLaporan', 'chartTemuan', 'recentLaporans'));
    }
}
