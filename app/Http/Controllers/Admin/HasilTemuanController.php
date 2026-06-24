<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HasilTemuanRequest;
use App\Models\Temuan;
use App\Services\AuditLogService;
use App\Services\ImageUploadService;

class HasilTemuanController extends Controller
{
    public function __construct(
        private AuditLogService $auditLog,
        private ImageUploadService $imageUpload
    ) {}

    public function create(Temuan $temuan)
    {
        return view('admin.hasil.create', compact('temuan'));
    }

    public function store(HasilTemuanRequest $request, Temuan $temuan)
    {
        $data = $request->validated();

        if ($request->hasFile('foto_hasil')) {
            $data['foto_hasil'] = $this->imageUpload->storeSingle($request->file('foto_hasil'), 'hasil');
        }

        $temuan->hasilTemuan()->updateOrCreate(['temuan_id' => $temuan->id], $data);
        $temuan->update(['status' => 'selesai']);

        $this->auditLog->log('hasil', 'Menyelesaikan temuan: ' . $temuan->kode_temuan, $temuan);

        return redirect()->route('admin.temuan.show', $temuan)->with('success', 'Hasil temuan berhasil disimpan.');
    }
}
