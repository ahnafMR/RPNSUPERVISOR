@extends('layouts.app')
@section('title', 'Detail Laporan')
@section('page_title', 'Laporan: ' . $laporan->nomor_laporan)
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>No. Laporan</th><td>{{ $laporan->nomor_laporan }}</td></tr>
                    <tr><th>Supervisor</th><td>{{ $laporan->user->name }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $laporan->lokasi->nama_lokasi }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $laporan->tanggal_inspeksi->format('d/m/Y') }}</td></tr>
                    <tr><th>Area</th><td>{{ $laporan->area }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $laporan->kategori }}</td></tr>
                    <tr><th>Deskripsi</th><td>{{ $laporan->deskripsi }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-info">{{ $laporan->statusLabel() }}</span></td></tr>
                    @if($laporan->catatan_approval)
                    <tr><th>Catatan Approval</th><td>{{ $laporan->catatan_approval }}</td></tr>
                    @endif
                </table>
                @if($laporan->fotoLaporans->count())
                <h5>Foto Dokumentasi</h5>
                <div class="row">
                    @foreach($laporan->fotoLaporans as $foto)
                        <div class="col-12 col-md-3 mb-2"><img src="{{ asset('storage/' . $foto->foto) }}" class="img-fluid rounded"></div>
                    @endforeach
                </div>
                @endif
            </div>
            @if($laporan->status === 'menunggu_review')
            <div class="card-footer">
                <form action="{{ route('admin.laporan.approve', $laporan) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="text" name="catatan_approval" class="form-control mb-2" placeholder="Catatan (opsional)">
                    <button type="submit" class="btn btn-success" onclick="return confirm('Setujui laporan ini?')"><i class="fas fa-check"></i> Setujui</button>
                </form>
                <form action="{{ route('admin.laporan.reject', $laporan) }}" method="POST" class="d-inline ml-2">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak laporan ini?')"><i class="fas fa-times"></i> Tolak</button>
                </form>
            </div>
            @endif
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Temuan ({{ $laporan->temuans->count() }})</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Kode</th><th>Judul</th><th>Risiko</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @foreach($laporan->temuans as $temuan)
                        <tr>
                            <td>{{ $temuan->kode_temuan }}</td>
                            <td>{{ $temuan->judul_temuan }}</td>
                            <td><span class="badge badge-{{ $temuan->tingkat_risiko === 'tinggi' ? 'danger' : ($temuan->tingkat_risiko === 'sedang' ? 'warning' : 'success') }}">{{ $temuan->risikoLabel() }}</span></td>
                            <td>{{ $temuan->statusLabel() }}</td>
                            <td><a href="{{ route('admin.temuan.show', $temuan) }}" class="btn btn-xs btn-info">Detail</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <a href="{{ route('admin.laporan.export-pdf', $laporan) }}" class="btn btn-danger btn-block mb-2"><i class="fas fa-file-pdf"></i> Export PDF</a>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary btn-block">Kembali</a>
            </div>
        </div>
    </div>
</div>
@endsection
