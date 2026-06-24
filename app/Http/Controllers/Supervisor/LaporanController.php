<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanRequest;
use App\Models\LaporanInspeksi;
use App\Services\AuditLogService;
use App\Services\GpsService;
use App\Services\ImageUploadService;
use App\Services\NumberGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function __construct(
        private GpsService $gpsService,
        private ImageUploadService $imageUpload,
        private NumberGeneratorService $numberGenerator,
        private AuditLogService $auditLog
    ) {}

    public function index()
    {
        $laporans = LaporanInspeksi::with(['lokasi', 'temuans'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('supervisor.laporan.index', compact('laporans'));
    }

    public function create(Request $request)
    {
        $activeCheckin = $request->attributes->get('active_checkin') ?? Auth::user()->activeCheckin();

        return view('supervisor.laporan.create', compact('activeCheckin'));
    }

    public function store(LaporanRequest $request)
    {
        $user = Auth::user();
        $activeCheckin = $user->activeCheckin();

        if (! $activeCheckin) {
            return redirect()->route('supervisor.checkin.index')
                ->with('error', 'Anda harus melakukan check-in terlebih dahulu.');
        }

        $lokasi = $activeCheckin->lokasi;

        if (! $this->gpsService->isWithinRadius(
            (float) $request->latitude,
            (float) $request->longitude,
            (float) $lokasi->latitude,
            (float) $lokasi->longitude,
            $lokasi->radius_meter
        )) {
            return back()->with('error', 'Anda berada di luar area inspeksi yang diizinkan.')->withInput();
        }

        $laporan = LaporanInspeksi::create([
            'nomor_laporan' => $this->numberGenerator->generateNomorLaporan(),
            'user_id' => $user->id,
            'lokasi_id' => $activeCheckin->lokasi_id,
            'checkin_id' => $activeCheckin->id,
            'tanggal_inspeksi' => $request->tanggal_inspeksi,
            'area' => $request->area,
            'kategori' => $request->kategori,
            'deskripsi' => $request->deskripsi,
            'status' => 'menunggu_review',
        ]);

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $this->imageUpload->storeSingle($file, 'laporan');
                $laporan->fotoLaporans()->create(['foto' => $path]);
            }
        }

        $this->auditLog->log('create', 'Membuat laporan: ' . $laporan->nomor_laporan, $laporan);

        return redirect()->route('supervisor.laporan.show', $laporan)
            ->with('success', 'Laporan inspeksi berhasil dibuat.');
    }

    public function show(LaporanInspeksi $laporan)
    {
        $this->authorizeLaporan($laporan);
        $laporan->load(['lokasi', 'checkin', 'fotoLaporans', 'temuans.fotoTemuans', 'temuans.prosesTemuan', 'temuans.hasilTemuan']);

        return view('supervisor.laporan.show', compact('laporan'));
    }

    private function authorizeLaporan(LaporanInspeksi $laporan): void
    {
        if ($laporan->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
