@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard')

@section('sidebar')
@include('admin.partials.sidebar')
@endsection

@push('styles')
<style>
/* ---- Dashboard-specific tweaks ---- */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 28px;
}
.neu-stat-card {
    cursor: default;
    user-select: none;
}
.chart-wrap {
    background: var(--bg);
    border-radius: var(--radius);
    box-shadow: 6px 6px 16px var(--shadow-dark), -6px -6px 16px var(--shadow-light);
    padding: 22px 24px 18px;
    margin-bottom: 24px;
    height: 100%;
}
.chart-wrap-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
}
.chart-wrap-title .ctitle-icon {
    width: 34px; height: 34px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; color: #fff;
    box-shadow: 3px 3px 8px rgba(0,0,0,0.15);
    flex-shrink: 0;
}
.chart-wrap-title h3 {
    font-size: .92rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
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
</style>
@endpush

@section('content')

{{-- ===== STAT CARDS ===== --}}
<p class="section-label"><i class="fas fa-chart-bar" style="color:var(--accent);"></i>Ringkasan Data</p>

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
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['total_temuan'] }}</h3>
            <p>Total Temuan</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-primary">
            <i class="fas fa-cogs"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['temuan_diproses'] }}</h3>
            <p>Temuan Diproses</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-success">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['temuan_selesai'] }}</h3>
            <p>Temuan Selesai</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-danger">
            <i class="fas fa-fire"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['temuan_risiko_tinggi'] }}</h3>
            <p>Risiko Tinggi</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-secondary">
            <i class="fas fa-camera"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['total_checkin'] }}</h3>
            <p>Total Check-In</p>
        </div>
    </div>

    <div class="neu-stat-card">
        <div class="neu-stat-icon icon-dark">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="neu-stat-body">
            <h3>{{ $stats['total_lokasi'] }}</h3>
            <p>Total Lokasi</p>
        </div>
    </div>

</div>

{{-- ===== CHARTS ===== --}}
<p class="section-label"><i class="fas fa-chart-line" style="color:var(--accent);"></i>Analitik</p>

<div class="row">
    <div class="col-md-7 mb-0">
        <div class="chart-wrap">
            <div class="chart-wrap-title">
                <div class="ctitle-icon" style="background:linear-gradient(135deg,#36b9cc,#258fa0);">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Laporan per Bulan</h3>
            </div>
            <canvas id="chartLaporan" height="110"></canvas>
        </div>
    </div>
    <div class="col-md-5 mb-0">
        <div class="chart-wrap">
            <div class="chart-wrap-title">
                <div class="ctitle-icon" style="background:linear-gradient(135deg,#e74a3b,#c0392b);">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3>Temuan per Risiko</h3>
            </div>
            <canvas id="chartTemuan" height="160"></canvas>
        </div>
    </div>
</div>

{{-- ===== RECENT REPORTS TABLE ===== --}}
<p class="section-label mt-2"><i class="fas fa-list-alt" style="color:var(--accent);"></i>Laporan Terbaru</p>

<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h3 class="card-title"><i class="fas fa-file-alt mr-2" style="color:var(--accent);"></i>Laporan Terbaru</h3>
        <a href="{{ route('admin.laporan.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-arrow-right mr-1"></i>Lihat Semua
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No. Laporan</th>
                        <th>Supervisor</th>
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
                            <span style="font-weight:600;color:var(--accent);">{{ $laporan->nomor_laporan }}</span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--accent-light),var(--accent));display:flex;align-items:center;justify-content:center;color:#fff;font-size:.7rem;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($laporan->user->name, 0, 1)) }}
                                </div>
                                {{ $laporan->user->name }}
                            </div>
                        </td>
                        <td>{{ $laporan->lokasi->nama_lokasi }}</td>
                        <td><span class="badge badge-info">{{ $laporan->statusLabel() }}</span></td>
                        <td style="color:var(--text-muted);">{{ $laporan->tanggal_inspeksi->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.laporan.show', $laporan) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4" style="color:var(--text-muted);">
                            <i class="fas fa-inbox fa-2x d-block mb-2" style="opacity:.35;"></i>
                            Belum ada laporan
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Chart.js global defaults — Poppins font & no border
Chart.defaults.font.family = "'Poppins', sans-serif";
Chart.defaults.font.size   = 12;
Chart.defaults.color       = '#6b7a99';

// Bar chart — Laporan per Bulan
new Chart(document.getElementById('chartLaporan'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartLaporan->pluck('bulan')) !!},
        datasets: [{
            label: 'Laporan',
            data: {!! json_encode($chartLaporan->pluck('total')) !!},
            backgroundColor: 'rgba(78,115,223,0.75)',
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: 'rgba(78,115,223,1)',
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                grid: { display: false },
                ticks: { color: '#6b7a99' },
                border: { display: false }
            },
            y: {
                grid: { color: 'rgba(163,177,198,0.2)', drawBorder: false },
                ticks: { color: '#6b7a99', stepSize: 1 },
                border: { display: false }
            }
        }
    }
});

// Doughnut chart — Temuan per Risiko
new Chart(document.getElementById('chartTemuan'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($chartTemuan->pluck('tingkat_risiko')->map(fn($r) => ucfirst($r))) !!},
        datasets: [{
            data: {!! json_encode($chartTemuan->pluck('total')) !!},
            backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b'],
            hoverBackgroundColor: ['#13a06d', '#d4a017', '#c0392b'],
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 }
            }
        }
    }
});
</script>
@endpush
