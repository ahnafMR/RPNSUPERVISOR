@extends('layouts.app')
@section('title', 'Kelola Check-In')
@section('page_title', 'Kelola Check-In')
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered datatable">
            <thead>
                <tr><th>Supervisor</th><th>Lokasi</th><th>GPS</th><th>Check-In</th><th>Check-Out</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            @foreach($checkins as $checkin)
                <tr>
                    <td>{{ $checkin->user->name }}</td>
                    <td>{{ $checkin->lokasi->nama_lokasi }}</td>
                    <td>{{ $checkin->latitude }}, {{ $checkin->longitude }}</td>
                    <td>{{ $checkin->waktu_checkin->format('d/m/Y H:i') }}</td>
                    <td>{{ $checkin->waktu_checkout?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td><span class="badge badge-{{ $checkin->status === 'aktif' ? 'success' : 'secondary' }}">{{ ucfirst($checkin->status) }}</span></td>
                    <td><a href="{{ route('admin.checkin.show', $checkin) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
