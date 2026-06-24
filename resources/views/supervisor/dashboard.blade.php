@extends('layouts.app')

@section('title', 'Dashboard Supervisor')
@section('page_title', 'Dashboard')

@section('sidebar')
@include('supervisor.partials.sidebar')
@endsection

@push('styles')
<style>
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
}
.section-label {
    font-size: .72rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: rgba(163,177,198,0.35);
}

/* Check-in status banner */
.checkin-banner {
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.checkin-banner.active {
    background: linear-gradient(135deg, rgba(28,200,138,0.12), rgba(28,200,138,0.05));
    box-shadow: 6px 6px 16px var(--shadow-dark), -6px -6px 16px var(--shadow-light);
    border-left: 4px solid var(--success);
}
.checkin-banner.inactive {
    background: linear-gradient(135deg, rgba(246,194,62,0.12), rgba(246,194,62,0.05));
    box-shadow: 6px 6px 16px var(--shadow-dark), -6px -6px 16px var(--shadow-light);
    border-left: 4px solid var(--warning);
}
.checkin-banner-icon {
    width: 46px; height: 46px;
    border-radius: var(--radius-sm);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; color: #fff; flex-shrink: 0;
    box-shadow: 3px 3px 8px rgba(0,0,0,0.15);
}
.checkin-banner-icon.active  { background: linear-gradient(135deg, #1cc88a, #13a06d); }
.checkin-banner-icon.inactive { background: linear-gradient(135deg, #f6c23e, #d4a017); }
.checkin-banner-body { flex: 1; }
.checkin-banner-body .title {
    font-weight: 700; font-size: .9rem;
    color: var(--text-primary); margin-bottom: 2px;
}
.checkin-banner-body .subtitle {
    font-size: .78rem; color: var(--text-secondary);
}
.checkin-banner-body .subtitle a {
    color: var(--accent); font-weight: 600;
}

/* Quick action buttons */
.quick-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}
.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 20px;
    background: var(--bg);
    border-radius: var(--radius);
    box-shadow: 5px 5px 14px var(--shadow-dark), -5px -5px 14px var(--shadow-light);
    color: var(--text-primary);
    font-weight: 600;
    font-size: .83rem;
    text-decoration: none !important;
    transition: var(--transition);
    border: none;
    flex: 1;
    min-width: 160px;
}
.quick-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 7px 7px 18px var(--shadow-dark), -7px -7px 18px var(--shadow-light);
    color: var(--text-primary);
}
.quick-action-btn .qa-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; color: #fff; flex-shrink: 0;
    box-shadow: 2px 2px 6px rgba(0,0,0,0.15);
}
.quick-action-btn .qa-label { line-height: 1.2; }
.quick-action-btn .qa-label small {
    display: block; font-size: .7rem;
    color: var(--text-muted); font-weight: 400;
}
</style>
@endpush

@section('content')

{{-- ===== CHECK-IN STATUS BANNER ===== --}}
@if($activeCheckin)
<div class="checkin-banner active">
    <div class="d-flex align-items-center gap-3" style="gap:14px;">
        <div class="checkin-banner-icon active">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="checkin-banner-body">
            <p class="title">
                <i class="fas fa-circle mr-1" style="color:var(--success);font-size:.6rem;vertical-align:middle;"></i>
                Check-In Aktif
            </p>
            <p class="subtitle">
                Lokasi: <strong>{{ $activeCheckin->lokasi->nama_lokasi }}</strong>
                &nbsp;&bull;&nbsp; Sejak {{ $activeCheckin->waktu_checkin->format('d/m/Y H:i') }}
            </p>
        </div>
    </div>
    <form action="{{ route('supervisor.checkin.checkout') }}" method="POST" class="m-0">
        @csrf
        <button type="submit" class="btn btn-warning"
                onclick="return confirm('Check-out dari lokasi ini sekarang?')">
            <i class="fas fa-sign-out-alt mr-1"></i>Check-Out
        </button>
    </form>
</div>
@else
<div class="checkin-banner inactive">
    <div class="d-flex align-items-center gap-3" style="gap:14px;">
        <div class="checkin-banner-icon inactive">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="checkin-banner-body">
            <p class="title">Belum Check-In</p>
            <p class="subtitle">
                Lakukan <a href="{{ route('supervisor.checkin.index') }}">check-in selfie</a>
                terlebih dahulu untuk membuat laporan inspeksi.
            </p>
        </div>
    </div>
    <a href="{{ route('supervisor.checkin.index') }}" class="btn btn-warning">
        <i class="fas fa-camera mr-1"></i>Check-In Sekarang
    </a>
</div>
@endif

{{-- ===== STAT CARDS ===== --}}
<p class="section-label"><i class="fas fa-chart-bar" style="color:var(--accent);"></i>Ringkasan Saya</p>

<div class="stat-grid">

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-info">
            <i class="fas fa-file-alt"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['total_laporan'] }}</h3>
            <p>Total Laporan</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['menunggu_review'] }}</h3>
            <p>Menunggu Review</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-primary">
            <i class="fas fa-cogs"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['diproses'] }}</h3>
            <p>Diproses</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-success">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['selesai'] }}</h3>
            <p>Selesai</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['total_temuan'] }}</h3>
            <p>Total Temuan</p>
        </div>
    </div>

