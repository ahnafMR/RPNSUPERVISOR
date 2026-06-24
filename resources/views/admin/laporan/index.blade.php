@extends('layouts.app')
@section('title', 'Kelola Laporan')
@section('page_title', 'Kelola Laporan')
@section('sidebar')@include('admin.partials.sidebar')@endsection

@push('styles')
<style>
.export-panel {
    background: var(--bg);
    border-radius: var(--radius);
    box-shadow: 6px 6px 16px var(--shadow-dark), -6px -6px 16px var(--shadow-light);
    padding: 20px 22px;
    margin-bottom: 24px;
}
.export-panel-title {
    font-size: .72rem; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .6px;
    display: flex; align-items: center; gap: 8px;
    margin-bottom: 16px;
}
.export-panel-title::after {
    content: ''; flex: 1; height: 1px; background: rgba(163,177,198,0.35);
}
.export-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 14px;
    margin-bottom: 16px;
}
.export-btn-group {
    display: flex; gap: 10px; flex-wrap: wrap;
}
.filter-divider {
    display: flex; align-items: center; gap: 10px; margin: 16px 0 14px;
}
.filter-divider::before, .filter-divider::after {
    content: ''; flex: 1; height: 1px; background: rgba(163,177,198,0.3);
}
.filter-divider span {
    font-size: .7rem; font-weight: 700; color: var(--text-muted);
    text-transform: uppercase; letter-spacing: .5px; white-space: nowrap;
}
</style>
@endpush

@section('content')

{{-- ══════════════════ EXPORT PANEL ══════════════════ --}}
<div class="export-panel">
    <div class="export-panel-title">
        <i class="fas fa-download" style="color:var(--accent);"></i>
        Download / Export Laporan
    </div>

    {{-- Export filters --}}
    <div class="export-grid">
        {{-- Pilih Bulan --}}
        <div class="form-group mb-0">
            <label for="exp_bulan">
                <i class="fas fa-calendar-alt mr-1" style="color:var(--accent);"></i>Per Bulan
            </label>
            <input type="month" id="exp_bulan" class="form-control"
                   value="{{ request('bulan') }}"
                   placeholder="Pilih bulan">
        </div>

        {{-- Tanggal Dari --}}
        <div class="form-group mb-0">
            <label for="exp_dari">
                <i class="fas fa-calendar-day mr-1" style="color:var(--accent);"></i>Dari Tanggal
            </label>
            <input type="date" id="exp_dari" class="form-control"
                   value="{{ request('tanggal_dari') }}">
        </div>

        {{-- Tanggal Sampai --}}
        <div class="form-group mb-0">
            <label for="exp_sampai">
                <i class="fas fa-calendar-day mr-1" style="color:var(--accent);"></i>Sampai Tanggal
            </label>
            <input type="date" id="exp_sampai" class="form-control"
                   value="{{ request('tanggal_sampai') }}">
        </div>

        {{-- Filter Supervisor --}}
        <div class="form-group mb-0">
            <label for="exp_user">
                <i class="fas fa-user-tie mr-1" style="color:var(--accent);"></i>Supervisor
            </label>
            <select id="exp_user" class="form-control">
                <option value="">Semua Supervisor</option>
                @foreach($supervisors as $sv)
                    <option value="{{ $sv->id }}"
                            {{ request('user_id') == $sv->id ? 'selected' : '' }}>
                        {{ $sv->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Status --}}
        <div class="form-group mb-0">
            <label for="exp_status">
                <i class="fas fa-filter mr-1" style="color:var(--accent);"></i>Status
            </label>
            <select id="exp_status" class="form-control">
                <option value="">Semua Status</option>
                @foreach(['menunggu_review' => 'Menunggu Review', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Download buttons --}}
    <div class="export-btn-group">
        <button type="button" class="btn btn-success" onclick="doExport('excel')">
            <i class="fas fa-file-excel mr-2"></i>Download Excel (.xlsx)
        </button>
        <button type="button" class="btn btn-danger" onclick="doExport('pdf')">
            <i class="fas fa-file-pdf mr-2"></i>Download PDF Rekap
        </button>
        <button type="button" class="btn btn-secondary" onclick="clearExport()">
            <i class="fas fa-times mr-1"></i>Reset
        </button>
    </div>
</div>

