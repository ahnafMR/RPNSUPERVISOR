<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProsesTemuanRequest;
use App\Models\Temuan;
use App\Services\AuditLogService;
use App\Services\ImageUploadService;

class ProsesTemuanController extends Controller
{
    public function __construct(
        private AuditLogService $auditLog,
        private ImageUploadService $imageUpload
    ) {}

    public function create(Temuan $temuan)
    {
        return view('admin.proses.create', compact('temuan'));
    }

    public function store(ProsesTemuanRequest $request, Temuan $temuan)
    {
        $data = $request->validated();

        if ($request->hasFile('foto_proses')) {
            $data['foto_proses'] = $this->imageUpload->storeSingle($request->file('foto_proses'), 'proses');
        }

        $temuan->prosesTemuan()->updateOrCreate(['temuan_id' => $temuan->id], $data);
        $temuan->update(['status' => 'diproses']);

        $this->auditLog->log('proses', 'Memproses temuan: ' . $temuan->kode_temuan, $temuan);

        return redirect()->route('admin.temuan.show', $temuan)->with('success', 'Proses temuan berhasil disimpan.');
    }
}
