<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TemuanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell, WithDrawings
{
    protected $temuans;

    public function __construct($temuans)
    {
        // Gunakan eager loading untuk fotoTemuans agar query lebih cepat
        $this->temuans = $temuans;
    }

    public function collection()
    {
        return $this->temuans;
    }

    public function startCell(): string
    {
        return 'A7';
    }

    // 1. TAMBAHKAN KOLOM "FOTO TEMUAN" PADA HEADINGS
    public function headings(): array
    {
        return ['Kode', 'Judul', 'No. Laporan', 'Lokasi', 'Risiko', 'Status', 'Tanggal', 'Foto Temuan'];
    }

    public function map($t): array
    {
        return [
            $t->kode_temuan,
            $t->judul_temuan,
            $t->laporan->nomor_laporan ?? '-',
            $t->laporan->lokasi->nama_lokasi ?? '-',
            ucfirst($t->tingkat_risiko),
            ucfirst($t->status),
            $t->created_at ? $t->created_at->format('d/m/Y H:i') : '-',
            '', // Kolom H dikosongkan karena akan diisi oleh objek Drawing Gambar
        ];
    }

    // 2. LOGIKA UNTUK MEMASUKKAN GAMBAR KE KOLOM H
    public function drawings()
    {
        $drawings = [];
        $row = 8; // Baris data dimulai dari baris ke-8

        foreach ($this->temuans as $temuan) {
            // Mengambil foto pertama dari relasi fotoTemuans (jika ada)
            $foto = $temuan->fotoTemuans->first();

            if ($foto && $foto->foto) {
                // Tentukan full path lokasi penyimpanan gambar kamu (misal di folder public/storage)
                $pathGambar = public_path('storage/' . $foto->foto);

                // Cek apakah file gambarnya benar-benar ada di lokal server/Laragon
                if (file_exists($pathGambar)) {
                    $drawing = new Drawing();
                    $drawing->setName('Foto Temuan');
                    $drawing->setDescription($temuan->kode_temuan);
                    $drawing->setPath($pathGambar);
                    $drawing->setHeight(50); // Set tinggi gambar agar muat di dalam cell (dalam pixel)
                    $drawing->setCoordinates('H' . $row); // Tempatkan di kolom H
                    $drawing->setOffsetX(10); // Geser sedikit ke kanan agar presisi di tengah
                    $drawing->setOffsetY(5);  // Geser sedikit ke bawah
                    
                    $drawings[] = $drawing;
                }
            }
            $row++;
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // ====================================================================
        // PENGATURAN HALAMAN PRINT CETAK (A4 & LANDSCAPE) - SOLUSI FIX ERROR
        // ====================================================================
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        
        // Fit to Page: Memaksa lebar tabel muat dalam 1 halaman kesamping (lebar kertas)
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0); // 0 berarti baris data ke bawah bisa otomatis lanjut ke halaman 2, 3, dst.
        
        // FIX: Menggunakan string view mode yang valid secara langsung tanpa konstanta class
        $sheet->getSheetView()->setView('pageBreakPreview');
        // ====================================================================

        // MERGE & JUDUL ATAS (A1 - H1)
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'REKAP DATA TEMUAN INSPEKSI');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1F4E78'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'PT. RPN — Supervisor Management System');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('595959'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A4', 'Dicetak: ' . now()->format('d/m/Y H:i'));
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(9)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('7F7F7F'));

        // STYLING HEADER TABEL (A7 - H7)
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2F5597']],
        ];
        $sheet->getStyle('A7:H7')->applyFromArray($headerStyle);
        $sheet->getRowDimension(7)->setRowHeight(28);

        // STYLING BORDER BARIS DATA
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9'],
                ],
            ],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        $sheet->getStyle('A8:H' . $highestRow)->applyFromArray($styleArray);

        // ALIGNMENT KOLOM
        $sheet->getStyle('A8:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C8:H' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // WARNAI BADGE STATUS DAN UKURAN TINGGI BARIS KHUSUS (Disesuaikan untuk foto)
        for ($row = 8; $row <= $highestRow; $row++) {
            // Set tinggi baris lebih tinggi (45px) agar gambar thumbnail-nya kelihatan jelas dan rapi
            $sheet->getRowDimension($row)->setRowHeight(45);
            
            $statusValue = strtolower($sheet->getCell('F' . $row)->getValue());
            $badgeStyle = ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID]];

            if ($statusValue == 'selesai') {
                $badgeStyle['font']['color'] = ['rgb' => '385723'];
                $badgeStyle['fill']['startColor'] = ['rgb' => 'E2EFDA'];
            } elseif (in_array($statusValue, ['diproses', 'proses'])) {
                $badgeStyle['font']['color'] = ['rgb' => '002060'];
                $badgeStyle['fill']['startColor'] = ['rgb' => 'DDEBF7'];
            } elseif (in_array($statusValue, ['menunggu', 'menunggu_review'])) {
                $badgeStyle['font']['color'] = ['rgb' => '7F6000'];
                $badgeStyle['fill']['startColor'] = ['rgb' => 'FFF2CC'];
            } else {
                $badgeStyle['font']['color'] = ['rgb' => 'C00000'];
                $badgeStyle['fill']['startColor'] = ['rgb' => 'FCE4D6'];
            }

            $sheet->getStyle('F' . $row)->applyFromArray($badgeStyle);
        }

        return [];
    }
}