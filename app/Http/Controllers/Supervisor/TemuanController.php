<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Http\Requests\TemuanRequest;
use App\Models\LaporanInspeksi;
use App\Models\Temuan;
use App\Services\AuditLogService;
use App\Services\GpsService;
use App\Services\ImageUploadService;
use App\Services\NumberGeneratorService;
use Illuminate\Support\Facades\Auth;

class TemuanController extends Controller
{
    public function __construct(
        private GpsService $gpsService,
        private ImageUploadService $imageUpload,
        private NumberGeneratorService $numberGenerator,
        private AuditLogService $auditLog
    ) {}

    public function create(LaporanInspeksi $laporan)
    {
        $this->authorizeLaporan($laporan);
        $activeCheckin = Auth::user()->activeCheckin();

        if (! $activeCheckin) {
            return redirect()->route('supervisor.checkin.index')
                ->with('error', 'Anda harus melakukan check-in terlebih dahulu.');
        }

        return view('supervisor.temuan.create', compact('laporan', 'activeCheckin'));
    }

    public function store(TemuanRequest $request, LaporanInspeksi $laporan)
    {
        $this->authorizeLaporan($laporan);
        $activeCheckin = Auth::user()->activeCheckin();

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

        $temuan = Temuan::create([
            'laporan_id' => $laporan->id,
            'kode_temuan' => $this->numberGenerator->generateKodeTemuan(),
            'judul_temuan' => $request->judul_temuan,
            'deskripsi' => $request->deskripsi,
            'tingkat_risiko' => $request->tingkat_risiko,
            'rekomendasi' => $request->rekomendasi,
            'status' => 'menunggu_review',
        ]);

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $this->imageUpload->storeSingle($file, 'temuan');
                $temuan->fotoTemuans()->create(['foto' => $path]);
            }
        }

        $this->auditLog->log('create', 'Menambah temuan: ' . $temuan->kode_temuan, $temuan);

        return redirect()->route('supervisor.laporan.show', $laporan)
            ->with('success', 'Temuan berhasil ditambahkan.');
    }

    public function show(Temuan $temuan)
    {
        $this->authorizeLaporan($temuan->laporan);
        $temuan->load(['laporan.lokasi', 'fotoTemuans', 'prosesTemuan', 'hasilTemuan']);

        return view('supervisor.temuan.show', compact('temuan'));
    }

    private function authorizeLaporan(LaporanInspeksi $laporan): void
    {
        if ($laporan->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
