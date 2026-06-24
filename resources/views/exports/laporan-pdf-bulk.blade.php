<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Laporan Inspeksi</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DejaVu Sans', Arial, sans-serif; font-size:10pt; color:#1a1a2e; }
  @page { margin:15mm 14mm; size:A4 portrait; }

  .letterhead { border-bottom:3px solid #2c5f9e; padding-bottom:10px; margin-bottom:14px; display:table; width:100%; }
  .lh-left  { display:table-cell; vertical-align:middle; }
  .lh-right { display:table-cell; vertical-align:middle; text-align:right; width:200px; }
  .lh-left h1 { font-size:13pt; font-weight:700; color:#1e3a5f; }
  .lh-left p  { font-size:8.5pt; color:#555; margin-top:2px; }
  .lh-right .filter-box {
    display:inline-block; padding:5px 10px; background:#eef3fa;
    border:1px solid #c5d3e8; border-radius:4px; font-size:8pt; color:#2c5f9e;
    text-align:left; max-width:190px;
  }
  .filter-box strong { display:block; font-size:7.5pt; color:#888; text-transform:uppercase; letter-spacing:.3px; margin-bottom:2px; }

  .summary-bar { display:table; width:100%; margin-bottom:14px; border-collapse:separate; border-spacing:6px 0; }
  .sum-cell    { display:table-cell; text-align:center; padding:7px 4px; border-radius:4px; font-size:9pt; font-weight:700; }
  .sum-total   { background:#e8eef8; color:#1e3a5f; border:1px solid #c5d3e8; }
  .sum-selesai { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
  .sum-diproses{ background:#cce5ff; color:#1a3f6f; border:1px solid #b8daff; }
  .sum-menunggu{ background:#fff3cd; color:#856404; border:1px solid #ffeeba; }
  .sum-ditolak { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
  .sum-cell span { display:block; font-size:7.5pt; font-weight:400; opacity:.75; margin-top:1px; }

  table.main { width:100%; border-collapse:collapse; font-size:8.8pt; }
  table.main thead th {
    background:#2c5f9e; color:#fff; padding:6px 6px;
    border:1px solid #1a3f6f; text-align:center;
    font-size:8.5pt; letter-spacing:.3px;
  }
  table.main tbody tr { border-bottom:1px solid #d0d7e3; }
  table.main tbody tr:nth-child(even) { background:#f5f8fd; }
  table.main tbody td { padding:5px 6px; border:1px solid #d0d7e3; vertical-align:top; }
  table.main tbody td.center { text-align:center; }

  .badge { display:inline-block; padding:2px 7px; border-radius:3px; font-size:7.5pt; font-weight:700; }
  .s-selesai  { color:#155724; background:#d4edda; }
  .s-diproses { color:#1a3f6f; background:#cce5ff; }
  .s-menunggu { color:#856404; background:#fff3cd; }
  .s-ditolak  { color:#721c24; background:#f8d7da; }
  .r-tinggi   { color:#721c24; background:#f8d7da; }
  .r-sedang   { color:#856404; background:#fff3cd; }
  .r-rendah   { color:#155724; background:#d4edda; }

  .page-footer {
    position:fixed; bottom:0; left:0; right:0;
    border-top:1.5px solid #2c5f9e;
    padding-top:4px; display:table; width:100%;
    font-size:7.5pt; color:#888;
  }
  .page-footer .left  { display:table-cell; text-align:left; }
  .page-footer .right { display:table-cell; text-align:right; }

  .empty-state { text-align:center; padding:30px; color:#888; font-style:italic; }
</style>
</head>
<body>

<div class="page-footer">
  <span class="left">Dokumen Rahasia — PT. RPN Sistem Manajemen Laporan Inspeksi</span>
  <span class="right">Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
</div>

<!-- Letterhead -->
<div class="letterhead">
  <div class="lh-left">
    <h1>REKAP LAPORAN INSPEKSI — PT. RPN</h1>
    <p>Sistem Manajemen Laporan Supervisor &bull; Dokumen Resmi</p>
  </div>
  <div class="lh-right">
    <div class="filter-box">
      <strong>Filter Aktif</strong>
      {{ $filterLabel }}
    </div>
  </div>
</div>

<!-- Summary bar -->
@php
  $total    = $laporans->count();
  $selesai  = $laporans->where('status','selesai')->count();
  $diproses = $laporans->where('status','diproses')->count();
  $menunggu = $laporans->where('status','menunggu_review')->count();
  $ditolak  = $laporans->where('status','ditolak')->count();
@endphp
<div class="summary-bar">
  <div class="sum-cell sum-total">{{ $total }} <span>Total Laporan</span></div>
  <div class="sum-cell sum-selesai">{{ $selesai }} <span>Selesai</span></div>
  <div class="sum-cell sum-diproses">{{ $diproses }} <span>Diproses</span></div>
  <div class="sum-cell sum-menunggu">{{ $menunggu }} <span>Menunggu</span></div>
  <div class="sum-cell sum-ditolak">{{ $ditolak }} <span>Ditolak</span></div>
</div>

<!-- Main table -->
@if($laporans->count())
<table class="main">
  <thead>
    <tr>
      <th style="width:26px;">No</th>
      <th style="width:68px;">Tanggal</th>
      <th style="width:105px;">No. Laporan</th>
      <th>Supervisor</th>
      <th>Lokasi</th>
      <th>Area</th>
      <th style="width:70px;">Kategori</th>
      <th style="width:72px;">Status</th>
      <th style="width:38px;">Temuan</th>
    </tr>
  </thead>
  <tbody>
    @foreach($laporans as $i => $l)
    <tr>
      <td class="center" style="font-size:8pt;">{{ $i+1 }}</td>
      <td class="center">{{ $l->tanggal_inspeksi->format('d/m/Y') }}</td>
      <td style="font-size:8pt;font-family:monospace;">{{ $l->nomor_laporan }}</td>
      <td>{{ $l->user->name ?? '-' }}</td>
      <td>{{ $l->lokasi->nama_lokasi ?? '-' }}</td>
      <td>{{ $l->area }}</td>
      <td class="center">{{ $l->kategori }}</td>
      <td class="center">
        @php $sc = match($l->status){ 'selesai'=>'s-selesai','diproses'=>'s-diproses','menunggu_review'=>'s-menunggu','ditolak'=>'s-ditolak',default=>'' }; @endphp
        <span class="badge {{ $sc }}">{{ $l->statusLabel() }}</span>
      </td>
      <td class="center">{{ $l->temuans->count() }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
@else
  <div class="empty-state">Tidak ada laporan yang sesuai dengan filter yang dipilih.</div>
@endif

</body>
</html>
