@extends('layouts.app')
@section('title', 'Tambah Temuan')
@section('page_title', 'Tambah Temuan')
@section('sidebar')@include('supervisor.partials.sidebar')@endsection
@section('content')
<div class="card">
    <form action="{{ route('supervisor.temuan.store', $laporan) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-group"><label>Judul Temuan</label><input type="text" name="judul_temuan" class="form-control" value="{{ old('judul_temuan') }}" required></div>
            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" class="form-control" rows="3" required>{{ old('deskripsi') }}</textarea></div>
            <div class="form-group">
                <label>Tingkat Risiko</label>
                <select name="tingkat_risiko" class="form-control" required>
                    <option value="rendah">Rendah</option>
                    <option value="sedang">Sedang</option>
                    <option value="tinggi">Tinggi</option>
                </select>
            </div>
            <div class="form-group"><label>Rekomendasi Perbaikan</label><textarea name="rekomendasi" class="form-control" rows="2">{{ old('rekomendasi') }}</textarea></div>
            <div class="form-group">
                <label>Foto Temuan (Multiple)</label>
                <input type="file" name="foto[]" id="fotoInput" class="form-control" multiple accept="image/*">
                <div id="fotoPreview" class="row mt-2"></div>
            </div>
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <div id="gpsStatus" class="alert alert-secondary"><i class="fas fa-satellite-dish"></i> Memvalidasi GPS...</div>
        </div>
        <div class="card-footer">
            <button type="submit" id="btnSubmit" class="btn btn-primary" disabled>Simpan Temuan</button>
            <a href="{{ route('supervisor.laporan.show', $laporan) }}" class="btn btn-secondary">Batal</a>
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
        reader.onload = ev => preview.innerHTML += '<div class="col-12 col-md-3"><img src="'+ev.target.result+'" class="img-fluid rounded mb-2"></div>';
        reader.readAsDataURL(file);
    });
});
if (navigator.geolocation) {
    navigator.geolocation.watchPosition(function(pos) {
        document.getElementById('latitude').value = pos.coords.latitude;
        document.getElementById('longitude').value = pos.coords.longitude;
        const R = 6371000, dLat = (lokasiLat-pos.coords.latitude)*Math.PI/180, dLon = (lokasiLng-pos.coords.longitude)*Math.PI/180;
        const a = Math.sin(dLat/2)**2+Math.cos(pos.coords.latitude*Math.PI/180)*Math.cos(lokasiLat*Math.PI/180)*Math.sin(dLon/2)**2;
        const dist = R*2*Math.atan2(Math.sqrt(a),Math.sqrt(1-a));
        const el = document.getElementById('gpsStatus');
        if (dist <= radius) { el.className='alert alert-success'; el.innerHTML='<i class="fas fa-check"></i> GPS valid'; document.getElementById('btnSubmit').disabled=false; }
        else { el.className='alert alert-danger'; el.innerHTML='<i class="fas fa-times"></i> Di luar area'; document.getElementById('btnSubmit').disabled=true; }
    }, null, { enableHighAccuracy: true });
}
</script>
@endpush
