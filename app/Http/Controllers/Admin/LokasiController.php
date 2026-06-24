<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LokasiRequest;
use App\Models\Lokasi;
use App\Services\AuditLogService;

class LokasiController extends Controller
{
    public function __construct(private AuditLogService $auditLog) {}

    public function index()
    {
        $lokasis = Lokasi::latest()->get();

        return view('admin.lokasi.index', compact('lokasis'));
    }

    public function create()
    {
        return view('admin.lokasi.create');
    }

    public function store(LokasiRequest $request)
    {
        $lokasi = Lokasi::create($request->validated());
        $this->auditLog->log('create', 'Menambah lokasi: ' . $lokasi->nama_lokasi, $lokasi);

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function show(Lokasi $lokasi)
    {
        $lokasi->load(['checkins.user', 'laporans']);

        return view('admin.lokasi.show', compact('lokasi'));
    }

    public function edit(Lokasi $lokasi)
    {
        return view('admin.lokasi.edit', compact('lokasi'));
    }

    public function update(LokasiRequest $request, Lokasi $lokasi)
    {
        $lokasi->update($request->validated());
        $this->auditLog->log('update', 'Mengubah lokasi: ' . $lokasi->nama_lokasi, $lokasi);

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Lokasi $lokasi)
    {
        $nama = $lokasi->nama_lokasi;
        $lokasi->delete();
        $this->auditLog->log('delete', 'Menghapus lokasi: ' . $nama);

        return redirect()->route('admin.lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
