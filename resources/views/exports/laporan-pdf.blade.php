<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan {{ $laporan->nomor_laporan }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size:10.5pt; color:#1a1a2e; background:#fff; }

  /* ── Page ── */
  @page { margin: 18mm 16mm 18mm 16mm; size: A4 portrait; }

  /* ── Header / Letterhead ── */
  .letterhead { border-bottom: 3px solid #2c5f9e; padding-bottom: 10px; margin-bottom: 14px; }
  .letterhead-inner { display:table; width:100%; }
  .letterhead-logo  { display:table-cell; vertical-align:middle; width:60px; }
  .logo-box {
    width:52px; height:52px; border-radius:10px;
    background: #2c5f9e;
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-size:18pt; font-weight:700; text-align:center;
    line-height:52px;
  }
  .letterhead-text { display:table-cell; vertical-align:middle; padding-left:14px; }
  .letterhead-text h1 { font-size:14pt; font-weight:700; color:#1e3a5f; letter-spacing:.5px; }
  .letterhead-text p  { font-size:8.5pt; color:#555; margin-top:2px; }
  .letterhead-right   { display:table-cell; vertical-align:middle; text-align:right; width:180px; }
  .doc-number { font-size:8.5pt; color:#666; }
  .doc-number strong  { display:block; font-size:10pt; color:#1e3a5f; letter-spacing:.3px; }

  /* ── Status badge ── */
  .status-stamp {
    display:inline-block; padding:4px 14px; border-radius:4px;
    font-size:9.5pt; font-weight:700; letter-spacing:.5px; text-transform:uppercase;
    border: 2px solid currentColor;
  }
  .status-selesai   { color:#155724; background:#d4edda; border-color:#c3e6cb; }
  .status-diproses  { color:#1a3f6f; background:#cce5ff; border-color:#b8daff; }
  .status-menunggu  { color:#856404; background:#fff3cd; border-color:#ffeeba; }
  .status-ditolak   { color:#721c24; background:#f8d7da; border-color:#f5c6cb; }

  /* ── Section title ── */
  .section-title {
    font-size:9pt; font-weight:700; color:#fff;
    background:#2c5f9e; padding:5px 10px;
    margin-bottom:0; letter-spacing:.4px; text-transform:uppercase;
  }
  .section-body { border:1px solid #d0d7e3; border-top:none; margin-bottom:16px; }

  /* ── Info table ── */
  .info-table { width:100%; border-collapse:collapse; }
  .info-table tr { border-bottom:1px solid #e8ecf3; }
  .info-table tr:last-child { border-bottom:none; }
  .info-table th {
    width:28%; padding:6px 10px; font-size:9.5pt; font-weight:600;
    color:#2c5f9e; background:#f0f4fa; vertical-align:top;
    border-right:1px solid #d0d7e3;
  }
  .info-table td { padding:6px 10px; font-size:9.5pt; color:#333; vertical-align:top; }

  /* ── Temuan table ── */
  .temuan-table { width:100%; border-collapse:collapse; font-size:9pt; }
  .temuan-table thead th {
    background:#2c5f9e; color:#fff; padding:6px 8px;
    text-align:center; font-weight:700; letter-spacing:.3px;
    border:1px solid #1a3f6f;
  }
  .temuan-table tbody tr { border-bottom:1px solid #d0d7e3; }
  .temuan-table tbody tr:nth-child(even) { background:#f5f8fd; }
  .temuan-table tbody td { padding:5px 8px; vertical-align:top; border:1px solid #d0d7e3; }
  .temuan-table tbody td.center { text-align:center; }

  /* risk badges */
  .risk { display:inline-block; padding:2px 8px; border-radius:3px; font-size:8pt; font-weight:700; }
  .risk-tinggi  { color:#721c24; background:#f8d7da; }
  .risk-sedang  { color:#856404; background:#fff3cd; }
  .risk-rendah  { color:#155724; background:#d4edda; }

  /* ── Signature ── */
  .signature-table { width:100%; border-collapse:collapse; margin-top:28px; }
  .signature-table td { width:33.33%; text-align:center; padding:0 10px; vertical-align:bottom; }
  .sig-line { border-top:1.5px solid #333; margin-top:48px; padding-top:5px; font-size:9pt; }
  .sig-name { font-weight:700; font-size:9.5pt; }
  .sig-title { font-size:8.5pt; color:#555; }

  /* ── Footer ── */
  .page-footer {
    position:fixed; bottom:0; left:0; right:0;
    border-top:1.5px solid #2c5f9e;
    padding-top:5px;
    display:table; width:100%;
    font-size:7.5pt; color:#888;
  }
  .page-footer .left  { display:table-cell; text-align:left; }
  .page-footer .right { display:table-cell; text-align:right; }

  /* ── Utilities ── */
  .text-muted { color:#777; font-style:italic; }
  .mt-4 { margin-top:16px; }
  .mb-2 { margin-bottom:8px; }
</style>
</head>
<body>

<!-- ── Fixed Footer ── -->
<div class="page-footer">
  <span class="left">Dokumen Rahasia — Hanya untuk keperluan internal PT. RPN</span>
  <span class="right">Dicetak: {{ now()->format('d/m/Y H:i') }}</span>
</div>

<!-- ── Letterhead ── -->
<div class="letterhead">
  <div class="letterhead-inner">
    <div class="letterhead-logo">
      <div class="logo-box">R</div>
    </div>
    <div class="letterhead-text">
      <h1>PT. RPN — LAPORAN INSPEKSI</h1>
      <p>Sistem Manajemen Laporan Supervisor &bull; Dokumen Resmi</p>
    </div>
    <div class="letterhead-right">
      <div class="doc-number">
        Nomor Dokumen
        <strong>{{ $laporan->nomor_laporan }}</strong>
      </div>
      <div style="margin-top:6px;">
        @php
          $statusClass = match($laporan->status) {
            'selesai'         => 'status-selesai',
            'diproses'        => 'status-diproses',
            'menunggu_review' => 'status-menunggu',
            'ditolak'         => 'status-ditolak',
            default           => '',
          };
        @endphp
        <span class="status-stamp {{ $statusClass }}">{{ $laporan->statusLabel() }}</span>
      </div>
    </div>
  </div>
</div>

<!-- ── Informasi Umum ── -->
<div class="section-title">Informasi Laporan</div>
<div class="section-body">
  <table class="info-table">
    <tr>
      <th>No. Laporan</th>
      <td><strong>{{ $laporan->nomor_laporan }}</strong></td>
      <th>Tanggal Inspeksi</th>
      <td>{{ $laporan->tanggal_inspeksi->format('d F Y') }}</td>
    </tr>
    <tr>
      <th>Supervisor</th>
      <td>{{ $laporan->user->name }}</td>
      <th>Lokasi</th>
      <td>{{ $laporan->lokasi->nama_lokasi }} <span class="text-muted">({{ $laporan->lokasi->kode_lokasi }})</span></td>
    </tr>
    <tr>
      <th>Area Inspeksi</th>
      <td>{{ $laporan->area }}</td>
      <th>Kategori</th>
      <td>{{ $laporan->kategori }}</td>
    </tr>
    <tr>
      <th>Deskripsi</th>
      <td colspan="3">{{ $laporan->deskripsi }}</td>
    </tr>
    @if($laporan->catatan_approval)
    <tr>
      <th>Catatan Reviewer</th>
      <td colspan="3">{{ $laporan->catatan_approval }}</td>
    </tr>
    @endif
    @if($laporan->approver && $laporan->approved_at)
    <tr>
      <th>Disetujui oleh</th>
      <td>{{ $laporan->approver->name }}</td>
      <th>Tanggal Approval</th>
      <td>{{ $laporan->approved_at->format('d F Y H:i') }}</td>
    </tr>
    @endif
  </table>
</div>

<!-- ── Daftar Temuan ── -->
<div class="section-title">
  Daftar Temuan
  <span style="font-size:8pt;font-weight:400;opacity:.85;">({{ $laporan->temuans->count() }} item)</span>
</div>
<div class="section-body">
  @if($laporan->temuans->count())
  <table class="temuan-table">
    <thead>
      <tr>
        <th style="width:30px;">No</th>
        <th style="width:90px;">Kode</th>
        <th>Judul Temuan</th>
        <th style="width:70px;">Risiko</th>
        <th style="width:85px;">Status</th>
        <th>Rekomendasi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($laporan->temuans as $i => $t)
      <tr>
        <td class="center">{{ $i + 1 }}</td>
        <td class="center" style="font-size:8.5pt;font-family:monospace;">{{ $t->kode_temuan }}</td>
        <td>{{ $t->judul_temuan }}</td>
        <td class="center">
          @php
            $riskClass = match($t->tingkat_risiko) {
              'tinggi' => 'risk-tinggi',
              'sedang' => 'risk-sedang',
              default  => 'risk-rendah',
            };
          @endphp
          <span class="risk {{ $riskClass }}">{{ ucfirst($t->tingkat_risiko) }}</span>
        </td>
        <td class="center">{{ $t->statusLabel() }}</td>
        <td>{{ $t->rekomendasi ?? '-' }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
    <p style="padding:12px 10px;color:#888;font-style:italic;font-size:9pt;">Tidak ada temuan dalam laporan ini.</p>
  @endif
</div>

<!-- ── Tanda Tangan ── -->
<div class="mt-4">
  <table class="signature-table">
    <tr>
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $laporan->user->name }}</div>
          <div class="sig-title">Supervisor Lapangan</div>
        </div>
      </td>
      <td>
        <div class="sig-line">
          <div class="sig-name">{{ $laporan->approver?->name ?? '___________________' }}</div>
          <div class="sig-title">Penyetuju / Admin</div>
        </div>
      </td>
      <td>
        <div class="sig-line">
          <div class="sig-name">___________________</div>
          <div class="sig-title">Mengetahui</div>
        </div>
      </td>
    </tr>
    <tr>
      <td style="padding-top:4px;"><div class="sig-title text-muted">Tanggal: {{ $laporan->tanggal_inspeksi->format('d/m/Y') }}</div></td>
      <td style="padding-top:4px;"><div class="sig-title text-muted">Tanggal: {{ $laporan->approved_at?->format('d/m/Y') ?? '___/___/______' }}</div></td>
      <td style="padding-top:4px;"><div class="sig-title text-muted">Tanggal: ___/___/______</div></td>
    </tr>
  </table>
</div>

</body>
</html>