</div>

{{-- ===== QUICK ACTIONS ===== --}}
<p class="section-label"><i class="fas fa-bolt" style="color:var(--accent);"></i>Aksi Cepat</p>

<div class="quick-actions mb-4">
    @if($activeCheckin)
    <a href="{{ route('supervisor.laporan.create') }}" class="quick-action-btn">
        <div class="qa-icon" style="background:linear-gradient(135deg,var(--accent),var(--accent-dark));">
            <i class="fas fa-plus"></i>
        </div>
        <div class="qa-label">
            Buat Laporan
            <small>Laporan inspeksi baru</small>
        </div>
    </a>
    @endif
    <a href="{{ route('supervisor.laporan.index') }}" class="quick-action-btn">
        <div class="qa-icon" style="background:linear-gradient(135deg,#36b9cc,#258fa0);">
            <i class="fas fa-list-alt"></i>
        </div>
        <div class="qa-label">
            Semua Laporan
            <small>Lihat riwayat laporan</small>
        </div>
    </a>
    <a href="{{ route('supervisor.checkin.index') }}" class="quick-action-btn">
        <div class="qa-icon" style="background:linear-gradient(135deg,#1cc88a,#13a06d);">
            <i class="fas fa-camera"></i>
        </div>
        <div class="qa-label">
            Check-In
            <small>Selfie & validasi GPS</small>
        </div>
    </a>
</div>

{{-- ===== RECENT REPORTS TABLE ===== --}}
<p class="section-label"><i class="fas fa-list-alt" style="color:var(--accent);"></i>Laporan Terbaru</p>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title">
            <i class="fas fa-file-alt mr-2" style="color:var(--accent);"></i>Laporan Terbaru
        </h3>
        <a href="{{ route('supervisor.laporan.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-arrow-right mr-1"></i>Lihat Semua
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Laporan</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @forelse($recentLaporans as $laporan)
                    <tr>
                        <td>
                            <span style="font-weight:600;color:var(--accent);">
                                {{ $laporan->nomor_laporan }}
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-map-marker-alt mr-1" style="color:var(--danger);font-size:.75rem;"></i>
                            {{ $laporan->lokasi->nama_lokasi }}
                        </td>
                        <td>
                            @php
                                $statusColor = match($laporan->status) {
                                    'menunggu_review' => 'warning',
                                    'diproses'        => 'primary',
                                    'selesai'         => 'success',
                                    'ditolak'         => 'danger',
                                    default           => 'info',
                                };
                            @endphp
                            <span class="badge badge-{{ $statusColor }}">{{ $laporan->statusLabel() }}</span>
                        </td>
                        <td style="color:var(--text-muted);">
                            {{ $laporan->tanggal_inspeksi->format('d/m/Y') }}
                        </td>
                        <td>
                            <a href="{{ route('supervisor.laporan.show', $laporan) }}"
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:var(--text-muted);">
                            <i class="fas fa-inbox fa-2x d-block mb-2" style="opacity:.3;"></i>
                            Belum ada laporan.
                            @if($activeCheckin)
                                <a href="{{ route('supervisor.laporan.create') }}" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus mr-1"></i>Buat Laporan Pertama
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
