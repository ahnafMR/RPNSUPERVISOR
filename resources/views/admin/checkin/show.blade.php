@extends('layouts.app')
@section('title', 'Detail Check-In')
@section('page_title', 'Detail Check-In')
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Foto Selfie</h3></div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $checkin->foto_selfie) }}" class="img-fluid rounded" alt="Selfie">
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <tr><th>Supervisor</th><td>{{ $checkin->user->name }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $checkin->lokasi->nama_lokasi }}</td></tr>
                    <tr><th>Latitude</th><td>{{ $checkin->latitude }}</td></tr>
                    <tr><th>Longitude</th><td>{{ $checkin->longitude }}</td></tr>
                    <tr><th>Waktu Check-In</th><td>{{ $checkin->waktu_checkin->format('d/m/Y H:i:s') }}</td></tr>
                    <tr><th>Waktu Check-Out</th><td>{{ $checkin->waktu_checkout?->format('d/m/Y H:i:s') ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ ucfirst($checkin->status) }}</td></tr>
                    <tr><th>Jumlah Laporan</th><td>{{ $checkin->laporans->count() }}</td></tr>
                </table>
            </div>
        </div>
        <div class="card"><div class="card-body"><div id="map" style="height: 300px;"></div></div></div>
    </div>
</div>
@endsection
@push('scripts')
<script>
const map = L.map('map').setView([{{ $checkin->latitude }}, {{ $checkin->longitude }}], 17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
L.marker([{{ $checkin->latitude }}, {{ $checkin->longitude }}]).addTo(map).bindPopup('Check-In: {{ $checkin->user->name }}');
L.circle([{{ $checkin->lokasi->latitude }}, {{ $checkin->lokasi->longitude }}], { radius: {{ $checkin->lokasi->radius_meter }}, color: 'blue' }).addTo(map);
</script>
@endpush
