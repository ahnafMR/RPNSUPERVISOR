@extends('layouts.app')
@section('title', 'Detail Temuan')
@section('page_title', 'Temuan: ' . $temuan->kode_temuan)
@section('sidebar')@include('supervisor.partials.sidebar')@endsection
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <tr><th>Kode</th><td>{{ $temuan->kode_temuan }}</td></tr>
                    <tr><th>Judul</th><td>{{ $temuan->judul_temuan }}</td></tr>
                    <tr><th>Laporan</th><td>{{ $temuan->laporan->nomor_laporan }}</td></tr>
                    <tr><th>Deskripsi</th><td>{{ $temuan->deskripsi }}</td></tr>
                    <tr><th>Risiko</th><td><span class="badge badge-{{ $temuan->tingkat_risiko === 'tinggi' ? 'danger' : ($temuan->tingkat_risiko === 'sedang' ? 'warning' : 'success') }}">{{ $temuan->risikoLabel() }}</span></td></tr>
                    <tr><th>Rekomendasi</th><td>{{ $temuan->rekomendasi ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ $temuan->statusLabel() }}</td></tr>
                </table>
                @if($temuan->fotoTemuans->count())
                <h5>Foto Temuan</h5>
                <div class="row">@foreach($temuan->fotoTemuans as $f)<div class="col-12 col-md-3"><img src="{{ asset('storage/'.$f->foto) }}" class="img-fluid rounded mb-2"></div>@endforeach</div>
                @endif
            </div>
        </div>
        @include('partials.timeline', ['temuan' => $temuan])
        @if($temuan->prosesTemuan)
        <div class="card">
            <div class="card-header"><h3 class="card-title">Proses Tindak Lanjut</h3></div>
            <div class="card-body">
                <p><strong>PIC:</strong> {{ $temuan->prosesTemuan->pic }}</p>
                <p><strong>Tindakan:</strong> {{ $temuan->prosesTemuan->tindakan }}</p>
                <p><strong>Catatan:</strong> {{ $temuan->prosesTemuan->catatan ?? '-' }}</p>
                @if($temuan->prosesTemuan->foto_proses)<img src="{{ asset('storage/'.$temuan->prosesTemuan->foto_proses) }}" class="img-fluid rounded mb-2">@endif
            </div>
        </div>
        @endif
        @if($temuan->hasilTemuan)
        <div class="card">
            <div class="card-header"><h3 class="card-title">Hasil Perbaikan</h3></div>
            <div class="card-body">
                <p><strong>Hasil:</strong> {{ $temuan->hasilTemuan->hasil_perbaikan }}</p>
                <p><strong>Tanggal Selesai:</strong> {{ $temuan->hasilTemuan->tanggal_selesai->format('d/m/Y') }}</p>
                <p><strong>Catatan:</strong> {{ $temuan->hasilTemuan->catatan_akhir ?? '-' }}</p>
                    @if($temuan->hasilTemuan->foto_hasil)<img src="{{ asset('storage/'.$temuan->hasilTemuan->foto_hasil) }}" class="img-fluid rounded mb-2">@endif
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-4">
        <a href="{{ route('supervisor.laporan.show', $temuan->laporan) }}" class="btn btn-secondary btn-block">Kembali ke Laporan</a>
    </div>
</div>
@endsection
