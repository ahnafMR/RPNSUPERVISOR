<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\LaporanInspeksi;
use App\Models\Temuan;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $activeCheckin = $user->activeCheckin();

        $stats = [
            'total_laporan' => LaporanInspeksi::where('user_id', $user->id)->count(),
            'menunggu_review' => LaporanInspeksi::where('user_id', $user->id)->where('status', 'menunggu_review')->count(),
            'diproses' => LaporanInspeksi::where('user_id', $user->id)->where('status', 'diproses')->count(),
            'selesai' => LaporanInspeksi::where('user_id', $user->id)->where('status', 'selesai')->count(),
            'total_temuan' => Temuan::whereHas('laporan', fn ($q) => $q->where('user_id', $user->id))->count(),
        ];

        $recentLaporans = LaporanInspeksi::with('lokasi')
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('supervisor.dashboard', compact('stats', 'recentLaporans', 'activeCheckin'));
    }
}
