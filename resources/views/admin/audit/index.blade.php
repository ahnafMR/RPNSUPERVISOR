@extends('layouts.app')
@section('title', 'Audit Log')
@section('page_title', 'Audit Log')
@section('sidebar')@include('admin.partials.sidebar')@endsection
@section('content')
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-bordered datatable">
            <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Deskripsi</th><th>IP</th></tr></thead>
            <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $log->user->name ?? 'System' }}</td>
                    <td><span class="badge badge-secondary">{{ $log->action }}</span></td>
                    <td>{{ $log->description }}</td>
                    <td>{{ $log->ip_address }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $logs->links() }}
    </div>
</div>
@endsection
