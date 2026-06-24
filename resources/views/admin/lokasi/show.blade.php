@extends('layouts.app')
@section('title', 'Detail Lokasi')
@section('page_title', 'Detail Lokasi: ' . $lokasi->nama_lokasi)
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr><th>Kode</th><td>{{ $lokasi->kode_lokasi }}</td></tr>
                    <tr><th>Nama</th><td>{{ $lokasi->nama_lokasi }}</td></tr>
                    <tr><th>Latitude</th><td>{{ $lokasi->latitude }}</td></tr>
                    <tr><th>Longitude</th><td>{{ $lokasi->longitude }}</td></tr>
                    <tr><th>Radius</th><td>{{ $lokasi->radius_meter }} meter</td></tr>
                    <tr><th>Status</th><td>{{ ucfirst($lokasi->status) }}</td></tr>
                </table>
                <a href="{{ route('admin.lokasi.edit', $lokasi) }}" class="btn btn-warning">Edit</a>
                <a href="{{ route('admin.lokasi.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card"><div class="card-body"><div id="map" style="height: 350px;"></div></div></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const map = L.map('map').setView([{{ $lokasi->latitude }}, {{ $lokasi->longitude }}], 16);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([{{ $lokasi->latitude }}, {{ $lokasi->longitude }}]).addTo(map);
L.circle([{{ $lokasi->latitude }}, {{ $lokasi->longitude }}], { radius: {{ $lokasi->radius_meter }} }).addTo(map);
</script>
@endpush
