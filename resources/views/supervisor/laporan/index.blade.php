@extends('layouts.app')
@section('title', 'Laporan Inspeksi')
@section('page_title', 'Daftar Laporan')
@section('sidebar')@include('supervisor.partials.sidebar')@endsection
@section('content')
<div class="mb-3">
    <a href="{{ route('supervisor.laporan.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Buat Laporan</a>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered datatable">
            <thead><tr><th>No. Laporan</th><th>Lokasi</th><th>Area</th><th>Tanggal</th><th>Status</th><th>Temuan</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($laporans as $laporan)
                <tr>
                    <td>{{ $laporan->nomor_laporan }}</td>
                    <td>{{ $laporan->lokasi->nama_lokasi }}</td>
                    <td>{{ $laporan->area }}</td>
                    <td>{{ $laporan->tanggal_inspeksi->format('d/m/Y') }}</td>
                    <td>{{ $laporan->statusLabel() }}</td>
                    <td>{{ $laporan->temuans->count() }}</td>
                    <td><a href="{{ route('supervisor.laporan.show', $laporan) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
