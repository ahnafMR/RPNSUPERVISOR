<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApprovalLaporanRequest;
use App\Models\LaporanInspeksi;
use App\Models\Lokasi;
use App\Models\User;
use App\Services\AuditLogService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __construct(private AuditLogService $auditLog) {}

    public function index(Request $request)
    {
        $query = LaporanInspeksi::with(['user', 'lokasi']);

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_inspeksi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_inspeksi', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $laporans   = $query->latest()->get();
        $lokasis    = Lokasi::where('status', 'aktif')->get();
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();

        return view('admin.laporan.index', compact('laporans', 'lokasis', 'supervisors'));
    }

    public function show(LaporanInspeksi $laporan)
    {
        $laporan->load(['user', 'lokasi', 'checkin', 'fotoLaporans', 'temuans.fotoTemuans', 'temuans.prosesTemuan', 'temuans.hasilTemuan', 'approver']);
        return view('admin.laporan.show', compact('laporan'));
    }

    public function approve(ApprovalLaporanRequest $request, LaporanInspeksi $laporan)
    {
        $laporan->update([
            'status'            => 'diproses',
            'catatan_approval'  => $request->catatan_approval,
            'approved_by'       => auth()->id(),
            'approved_at'       => now(),
        ]);
        $this->auditLog->log('approve', 'Menyetujui laporan: ' . $laporan->nomor_laporan, $laporan);
        return back()->with('success', 'Laporan berhasil disetujui.');
    }

    public function reject(ApprovalLaporanRequest $request, LaporanInspeksi $laporan)
    {
        $laporan->update([
            'status'           => 'ditolak',
            'catatan_approval' => $request->catatan_approval,
            'approved_by'      => auth()->id(),
            'approved_at'      => now(),
        ]);
        $this->auditLog->log('reject', 'Menolak laporan: ' . $laporan->nomor_laporan, $laporan);
        return back()->with('success', 'Laporan berhasil ditolak.');
    }

    /* ─── Single Laporan PDF ─────────────────────────────── */
    public function exportPdf(LaporanInspeksi $laporan)
    {
        $laporan->load(['user', 'lokasi', 'fotoLaporans', 'temuans.fotoTemuans']);
        $pdf = Pdf::loadView('exports.laporan-pdf', compact('laporan'))
                  ->setPaper('a4', 'portrait');
        return $pdf->download('laporan-' . $laporan->nomor_laporan . '.pdf');
    }

    /* ─── Bulk Excel Export ──────────────────────────────── */
    public function exportExcel(Request $request)
    {
        $laporans = $this->buildFilteredQuery($request)
                         ->with(['user', 'lokasi', 'temuans'])
                         ->get();

        $filename = 'laporan-inspeksi-' . now()->format('Ymd-His') . '.xlsx';

        // Build SpreadsheetML (Excel 2003 XML — opens in all Excel versions, no lib needed)
        $xml = $this->buildExcelXml($laporans, $request);

        return response($xml, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /* ─── Bulk PDF Export ────────────────────────────────── */
    public function exportPdfBulk(Request $request)
    {
        $laporans = $this->buildFilteredQuery($request)
                         ->with(['user', 'lokasi', 'temuans'])
                         ->get();

        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();
        $filterLabel = $this->buildFilterLabel($request, $supervisors);

        $pdf = Pdf::loadView('exports.laporan-pdf-bulk', compact('laporans', 'filterLabel'))
                  ->setPaper('a4', 'portrait');

        $filename = 'rekap-laporan-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }

    /* ─── Helpers ────────────────────────────────────────── */
    private function buildFilteredQuery(Request $request)
    {
        $query = LaporanInspeksi::query();

        if ($request->filled('bulan')) {
            [$y, $m] = explode('-', $request->bulan);
            $query->whereYear('tanggal_inspeksi', $y)
                  ->whereMonth('tanggal_inspeksi', $m);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_inspeksi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_inspeksi', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->latest('tanggal_inspeksi');
    }

    private function buildFilterLabel(Request $request, $supervisors): string
    {
        $parts = [];
        if ($request->filled('bulan')) {
            $parts[] = 'Bulan: ' . \Carbon\Carbon::parse($request->bulan . '-01')->translatedFormat('F Y');
        }
        if ($request->filled('tanggal_dari') || $request->filled('tanggal_sampai')) {
            $dari    = $request->filled('tanggal_dari')    ? \Carbon\Carbon::parse($request->tanggal_dari)->format('d/m/Y')    : '—';
            $sampai  = $request->filled('tanggal_sampai')  ? \Carbon\Carbon::parse($request->tanggal_sampai)->format('d/m/Y') : '—';
            $parts[] = "Tanggal: {$dari} s/d {$sampai}";
        }
        if ($request->filled('user_id')) {
            $sv = $supervisors->find($request->user_id);
            if ($sv) $parts[] = 'Supervisor: ' . $sv->name;
        }
        if ($request->filled('status')) {
            $parts[] = 'Status: ' . ucfirst(str_replace('_', ' ', $request->status));
        }
        return $parts ? implode('  |  ', $parts) : 'Semua Laporan';
    }

    private function buildExcelXml($laporans, Request $request): string
    {
        $supervisors = User::where('role', 'supervisor')->orderBy('name')->get();
        $filterLabel = $this->buildFilterLabel($request, $supervisors);

        $bulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $e = fn($v) => htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        // ── Styles ──────────────────────────────────────────
        $styles = <<<STYLES
  <Style ss:ID="Default" ss:Name="Normal">
    <Alignment ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="11"/>
  </Style>
  <Style ss:ID="title">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#1E3A5F"/>
  </Style>
  <Style ss:ID="subtitle">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#555555"/>
  </Style>
  <Style ss:ID="filter">
    <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="10" ss:Italic="1" ss:Color="#666666"/>
    <Interior ss:Color="#F7F9FC" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="th">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
    <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
    <Interior ss:Color="#2C5F9E" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1A3F6F"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#1A3F6F"/>
    </Borders>
  </Style>
  <Style ss:ID="td">
    <Alignment ss:Vertical="Center" ss:WrapText="1"/>
    <Font ss:FontName="Calibri" ss:Size="10"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
      <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
    </Borders>
  </Style>
  <Style ss:ID="td_alt">
    <Alignment ss:Vertical="Center" ss:WrapText="1"/>
    <Font ss:FontName="Calibri" ss:Size="10"/>
    <Interior ss:Color="#EEF3FA" ss:Pattern="Solid"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
      <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
    </Borders>
  </Style>
  <Style ss:ID="td_center"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="10"/>
    <Borders>
      <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
      <Border ss:Position="Right"  ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
      <Border ss:Position="Left"   ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#D0D7E3"/>
    </Borders>
  </Style>
  <Style ss:ID="badge_selesai"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#0A5C36"/>
    <Interior ss:Color="#D4EDDA" ss:Pattern="Solid"/>
    <Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#C3E6CB"/></Borders>
  </Style>
  <Style ss:ID="badge_diproses"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#1A3F6F"/>
    <Interior ss:Color="#CCE5FF" ss:Pattern="Solid"/>
    <Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#B8DAFF"/></Borders>
  </Style>
  <Style ss:ID="badge_menunggu"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#856404"/>
    <Interior ss:Color="#FFF3CD" ss:Pattern="Solid"/>
    <Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#FFEEBA"/></Borders>
  </Style>
  <Style ss:ID="badge_ditolak"><Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#721C24"/>
    <Interior ss:Color="#F8D7DA" ss:Pattern="Solid"/>
    <Borders><Border ss:Position="All" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#F5C6CB"/></Borders>
  </Style>
  <Style ss:ID="footer_cell">
    <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
    <Font ss:FontName="Calibri" ss:Size="9" ss:Italic="1" ss:Color="#888888"/>
    <Interior ss:Color="#F0F4FA" ss:Pattern="Solid"/>
  </Style>
STYLES;

        // ── Rows ─────────────────────────────────────────────
        $rows = '';
        foreach ($laporans as $i => $l) {
            $styleData  = ($i % 2 === 0) ? 'td'     : 'td_alt';
            $styleCenter = ($i % 2 === 0) ? 'td_center' : 'td_center';

            $statusStyle = match ($l->status) {
                'selesai'         => 'badge_selesai',
                'diproses'        => 'badge_diproses',
                'menunggu_review' => 'badge_menunggu',
                'ditolak'         => 'badge_ditolak',
                default           => 'td_center',
            };

            $tgl = $l->tanggal_inspeksi instanceof \Carbon\Carbon
                 ? $l->tanggal_inspeksi->format('d/m/Y')
                 : \Carbon\Carbon::parse($l->tanggal_inspeksi)->format('d/m/Y');

            $jmlTemuan = $l->temuans->count();

            $rows .= "
    <Row ss:Height=\"22\">
      <Cell ss:StyleID=\"{$styleCenter}\"><Data ss:Type=\"Number\">" . ($i + 1) . "</Data></Cell>
      <Cell ss:StyleID=\"{$styleCenter}\"><Data ss:Type=\"String\">{$e($tgl)}</Data></Cell>
      <Cell ss:StyleID=\"{$styleData}\"><Data ss:Type=\"String\">{$e($l->nomor_laporan)}</Data></Cell>
      <Cell ss:StyleID=\"{$styleData}\"><Data ss:Type=\"String\">{$e($l->user->name ?? '-')}</Data></Cell>
      <Cell ss:StyleID=\"{$styleData}\"><Data ss:Type=\"String\">{$e($l->lokasi->nama_lokasi ?? '-')}</Data></Cell>
      <Cell ss:StyleID=\"{$styleData}\"><Data ss:Type=\"String\">{$e($l->area)}</Data></Cell>
      <Cell ss:StyleID=\"{$styleData}\"><Data ss:Type=\"String\">{$e($l->kategori)}</Data></Cell>
      <Cell ss:StyleID=\"{$statusStyle}\"><Data ss:Type=\"String\">{$e($l->statusLabel())}</Data></Cell>
      <Cell ss:StyleID=\"{$styleCenter}\"><Data ss:Type=\"Number\">{$jmlTemuan}</Data></Cell>
    </Row>";
        }

        $total      = $laporans->count();
        $selesai    = $laporans->where('status', 'selesai')->count();
        $diproses   = $laporans->where('status', 'diproses')->count();
        $menunggu   = $laporans->where('status', 'menunggu_review')->count();
        $ditolak    = $laporans->where('status', 'ditolak')->count();
        $printedAt  = now()->format('d/m/Y H:i');

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
          xmlns:x="urn:schemas-microsoft-com:office:excel">
  <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
    <Title>Rekap Laporan Inspeksi</Title>
    <Author>RPN Supervisor System</Author>
    <Created>{$e(now()->toIso8601String())}</Created>
  </DocumentProperties>
  <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
    <WindowHeight>10000</WindowHeight>
    <WindowWidth>18000</WindowWidth>
    <ProtectStructure>False</ProtectStructure>
    <ProtectWindows>False</ProtectWindows>
  </ExcelWorkbook>
  <Styles>
{$styles}
  </Styles>
  <Worksheet ss:Name="Rekap Laporan">
    <Table ss:ExpandedColumnCount="9" ss:DefaultRowHeight="18">
      <Column ss:Width="30"/>
      <Column ss:Width="75"/>
      <Column ss:Width="120"/>
      <Column ss:Width="130"/>
      <Column ss:Width="130"/>
      <Column ss:Width="110"/>
      <Column ss:Width="100"/>
      <Column ss:Width="100"/>
      <Column ss:Width="60"/>

      <!-- Title rows -->
      <Row ss:Height="30">
        <Cell ss:MergeAcross="8" ss:StyleID="title">
          <Data ss:Type="String">REKAP LAPORAN INSPEKSI</Data>
        </Cell>
      </Row>
      <Row ss:Height="20">
        <Cell ss:MergeAcross="8" ss:StyleID="subtitle">
          <Data ss:Type="String">PT. RPN — Sistem Manajemen Laporan Inspeksi</Data>
        </Cell>
      </Row>
      <Row ss:Height="18">
        <Cell ss:MergeAcross="8" ss:StyleID="filter">
          <Data ss:Type="String">Filter: {$e($filterLabel)}    |    Dicetak: {$e($printedAt)}</Data>
        </Cell>
      </Row>
      <Row ss:Height="6"/>

      <!-- Summary row -->
      <Row ss:Height="20">
        <Cell ss:MergeAcross="1" ss:StyleID="filter"><Data ss:Type="String">Total: {$total} laporan</Data></Cell>
        <Cell ss:MergeAcross="1" ss:StyleID="badge_selesai"><Data ss:Type="String">Selesai: {$selesai}</Data></Cell>
        <Cell ss:MergeAcross="1" ss:StyleID="badge_diproses"><Data ss:Type="String">Diproses: {$diproses}</Data></Cell>
        <Cell ss:MergeAcross="1" ss:StyleID="badge_menunggu"><Data ss:Type="String">Menunggu: {$menunggu}</Data></Cell>
        <Cell ss:MergeAcross="2" ss:StyleID="badge_ditolak"><Data ss:Type="String">Ditolak: {$ditolak}</Data></Cell>
      </Row>
      <Row ss:Height="6"/>

      <!-- Header -->
      <Row ss:Height="28">
        <Cell ss:StyleID="th"><Data ss:Type="String">No</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">Tanggal</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">No. Laporan</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">Supervisor</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">Lokasi</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">Area</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">Kategori</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">Status</Data></Cell>
        <Cell ss:StyleID="th"><Data ss:Type="String">Temuan</Data></Cell>
      </Row>

      <!-- Data rows -->
{$rows}

      <!-- Footer -->
      <Row ss:Height="6"/>
      <Row ss:Height="18">
        <Cell ss:MergeAcross="8" ss:StyleID="footer_cell">
          <Data ss:Type="String">© {$e(date('Y'))} RPN Supervisor System — Dokumen ini digenerate otomatis pada {$e($printedAt)}</Data>
        </Cell>
      </Row>
    </Table>
    <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
      <PageSetup>
        <Layout x:Orientation="Landscape"/>
        <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
      </PageSetup>
      <FitToPage/>
      <Print>
        <FitWidth>1</FitWidth>
        <FitHeight>0</FitHeight>
        <ValidPrinterInfo/>
        <PaperSizeIndex>9</PaperSizeIndex>
      </Print>
      <Selected/>
      <FreezePanes/>
      <SplitHorizontal>7</SplitHorizontal>
      <TopRowBottomPane>7</TopRowBottomPane>
      <ActivePane>2</ActivePane>
    </WorksheetOptions>
  </Worksheet>
</Workbook>
XML;

        return $xml;
    }
}

