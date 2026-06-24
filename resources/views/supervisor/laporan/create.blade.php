@extends('layouts.app')
@section('title', 'Buat Laporan')
@section('page_title', 'Buat Laporan Inspeksi')
@section('sidebar')@include('supervisor.partials.sidebar')@endsection
@section('content')
<div class="alert alert-info">
    Check-in aktif: <strong>{{ $activeCheckin->lokasi->nama_lokasi }}</strong>
</div>
<div class="card">
    <form action="{{ route('supervisor.laporan.store') }}" method="POST" enctype="multipart/form-data" id="laporanForm">
        @csrf
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Inspeksi</label>
                        <input type="date" name="tanggal_inspeksi" class="form-control" value="{{ old('tanggal_inspeksi', date('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Area Inspeksi</label>
                        <input type="text" name="area" class="form-control" value="{{ old('area') }}" required placeholder="Contoh: Area Gudang Utara">
                    </div>
                    <div class="form-group">
                        <label>Kategori Inspeksi</label>
                        <select name="kategori" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            @foreach(['Keselamatan','Kebersihan','Peralatan','Lingkungan','Lainnya'] as $kat)
                                <option value="{{ $kat }}" {{ old('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Deskripsi Inspeksi</label>
                        <textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Foto Dokumentasi (Multiple)</label>
                        <input type="file" name="foto[]" id="fotoInput" class="form-control" multiple accept="image/*">
                        <div id="fotoPreview" class="row mt-2"></div>
                    </div>
                </div>
            </div>
            <div id="gpsStatus" class="alert alert-secondary mt-2"><i class="fas fa-satellite-dish"></i> Memvalidasi GPS...</div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
        </div>
        <div class="card-footer">
            <button type="submit" id="btnSubmit" class="btn btn-primary" disabled><i class="fas fa-save"></i> Simpan Laporan</button>
            <a href="{{ route('supervisor.laporan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
@push('scripts')
<script>
const lokasiLat = {{ $activeCheckin->lokasi->latitude }};
const lokasiLng = {{ $activeCheckin->lokasi->longitude }};
const radius = {{ $activeCheckin->lokasi->radius_meter }};

document.getElementById('fotoInput').addEventListener('change', function(e) {
    const preview = document.getElementById('fotoPreview');
    preview.innerHTML = '';
    Array.from(e.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.innerHTML += '<div class="col-12 col-md-3"><img src="'+ev.target.result+'" class="img-fluid rounded mb-2"></div>';
        };
        reader.readAsDataURL(file);
    });
});

function haversine(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = (lat2-lat1) * Math.PI/180;
    const dLon = (lon2-lon1) * Math.PI/180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}

if (navigator.geolocation) {
    navigator.geolocation.watchPosition(function(pos) {
        const lat = pos.coords.latitude, lng = pos.coords.longitude;
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;
        const dist = haversine(lat, lng, lokasiLat, lokasiLng);
        const el = document.getElementById('gpsStatus');
        if (dist <= radius) {
            el.className = 'alert alert-success mt-2';
            el.innerHTML = '<i class="fas fa-check"></i> GPS valid. Jarak: ' + dist.toFixed(0) + 'm';
            document.getElementById('btnSubmit').disabled = false;
        } else {
            el.className = 'alert alert-danger mt-2';
            el.innerHTML = '<i class="fas fa-times"></i> Anda berada di luar area inspeksi yang diizinkan. Jarak: ' + dist.toFixed(0) + 'm';
            document.getElementById('btnSubmit').disabled = true;
        }
    }, function(err) {
        document.getElementById('gpsStatus').innerHTML = 'GPS error: ' + err.message;
    }, { enableHighAccuracy: true });
}
</script>
@endpush
