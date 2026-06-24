@extends('layouts.app')

@section('title', 'Kelola Lokasi')
@section('page_title', 'Kelola Lokasi')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.lokasi.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Lokasi</a>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered datatable">
            <thead>
                <tr><th>Kode</th><th>Nama</th><th>Latitude</th><th>Longitude</th><th>Radius (m)</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @foreach($lokasis as $lokasi)
                <tr>
                    <td>{{ $lokasi->kode_lokasi }}</td>
                    <td>{{ $lokasi->nama_lokasi }}</td>
                    <td>{{ $lokasi->latitude }}</td>
                    <td>{{ $lokasi->longitude }}</td>
                    <td>{{ $lokasi->radius_meter }}</td>
                    <td><span class="badge badge-{{ $lokasi->status === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($lokasi->status) }}</span></td>
                    <td>
                        <a href="{{ route('admin.lokasi.show', $lokasi) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.lokasi.edit', $lokasi) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.lokasi.destroy', $lokasi) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus lokasi ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
