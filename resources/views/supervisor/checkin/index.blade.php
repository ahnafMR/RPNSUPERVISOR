@extends('layouts.app')
@section('title', 'Check-In Selfie')
@section('page_title', 'Check-In Selfie')
@section('sidebar')@include('supervisor.partials.sidebar')@endsection
@section('content')
@if($activeCheckin)
<div class="alert alert-success">
    <strong>Check-in Aktif</strong> di {{ $activeCheckin->lokasi->nama_lokasi }} sejak {{ $activeCheckin->waktu_checkin->format('d/m/Y H:i') }}
    <form action="{{ route('supervisor.checkin.checkout') }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-warning" onclick="return confirm('Yakin check-out? Anda harus selfie lagi untuk membuat laporan baru.')">
            <i class="fas fa-sign-out-alt"></i> Check-Out
        </button>
    </form>
</div>
@else
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Form Check-In</h3></div>
            <form id="checkinForm" action="{{ route('supervisor.checkin.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label>Pilih Lokasi</label>
                        <select name="lokasi_id" id="lokasi_id" class="form-control" required>
                            <option value="">-- Pilih Lokasi --</option>
                            @foreach($lokasis as $lokasi)
                                <option value="{{ $lokasi->id }}" data-lat="{{ $lokasi->latitude }}" data-lng="{{ $lokasi->longitude }}" data-radius="{{ $lokasi->radius_meter }}">
                                    {{ $lokasi->nama_lokasi }} ({{ $lokasi->kode_lokasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="gpsStatus" class="alert alert-secondary">
                        <i class="fas fa-satellite-dish"></i> Menunggu izin GPS...
                    </div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                    <input type="hidden" name="foto_selfie" id="foto_selfie">
                </div>
                <div class="card-footer">
                    <button type="button" id="btnCapture" class="btn btn-primary" disabled>
                        <i class="fas fa-camera"></i> Ambil Selfie & Check-In
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Kamera Selfie</h3></div>
            <div class="card-body text-center">
                <video id="webcam" autoplay playsinline style="width:100%; max-width:400px; border-radius:8px; background:#000;"></video>
                <canvas id="canvas" style="display:none;"></canvas>
                <div id="preview" class="mt-2"></div>
            </div>
        </div>
        <div class="card">
            <div class="card-body p-0"><div id="map" style="height: 250px;"></div></div>
        </div>
    </div>
</div>
@endif

<div class="card mt-3">
    <div class="card-header"><h3 class="card-title">Riwayat Check-In</h3></div>
    <div class="card-body table-responsive">
        <table class="table table-sm">
            <thead><tr><th>Lokasi</th><th>Check-In</th><th>Check-Out</th><th>Status</th></tr></thead>
            <tbody>
            @foreach($history as $h)
                <tr>
                    <td>{{ $h->lokasi->nama_lokasi }}</td>
                    <td>{{ $h->waktu_checkin->format('d/m/Y H:i') }}</td>
                    <td>{{ $h->waktu_checkout?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td>{{ ucfirst($h->status) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
let userLat = null, userLng = null, gpsValid = false, stream = null;
const map = L.map('map').setView([-6.2, 106.816666], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
let userMarker = null;

function updateGpsStatus(msg, type) {
    document.getElementById('gpsStatus').className = 'alert alert-' + type;
    document.getElementById('gpsStatus').innerHTML = msg;
}

if (navigator.geolocation) {
    navigator.geolocation.watchPosition(function(pos) {
        userLat = pos.coords.latitude;
        userLng = pos.coords.longitude;
        document.getElementById('latitude').value = userLat;
        document.getElementById('longitude').value = userLng;
        if (userMarker) map.removeLayer(userMarker);
        userMarker = L.marker([userLat, userLng]).addTo(map).bindPopup('Posisi Anda');
        map.setView([userLat, userLng], 16);
        validateGps();
    }, function(err) {
        updateGpsStatus('<i class="fas fa-times"></i> GPS ditolak: ' + err.message, 'danger');
    }, { enableHighAccuracy: true });
} else {
    updateGpsStatus('<i class="fas fa-times"></i> Browser tidak mendukung GPS', 'danger');
}

function validateGps() {
    const lokasiId = document.getElementById('lokasi_id').value;
    if (!lokasiId || !userLat) return;
    fetch('{{ route("supervisor.checkin.validate-gps") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ latitude: userLat, longitude: userLng, lokasi_id: lokasiId })
    }).then(r => r.json()).then(data => {
        gpsValid = data.valid;
        if (data.valid) {
            updateGpsStatus('<i class="fas fa-check"></i> GPS valid. Jarak: ' + data.distance + 'm (radius: ' + data.radius + 'm)', 'success');
            document.getElementById('btnCapture').disabled = false;
        } else {
            updateGpsStatus('<i class="fas fa-times"></i> Anda berada di luar area inspeksi yang diizinkan. Jarak: ' + data.distance + 'm', 'danger');
            document.getElementById('btnCapture').disabled = true;
        }
    });
}

document.getElementById('lokasi_id')?.addEventListener('change', validateGps);

async function startWebcam() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
        document.getElementById('webcam').srcObject = stream;
    } catch(e) {
        Swal.fire('Error', 'Tidak dapat mengakses kamera: ' + e.message, 'error');
    }
}
@if(!$activeCheckin) startWebcam(); @endif

document.getElementById('btnCapture')?.addEventListener('click', function() {
    if (!gpsValid) {
        Swal.fire('Gagal', 'Anda berada di luar area inspeksi yang diizinkan.', 'error');
        return;
    }
    const video = document.getElementById('webcam');
    const canvas = document.getElementById('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
    document.getElementById('foto_selfie').value = dataUrl;
    document.getElementById('preview').innerHTML = '<img src="' + dataUrl + '" class="img-fluid rounded" style="max-width:200px">';
    Swal.fire({
        title: 'Konfirmasi Check-In',
        text: 'Kirim selfie dan check-in?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Check-In'
    }).then((result) => {
        if (result.isConfirmed) {
            if (stream) stream.getTracks().forEach(t => t.stop());
            document.getElementById('checkinForm').submit();
        }
    });
});
</script>
@endpush
