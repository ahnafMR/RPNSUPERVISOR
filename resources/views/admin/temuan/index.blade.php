@extends('layouts.app')
@section('title', 'Kelola Temuan')
@section('page_title', 'Kelola Temuan')
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="mb-3">
    <a href="{{ route('admin.temuan.export-excel') }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
</div>
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <select name="status" class="form-control mr-2">
                <option value="">Semua Status</option>
                @foreach(['menunggu_review','diproses','selesai','ditolak'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                @endforeach
            </select>
            <select name="tingkat_risiko" class="form-control mr-2">
                <option value="">Semua Risiko</option>
                @foreach(['rendah','sedang','tinggi'] as $r)
                    <option value="{{ $r }}" {{ request('tingkat_risiko') == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
    <div class="card-body table-responsive">
        <table class="table table-bordered datatable">
            <thead><tr><th>Kode</th><th>Judul</th><th>Laporan</th><th>Lokasi</th><th>Risiko</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($temuans as $temuan)
                <tr>
                    <td>{{ $temuan->kode_temuan }}</td>
                    <td>{{ $temuan->judul_temuan }}</td>
                    <td>{{ $temuan->laporan->nomor_laporan ?? '-' }}</td>
                    <td>{{ $temuan->laporan->lokasi->nama_lokasi ?? '-' }}</td>
                    <td><span class="badge badge-{{ $temuan->tingkat_risiko === 'tinggi' ? 'danger' : ($temuan->tingkat_risiko === 'sedang' ? 'warning' : 'success') }}">{{ $temuan->risikoLabel() }}</span></td>
                    <td>{{ $temuan->statusLabel() }}</td>
                    <td>
                        <a href="{{ route('admin.temuan.show', $temuan) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.temuan.edit', $temuan) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
