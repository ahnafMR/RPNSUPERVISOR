@extends('layouts.app')
@section('title', 'Monitoring Peta')
@section('page_title', 'Monitoring Peta')
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="card">
    <div class="card-header">
        <div class="btn-group">
            <button class="btn btn-sm btn-primary" onclick="toggleLayer('lokasi')">Lokasi</button>
            <button class="btn btn-sm btn-success" onclick="toggleLayer('checkin')">Check-In</button>
            <button class="btn btn-sm btn-info" onclick="toggleLayer('laporan')">Laporan</button>
            <button class="btn btn-sm btn-warning" onclick="toggleLayer('temuan')">Temuan</button>
        </div>
    </div>
    <div class="card-body p-0"><div id="map" style="height: 600px;"></div></div>
</div>
@endsection
@push('scripts')
<script>
const map = L.map('map').setView([-6.2, 106.816666], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: 'OpenStreetMap' }).addTo(map);
const layers = { lokasi: L.layerGroup().addTo(map), checkin: L.layerGroup().addTo(map), laporan: L.layerGroup().addTo(map), temuan: L.layerGroup().addTo(map) };
@foreach($lokasis as $l)
layers.lokasi.addLayer(L.circle([{{ $l->latitude }}, {{ $l->longitude }}], {radius: {{ $l->radius_meter }}, color: 'blue'}).bindPopup('<b>{{ $l->nama_lokasi }}</b><br>Radius: {{ $l->radius_meter }}m'));
layers.lokasi.addLayer(L.marker([{{ $l->latitude }}, {{ $l->longitude }}]).bindPopup('{{ $l->nama_lokasi }}'));
@endforeach
@foreach($checkins as $c)
layers.checkin.addLayer(L.marker([{{ $c->latitude }}, {{ $c->longitude }}], {icon: L.divIcon({className:'',html:'<i class="fas fa-camera text-success"></i>'})}).bindPopup('Check-In: {{ $c->user->name }}<br>{{ $c->lokasi->nama_lokasi }}'));
@endforeach
@foreach($laporans as $lp)
layers.laporan.addLayer(L.marker([{{ $lp->lokasi->latitude }}, {{ $lp->lokasi->longitude }}], {icon: L.divIcon({className:'',html:'<i class="fas fa-file-alt text-info"></i>'})}).bindPopup('Laporan: {{ $lp->nomor_laporan }}'));
@endforeach
@foreach($temuans as $t)
@if($t->laporan && $t->laporan->lokasi)
layers.temuan.addLayer(L.marker([{{ $t->laporan->lokasi->latitude + 0.0001 }}, {{ $t->laporan->lokasi->longitude + 0.0001 }}], {icon: L.divIcon({className:'',html:'<i class="fas fa-exclamation-triangle text-warning"></i>'})}).bindPopup('Temuan: {{ $t->kode_temuan }}<br>{{ $t->judul_temuan }}'));
@endif
@endforeach
function toggleLayer(name) { map.hasLayer(layers[name]) ? map.removeLayer(layers[name]) : map.addLayer(layers[name]); }
</script>
@endpush
