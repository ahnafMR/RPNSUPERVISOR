@extends('layouts.app')
@section('title', 'Detail Temuan')
@section('page_title', 'Temuan: ' . $temuan->kode_temuan)
@section('sidebar')@include('admin.partials.sidebar')@endsection
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
                    <tr><th>Risiko</th><td>{{ $temuan->risikoLabel() }}</td></tr>
                    <tr><th>Rekomendasi</th><td>{{ $temuan->rekomendasi ?? '-' }}</td></tr>
                    <tr><th>Status</th><td>{{ $temuan->statusLabel() }}</td></tr>
                </table>
                @if($temuan->fotoTemuans->count())
                <h5>Foto Temuan</h5>
                <div class="row">@foreach($temuan->fotoTemuans as $f)<div class="col-12 col-md-3"><img src="{{ asset('storage/'.$f->foto) }}" class="img-fluid rounded mb-2"></div>@endforeach</div>
                @endif
            </div>
            <div class="card-footer">
                <form action="{{ route('admin.temuan.update-status', $temuan) }}" method="POST" class="form-inline">
                    @csrf @method('PATCH')
                    <select name="status" class="form-control mr-2">
                        @foreach(['menunggu_review','diproses','selesai','ditolak'] as $s)
                            <option value="{{ $s }}" {{ $temuan->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
                @if(!$temuan->prosesTemuan)<a href="{{ route('admin.proses.create', $temuan) }}" class="btn btn-warning mt-2">Proses Temuan</a>@endif
                @if($temuan->prosesTemuan && !$temuan->hasilTemuan)<a href="{{ route('admin.hasil.create', $temuan) }}" class="btn btn-success mt-2">Input Hasil</a>@endif
            </div>
        </div>
        @include('partials.timeline', ['temuan' => $temuan])
    </div>
    <div class="col-md-4">
        @if($temuan->prosesTemuan)
        <div class="card">
            <div class="card-header"><h3 class="card-title">Proses Temuan</h3></div>
            <div class="card-body">
                <p><strong>PIC:</strong> {{ $temuan->prosesTemuan->pic }}</p>
                <p><strong>Tanggal:</strong> {{ $temuan->prosesTemuan->tanggal_proses->format('d/m/Y') }}</p>
                <p><strong>Tindakan:</strong> {{ $temuan->prosesTemuan->tindakan }}</p>
                @if($temuan->prosesTemuan->foto_proses)<img src="{{ asset('storage/'.$temuan->prosesTemuan->foto_proses) }}" class="img-fluid rounded mb-2">@endif
            </div>
        </div>
        @endif
        @if($temuan->hasilTemuan)
        <div class="card">
            <div class="card-header"><h3 class="card-title">Hasil Temuan</h3></div>
            <div class="card-body">
                <p><strong>Hasil:</strong> {{ $temuan->hasilTemuan->hasil_perbaikan }}</p>
                <p><strong>Tanggal Selesai:</strong> {{ $temuan->hasilTemuan->tanggal_selesai->format('d/m/Y') }}</p>
                    @if($temuan->hasilTemuan->foto_hasil)<img src="{{ asset('storage/'.$temuan->hasilTemuan->foto_hasil) }}" class="img-fluid rounded mb-2">@endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
