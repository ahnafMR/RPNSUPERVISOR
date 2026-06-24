@extends('layouts.app')
@section('title', 'Edit Temuan')
@section('page_title', 'Edit Temuan')
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="card">
    <form action="{{ route('admin.temuan.update', $temuan) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="form-group"><label>Judul</label><input type="text" name="judul_temuan" class="form-control" value="{{ old('judul_temuan', $temuan->judul_temuan) }}" required></div>
            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" required>{{ old('deskripsi', $temuan->deskripsi) }}</textarea></div>
            <div class="form-group">
                <label>Tingkat Risiko</label>
                <select name="tingkat_risiko" class="form-control">
                    @foreach(['rendah','sedang','tinggi'] as $r)
                        <option value="{{ $r }}" {{ old('tingkat_risiko', $temuan->tingkat_risiko) === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Rekomendasi</label><textarea name="rekomendasi" class="form-control" rows="2">{{ old('rekomendasi', $temuan->rekomendasi) }}</textarea></div>
            <div class="form-group"><label>Tambah Foto</label><input type="file" name="foto[]" class="form-control" multiple accept="image/*"></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.temuan.show', $temuan) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
