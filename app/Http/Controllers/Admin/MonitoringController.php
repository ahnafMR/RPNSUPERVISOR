<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckIn;
use App\Models\LaporanInspeksi;
use App\Models\Lokasi;
use App\Models\Temuan;

class MonitoringController extends Controller
{
    public function index()
    {
        $lokasis = Lokasi::where('status', 'aktif')->get();
        $checkins = CheckIn::with(['user', 'lokasi'])->latest('waktu_checkin')->limit(50)->get();
        $laporans = LaporanInspeksi::with(['user', 'lokasi'])->latest()->limit(50)->get();
        $temuans = Temuan::with(['laporan.lokasi'])->latest()->limit(50)->get();

        return view('admin.monitoring.index', compact('lokasis', 'checkins', 'laporans', 'temuans'));
    }
}
