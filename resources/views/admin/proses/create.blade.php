@extends('layouts.app')
@section('title', 'Proses Temuan')
@section('page_title', 'Proses Temuan: ' . $temuan->kode_temuan)
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="card">
    <form action="{{ route('admin.proses.store', $temuan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-group"><label>Tanggal Proses</label><input type="date" name="tanggal_proses" class="form-control" value="{{ old('tanggal_proses', date('Y-m-d')) }}" required></div>
            <div class="form-group"><label>PIC</label><input type="text" name="pic" class="form-control" value="{{ old('pic') }}" required></div>
            <div class="form-group"><label>Tindakan Perbaikan</label><textarea name="tindakan" class="form-control" rows="3" required>{{ old('tindakan') }}</textarea></div>
            <div class="form-group"><label>Catatan</label><textarea name="catatan" class="form-control" rows="2">{{ old('catatan') }}</textarea></div>
            <div class="form-group"><label>Foto Proses</label><input type="file" name="foto_proses" class="form-control" accept="image/*"></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan Proses</button>
            <a href="{{ route('admin.temuan.show', $temuan) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
