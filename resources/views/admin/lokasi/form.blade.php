@extends('layouts.app')
@section('title', $lokasi->exists ? 'Edit Lokasi' : 'Tambah Lokasi')
@section('page_title', $lokasi->exists ? 'Edit Lokasi' : 'Tambah Lokasi')
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="card">
    <form action="{{ $lokasi->exists ? route('admin.lokasi.update', $lokasi) : route('admin.lokasi.store') }}" method="POST">
        @csrf
        @if($lokasi->exists) @method('PUT') @endif
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Lokasi</label>
                        <input type="text" name="kode_lokasi" class="form-control" value="{{ old('kode_lokasi', $lokasi->kode_lokasi) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lokasi</label>
                        <input type="text" name="nama_lokasi" class="form-control" value="{{ old('nama_lokasi', $lokasi->nama_lokasi) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="aktif" {{ old('status', $lokasi->status) === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ old('status', $lokasi->status) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" class="form-control" value="{{ old('latitude', $lokasi->latitude) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" class="form-control" value="{{ old('longitude', $lokasi->longitude) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Radius Toleransi (meter)</label>
                        <input type="number" name="radius_meter" class="form-control" value="{{ old('radius_meter', $lokasi->radius_meter ?? 100) }}" required>
                    </div>
                </div>
            </div>
            <div id="map" style="height: 300px;"></div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.lokasi.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
const lat = parseFloat(document.getElementById('latitude').value) || -6.2;
const lng = parseFloat(document.getElementById('longitude').value) || 106.816666;
const map = L.map('map').setView([lat, lng], 15);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let marker = L.marker([lat, lng], {draggable: true}).addTo(map);
marker.on('dragend', function(e) {
    document.getElementById('latitude').value = e.target.getLatLng().lat.toFixed(8);
    document.getElementById('longitude').value = e.target.getLatLng().lng.toFixed(8);
});
map.on('click', function(e) {
    marker.setLatLng(e.latlng);
    document.getElementById('latitude').value = e.latlng.lat.toFixed(8);
    document.getElementById('longitude').value = e.latlng.lng.toFixed(8);
});
</script>
@endpush
