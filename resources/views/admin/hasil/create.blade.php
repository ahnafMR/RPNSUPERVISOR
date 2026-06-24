@extends('layouts.app')
@section('title', 'Hasil Temuan')
@section('page_title', 'Hasil Temuan: ' . $temuan->kode_temuan)
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="card">
    <form action="{{ route('admin.hasil.store', $temuan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-group"><label>Tanggal Selesai</label><input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required></div>
            <div class="form-group"><label>Hasil Perbaikan</label><textarea name="hasil_perbaikan" class="form-control" rows="3" required>{{ old('hasil_perbaikan') }}</textarea></div>
            <div class="form-group"><label>Catatan Akhir</label><textarea name="catatan_akhir" class="form-control" rows="2">{{ old('catatan_akhir') }}</textarea></div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="selesai">Selesai</option>
                    <option value="belum_selesai">Belum Selesai</option>
                </select>
            </div>
            <div class="form-group"><label>Foto Hasil</label><input type="file" name="foto_hasil" class="form-control" accept="image/*"></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success">Simpan Hasil</button>
            <a href="{{ route('admin.temuan.show', $temuan) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