{{-- ══════════════════ FILTER & TABLE ══════════════════ --}}
<div class="card">
    <div class="card-header">
        <form method="GET" id="filterForm" class="d-flex flex-wrap align-items-end" style="gap:10px;">
            <div class="form-group mb-0" style="flex:1;min-width:130px;">
                <label style="font-size:.7rem;">Dari Tanggal</label>
                <input type="date" name="tanggal_dari" class="form-control"
                       value="{{ request('tanggal_dari') }}">
            </div>
            <div class="form-group mb-0" style="flex:1;min-width:130px;">
                <label style="font-size:.7rem;">Sampai Tanggal</label>
                <input type="date" name="tanggal_sampai" class="form-control"
                       value="{{ request('tanggal_sampai') }}">
            </div>
            <div class="form-group mb-0" style="flex:1;min-width:150px;">
                <label style="font-size:.7rem;">Supervisor</label>
                <select name="user_id" class="form-control">
                    <option value="">Semua</option>
                    @foreach($supervisors as $sv)
                        <option value="{{ $sv->id }}" {{ request('user_id') == $sv->id ? 'selected' : '' }}>
                            {{ $sv->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="flex:1;min-width:150px;">
                <label style="font-size:.7rem;">Lokasi</label>
                <select name="lokasi_id" class="form-control">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                            {{ $lokasi->nama_lokasi }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0" style="flex:1;min-width:140px;">
                <label style="font-size:.7rem;">Status</label>
                <select name="status" class="form-control">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu_review' => 'Menunggu Review', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-0 d-flex" style="gap:6px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i>Filter
                </button>
                @if(request()->hasAny(['tanggal_dari','tanggal_sampai','user_id','lokasi_id','status']))
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                </a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover datatable mb-0">
                <thead>
                    <tr>
                        <th>No. Laporan</th>
                        <th>Supervisor</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="width:60px;"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($laporans as $laporan)
                    <tr>
                        <td>
                            <span style="font-weight:600;color:var(--accent);">{{ $laporan->nomor_laporan }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:26px;height:26px;border-radius:50%;background:linear-gradient(135deg,var(--accent-light),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-size:.65rem;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($laporan->user->name,0,1)) }}
                                </div>
                                {{ $laporan->user->name }}
                            </div>
                        </td>
                        <td>{{ $laporan->lokasi->nama_lokasi }}</td>
                        <td style="color:var(--text-muted);">{{ $laporan->tanggal_inspeksi->format('d/m/Y') }}</td>
                        <td>
                            @php
                                $bc = match($laporan->status){
                                    'selesai'=>'success','diproses'=>'primary',
                                    'menunggu_review'=>'warning','ditolak'=>'danger',default=>'info'
                                };
                            @endphp
                            <span class="badge badge-{{ $bc }}">{{ $laporan->statusLabel() }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function buildExportUrl(format) {
    var bulan   = document.getElementById('exp_bulan').value;
    var dari    = document.getElementById('exp_dari').value;
    var sampai  = document.getElementById('exp_sampai').value;
    var userId  = document.getElementById('exp_user').value;
    var status  = document.getElementById('exp_status').value;

    var params = new URLSearchParams();
    if (bulan)  params.set('bulan', bulan);
    if (dari)   params.set('tanggal_dari', dari);
    if (sampai) params.set('tanggal_sampai', sampai);
    if (userId) params.set('user_id', userId);
    if (status) params.set('status', status);

    var baseUrl = format === 'excel'
        ? '{{ route("admin.laporan.export-excel") }}'
        : '{{ route("admin.laporan.export-pdf-bulk") }}';

    return baseUrl + (params.toString() ? '?' + params.toString() : '');
}

function doExport(format) {
    var bulan  = document.getElementById('exp_bulan').value;
    var dari   = document.getElementById('exp_dari').value;
    var sampai = document.getElementById('exp_sampai').value;

    // Basic validation: bulan XOR (dari/sampai)
    if (bulan && (dari || sampai)) {
        Swal.fire({
            icon: 'warning',
            title: 'Filter Konflik',
            text: 'Gunakan filter "Per Bulan" ATAU "Dari-Sampai Tanggal", tidak keduanya.',
            confirmButtonColor: '#4e73df'
        });
        return;
    }

    window.location.href = buildExportUrl(format);
}

function clearExport() {
    document.getElementById('exp_bulan').value  = '';
    document.getElementById('exp_dari').value   = '';
    document.getElementById('exp_sampai').value = '';
    document.getElementById('exp_user').value   = '';
    document.getElementById('exp_status').value = '';
}
</script>
@endpush
