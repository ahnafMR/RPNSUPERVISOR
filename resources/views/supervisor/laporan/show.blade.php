@extends('layouts.app')
@section('title', 'Detail Laporan')
@section('page_title', 'Laporan: ' . $laporan->nomor_laporan)
@section('sidebar')@include('supervisor.partials.sidebar')@endsection
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <tr><th>No. Laporan</th><td>{{ $laporan->nomor_laporan }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $laporan->lokasi->nama_lokasi }}</td></tr>
                    <tr><th>Tanggal</th><td>{{ $laporan->tanggal_inspeksi->format('d/m/Y') }}</td></tr>
                    <tr><th>Area</th><td>{{ $laporan->area }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $laporan->kategori }}</td></tr>
                    <tr><th>Deskripsi</th><td>{{ $laporan->deskripsi }}</td></tr>
                    <tr><th>Status</th><td><span class="badge badge-info">{{ $laporan->statusLabel() }}</span></td></tr>
                    @if($laporan->catatan_approval)<tr><th>Catatan Admin</th><td>{{ $laporan->catatan_approval }}</td></tr>@endif
                </table>
                @if($laporan->fotoLaporans->count())
                <h5>Foto Dokumentasi</h5>
                <div class="row">@foreach($laporan->fotoLaporans as $f)<div class="col-12 col-md-3"><img src="{{ asset('storage/'.$f->foto) }}" class="img-fluid rounded mb-2"></div>@endforeach</div>
                @endif
            </div>
            @if(auth()->user()->activeCheckin())
            <div class="card-footer">
                <a href="{{ route('supervisor.temuan.create', $laporan) }}" class="btn btn-warning"><i class="fas fa-plus"></i> Tambah Temuan</a>
            </div>
            @endif
        </div>
        <div class="card">
            <div class="card-header"><h3 class="card-title">Daftar Temuan</h3></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Kode</th><th>Judul</th><th>Risiko</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse($laporan->temuans as $temuan)
                        <tr>
                            <td>{{ $temuan->kode_temuan }}</td>
                            <td>{{ $temuan->judul_temuan }}</td>
                            <td>{{ $temuan->risikoLabel() }}</td>
                            <td>{{ $temuan->statusLabel() }}</td>
                            <td><a href="{{ route('supervisor.temuan.show', $temuan) }}" class="btn btn-xs btn-info">Detail</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Belum ada temuan</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="{{ route('supervisor.laporan.index') }}" class="btn btn-secondary btn-block">Kembali</a>
    </div>
</div>
@endsection
