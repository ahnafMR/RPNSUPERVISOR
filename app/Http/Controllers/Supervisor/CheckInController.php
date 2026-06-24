<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckInRequest;
use App\Models\CheckIn;
use App\Models\Lokasi;
use App\Services\AuditLogService;
use App\Services\GpsService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckInController extends Controller
{
    public function __construct(
        private GpsService $gpsService,
        private ImageUploadService $imageUpload,
        private AuditLogService $auditLog
    ) {}

    public function index()
    {
        $activeCheckin = Auth::user()->activeCheckin();
        $lokasis = Lokasi::where('status', 'aktif')->get();
        $history = CheckIn::with('lokasi')
            ->where('user_id', Auth::id())
            ->latest('waktu_checkin')
            ->limit(10)
            ->get();

        return view('supervisor.checkin.index', compact('activeCheckin', 'lokasis', 'history'));
    }

    public function store(CheckInRequest $request)
    {
        $user = Auth::user();

        if ($user->activeCheckin()) {
            return back()->with('error', 'Anda masih memiliki check-in aktif. Lakukan check-out terlebih dahulu.');
        }

        $lokasi = Lokasi::findOrFail($request->lokasi_id);

        if (! $this->gpsService->isWithinRadius(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $lokasi->latitude,
            (float) $lokasi->longitude,
            $lokasi->radius_meter
        )) {
            return back()->with('error', 'Anda berada di luar area inspeksi yang diizinkan.');
        }

        $fotoPath = $this->imageUpload->storeBase64($request->foto_selfie, 'selfie');

        $checkin = CheckIn::create([
            'user_id' => $user->id,
            'lokasi_id' => $lokasi->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'foto_selfie' => $fotoPath,
            'waktu_checkin' => now(),
            'status' => 'aktif',
        ]);

        $this->auditLog->log('checkin', 'Check-in di lokasi: ' . $lokasi->nama_lokasi, $checkin);

        return redirect()->route('supervisor.dashboard')->with('success', 'Check-in berhasil! Anda dapat membuat laporan inspeksi.');
    }

    public function checkout(Request $request)
    {
        $checkin = Auth::user()->activeCheckin();

        if (! $checkin) {
            return back()->with('error', 'Tidak ada check-in aktif.');
        }

        $checkin->update([
            'waktu_checkout' => now(),
            'status' => 'tidak_aktif',
        ]);

        $this->auditLog->log('checkout', 'Check-out dari lokasi: ' . $checkin->lokasi->nama_lokasi, $checkin);

        return redirect()->route('supervisor.checkin.index')->with('success', 'Check-out berhasil.');
    }

    public function validateGps(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'lokasi_id' => 'required|exists:lokasi,id',
        ]);

        $lokasi = Lokasi::findOrFail($request->lokasi_id);

        $within = $this->gpsService->isWithinRadius(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $lokasi->latitude,
            (float) $lokasi->longitude,
            $lokasi->radius_meter
        );

        $distance = $this->gpsService->calculateDistance(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $lokasi->latitude,
            (float) $lokasi->longitude
        );

        return response()->json([
            'valid' => $within,
            'distance' => round($distance, 2),
            'radius' => $lokasi->radius_meter,
            'message' => $within ? 'Lokasi valid.' : 'Anda berada di luar area inspeksi yang diizinkan.',
        ]);
    }
}
