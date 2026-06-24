<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TemuanRequest;
use App\Models\Temuan;
use App\Services\AuditLogService;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TemuanExport; // <-- Import class export yang baru dibuat

class TemuanController extends Controller
{
    public function __construct(
        private AuditLogService $auditLog,
        private ImageUploadService $imageUpload
    ) {}

    public function index(Request $request)
    {
        $query = Temuan::with(['laporan.user', 'laporan.lokasi']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tingkat_risiko')) {
            $query->where('tingkat_risiko', $request->tingkat_risiko);
        }

        $temuans = $query->latest()->get();

        return view('admin.temuan.index', compact('temuans'));
    }

    public function show(Temuan $temuan)
    {
        $temuan->load(['laporan.lokasi', 'laporan.user', 'fotoTemuans', 'prosesTemuan', 'hasilTemuan']);

        return view('admin.temuan.show', compact('temuan'));
    }

    public function edit(Temuan $temuan)
    {
        return view('admin.temuan.edit', compact('temuan'));
    }

    public function update(TemuanRequest $request, Temuan $temuan)
    {
        $temuan->update($request->validated());

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $file) {
                $path = $this->imageUpload->storeSingle($file, 'temuan');
                $temuan->fotoTemuans()->create(['foto' => $path]);
            }
        }

        $this->auditLog->log('update', 'Mengubah temuan: ' . $temuan->kode_temuan, $temuan);

        return redirect()->route('admin.temuan.show', $temuan)->with('success', 'Temuan berhasil diperbarui.');
    }

    public function destroy(Temuan $temuan)
    {
        $kode = $temuan->kode_temuan;
        $temuan->delete();
        $this->auditLog->log('delete', 'Menghapus temuan: ' . $kode);

        return redirect()->route('admin.temuan.index')->with('success', 'Temuan berhasil dihapus.');
    }

    public function updateStatus(Request $request, Temuan $temuan)
    {
        $request->validate(['status' => 'required|in:menunggu_review,diproses,selesai,ditolak']);

        $temuan->update(['status' => $request->status]);
        $this->auditLog->log('update_status', 'Update status temuan: ' . $temuan->kode_temuan . ' -> ' . $request->status, $temuan);

        return back()->with('success', 'Status temuan berhasil diperbarui.');
    }

    public function exportExcel(Request $request)
    {
        // Mengambil data temuan beserta relasinya
        $temuans = Temuan::with(['laporan.lokasi', 'laporan.user'])->get();
        
        // Menentukan nama file download
        $namaFile = 'data-temuan-' . now()->format('Ymd') . '.xlsx';

        // Menjalankan download dengan syntax Laravel-Excel 3.x
        return Excel::download(new TemuanExport($temuans), $namaFile);
    }
}