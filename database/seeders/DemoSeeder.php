<?php

namespace Database\Seeders;

use App\Models\CheckIn;
use App\Models\FotoLaporan;
use App\Models\FotoTemuan;
use App\Models\LaporanInspeksi;
use App\Models\Lokasi;
use App\Models\Temuan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoSeeder extends Seeder
{
    /**
     * Generate a placeholder selfie/photo image using PHP GD
     * and store it in Laravel storage, returning the relative path.
     */
    private function makePlaceholderImage(
        string $disk,
        string $folder,
        string $filename,
        string $bgHex,
        string $label,
        string $sublabel = ''
    ): string {
        $w = 640; $h = 480;
        $img = imagecreatetruecolor($w, $h);

        // ── Background gradient ──────────────────────────────
        [$r1, $g1, $b1] = sscanf($bgHex, '#%02x%02x%02x');
        $r2 = max(0, $r1 - 40); $g2 = max(0, $g1 - 40); $b2 = max(0, $b1 - 40);
        for ($y = 0; $y < $h; $y++) {
            $ratio = $y / $h;
            $r = (int)($r1 + ($r2 - $r1) * $ratio);
            $g = (int)($g1 + ($g2 - $g1) * $ratio);
            $b = (int)($b1 + ($b2 - $b1) * $ratio);
            $col = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $col);
        }

        // ── Grid overlay ─────────────────────────────────────
        $grid = imagecolorallocatealpha($img, 255, 255, 255, 110);
        for ($x = 0; $x < $w; $x += 40) imageline($img, $x, 0, $x, $h, $grid);
        for ($y = 0; $y < $h; $y += 40) imageline($img, 0, $y, $w, $y, $grid);

        // ── Centre circle (avatar placeholder) ───────────────
        $cx = $w / 2; $cy = $h / 2 - 40;
        $circleColor = imagecolorallocatealpha($img, 255, 255, 255, 60);
        imagefilledellipse($img, (int)$cx, (int)$cy, 160, 160, $circleColor);
        $personColor = imagecolorallocatealpha($img, 255, 255, 255, 30);
        // Head
        imagefilledellipse($img, (int)$cx, (int)$cy - 30, 60, 60, $personColor);
        // Body
        imagefilledrectangle($img, (int)$cx - 35, (int)$cy + 2, (int)$cx + 35, (int)$cy + 70, $personColor);

        // ── Labels ───────────────────────────────────────────
        $white  = imagecolorallocate($img, 255, 255, 255);
        $shadow = imagecolorallocatealpha($img, 0, 0, 0, 80);

        // Main label (simulate large bold text with thick lines)
        $fontSize  = 5; // GD built-in font size (1–5)
        $charW     = imagefontwidth($fontSize);
        $charH     = imagefontheight($fontSize);
        $textW     = strlen($label) * $charW;
        $textX     = (int)(($w - $textW) / 2);
        $textY     = $h - 120;
        imagestring($img, $fontSize, $textX + 1, $textY + 1, $label, $shadow);
        imagestring($img, $fontSize, $textX, $textY, $label, $white);

        if ($sublabel) {
            $smallFont = 3;
            $sw  = strlen($sublabel) * imagefontwidth($smallFont);
            $sx  = (int)(($w - $sw) / 2);
            $sy  = $textY + $charH + 8;
            imagestring($img, $smallFont, $sx + 1, $sy + 1, $sublabel, $shadow);
            imagestring($img, $smallFont, $sx, $sy, $sublabel, $white);
        }

        // ── Timestamp watermark ───────────────────────────────
        $ts      = '[DEMO] ' . now()->format('d/m/Y H:i');
        $tsFont  = 2;
        $tsW     = strlen($ts) * imagefontwidth($tsFont);
        imagestring($img, $tsFont, $w - $tsW - 6, $h - 18, $ts, $shadow);
        imagestring($img, $tsFont, $w - $tsW - 7, $h - 19, $ts, $white);

        // ── Save ─────────────────────────────────────────────
        ob_start();
        imagejpeg($img, null, 85);
        $jpeg = ob_get_clean();
        imagedestroy($img);

        $path = $folder . '/' . $filename;
        Storage::disk($disk)->put($path, $jpeg);

        return $path;
    }

    public function run(): void
    {
        $supervisor = User::where('email', 'supervisor@rpn.com')->firstOrFail();
        $admin      = User::where('email', 'admin@rpn.com')->firstOrFail();
        $lokasi1    = Lokasi::where('kode_lokasi', 'GDG-A')->firstOrFail();
        $lokasi2    = Lokasi::where('kode_lokasi', 'WSH-01')->firstOrFail();

        // ── 1. Selfie placeholder images ─────────────────────
        $selfie1 = $this->makePlaceholderImage(
            'public', 'selfie',
            'demo-selfie-1.jpg',
            '#2c5f9e',
            'SELFIE CHECK-IN DEMO',
            'Gudang A — ' . now()->subDays(10)->format('d/m/Y')
        );
        $selfie2 = $this->makePlaceholderImage(
            'public', 'selfie',
            'demo-selfie-2.jpg',
            '#1a6b4a',
            'SELFIE CHECK-IN DEMO',
            'Workshop — ' . now()->subDays(3)->format('d/m/Y')
        );

        // ── 2. CheckIn records ───────────────────────────────
        $checkin1 = CheckIn::create([
            'user_id'        => $supervisor->id,
            'lokasi_id'      => $lokasi1->id,
            'latitude'       => -6.200050,
            'longitude'      => 106.816720,
            'foto_selfie'    => $selfie1,
            'waktu_checkin'  => now()->subDays(10)->setTime(8, 15),
            'waktu_checkout' => now()->subDays(10)->setTime(12, 45),
            'status'         => 'tidak_aktif',
        ]);

        $checkin2 = CheckIn::create([
            'user_id'        => $supervisor->id,
            'lokasi_id'      => $lokasi2->id,
            'latitude'       => -6.202010,
            'longitude'      => 106.818700,
            'foto_selfie'    => $selfie2,
            'waktu_checkin'  => now()->subDays(3)->setTime(9, 0),
            'waktu_checkout' => now()->subDays(3)->setTime(14, 30),
            'status'         => 'tidak_aktif',
        ]);

        // ── 3. Laporan foto placeholder ───────────────────────
        $fotoColors = [
            ['#d35400', 'FOTO DOKUMENTASI', 'Area Gudang A'],
            ['#8e44ad', 'FOTO DOKUMENTASI', 'Kondisi Lantai'],
            ['#c0392b', 'FOTO DOKUMENTASI', 'Workshop'],
            ['#16a085', 'FOTO DOKUMENTASI', 'Area Mesin'],
        ];
        $fotoPaths = [];
        foreach ($fotoColors as $i => [$color, $lbl, $sub]) {
            $fotoPaths[] = $this->makePlaceholderImage(
                'public', 'laporan',
                "demo-foto-laporan-{$i}.jpg",
                $color, $lbl, $sub
            );
        }

        // ── 4. Laporan 1 — Keselamatan Gudang A (Diproses) ───
        $laporan1 = LaporanInspeksi::create([
            'nomor_laporan'   => 'LPI-' . now()->subDays(10)->format('Ymd') . '-001',
            'user_id'         => $supervisor->id,
            'lokasi_id'       => $lokasi1->id,
            'checkin_id'      => $checkin1->id,
            'tanggal_inspeksi'=> now()->subDays(10)->toDateString(),
            'area'            => 'Gudang Penyimpanan Bahan Baku — Lantai 1',
            'kategori'        => 'Keselamatan',
            'deskripsi'       =>
                'Inspeksi rutin keselamatan kerja di area gudang penyimpanan bahan baku lantai 1. ' .
                'Ditemukan beberapa kondisi yang berpotensi menimbulkan risiko keselamatan bagi tenaga kerja ' .
                'dan perlu segera ditindaklanjuti. Inspeksi dilakukan secara menyeluruh meliputi ' .
                'kondisi jalan evakuasi, penyimpanan bahan, dan kondisi peralatan pemadam kebakaran.',
            'status'          => 'diproses',
            'catatan_approval'=> 'Laporan telah diverifikasi. Tim K3 segera menindaklanjuti seluruh temuan.',
            'approved_by'     => $admin->id,
            'approved_at'     => now()->subDays(9),
        ]);

        // Foto laporan 1
        FotoLaporan::create(['laporan_id' => $laporan1->id, 'foto' => $fotoPaths[0]]);
        FotoLaporan::create(['laporan_id' => $laporan1->id, 'foto' => $fotoPaths[1]]);

        // Temuan laporan 1
        $temuan1a = Temuan::create([
            'laporan_id'    => $laporan1->id,
            'kode_temuan'   => 'TMN-' . now()->subDays(10)->format('Ymd') . '-001',
            'judul_temuan'  => 'Jalur Evakuasi Terblokir Tumpukan Barang',
            'deskripsi'     =>
                'Jalur evakuasi darurat di sisi barat gudang terblokir oleh tumpukan kardus dan palet kayu. ' .
                'Lebar jalur yang tersisa hanya ±40 cm dari standar minimal 90 cm yang disyaratkan. ' .
                'Kondisi ini sangat berbahaya karena akan menghambat evakuasi saat terjadi kebakaran atau keadaan darurat.',
            'tingkat_risiko'=> 'tinggi',
            'rekomendasi'   =>
                '1. Segera pindahkan seluruh barang yang menghalangi jalur evakuasi dalam waktu 1×24 jam. ' .
                '2. Pasang marka lantai permanen (garis kuning) batas jalur evakuasi minimal 1 meter. ' .
                '3. Pasang rambu larangan penempatan barang di jalur evakuasi. ' .
                '4. Lakukan inspeksi harian oleh petugas gudang.',
            'status'        => 'diproses',
        ]);

        $temuan1b = Temuan::create([
            'laporan_id'    => $laporan1->id,
            'kode_temuan'   => 'TMN-' . now()->subDays(10)->format('Ymd') . '-002',
            'judul_temuan'  => 'APAR Kedaluwarsa dan Tidak Pada Posisi',
            'deskripsi'     =>
                'Ditemukan 3 unit Alat Pemadam Api Ringan (APAR) dengan masa berlaku yang telah habis ' .
                '(expired Maret 2024). Selain itu, 1 unit APAR tidak terpasang pada bracket standar ' .
                'dan diletakkan di lantai di belakang rak penyimpanan sehingga sulit dijangkau saat darurat.',
            'tingkat_risiko'=> 'tinggi',
            'rekomendasi'   =>
                '1. Ganti segera 3 unit APAR yang telah kedaluwarsa dengan unit baru. ' .
                '2. Pasang kembali APAR pada bracket dinding dengan ketinggian standar (15–120 cm dari lantai). ' .
                '3. Lakukan pemeriksaan APAR setiap 6 bulan dan catat di kartu inspeksi. ' .
                '4. Jadwalkan pelatihan penggunaan APAR untuk seluruh karyawan gudang.',
            'status'        => 'diproses',
        ]);

        $temuan1c = Temuan::create([
            'laporan_id'    => $laporan1->id,
            'kode_temuan'   => 'TMN-' . now()->subDays(10)->format('Ymd') . '-003',
            'judul_temuan'  => 'Pencahayaan Area Penyimpanan Tidak Memadai',
            'deskripsi'     =>
                'Intensitas cahaya di area rak B3 dan B4 terukur hanya 50–80 lux, ' .
                'jauh di bawah standar minimum 200 lux untuk area kerja penyimpanan aktif. ' .
                '2 unit lampu LED pada rak B3 dalam kondisi mati dan belum diganti.',
            'tingkat_risiko'=> 'sedang',
            'rekomendasi'   =>
                '1. Ganti 2 unit lampu LED yang mati dalam waktu 3 hari kerja. ' .
                '2. Tambahkan 2 titik lampu baru di area rak B4 untuk mencapai standar 200 lux. ' .
                '3. Lakukan audit pencahayaan rutin setiap 3 bulan.',
            'status'        => 'menunggu_review',
        ]);

        // Foto temuan 1a
        $fotoTemuan1 = $this->makePlaceholderImage('public','temuan','demo-temuan-1.jpg','#922b21','FOTO TEMUAN','Jalur Evakuasi');
        FotoTemuan::create(['temuan_id' => $temuan1a->id, 'foto' => $fotoTemuan1]);

        // ── 5. Laporan 2 — Peralatan Workshop (Selesai) ──────
        $laporan2 = LaporanInspeksi::create([
            'nomor_laporan'   => 'LPI-' . now()->subDays(3)->format('Ymd') . '-001',
            'user_id'         => $supervisor->id,
            'lokasi_id'       => $lokasi2->id,
            'checkin_id'      => $checkin2->id,
            'tanggal_inspeksi'=> now()->subDays(3)->toDateString(),
            'area'            => 'Workshop Pemeliharaan Mesin — Lantai 2',
            'kategori'        => 'Peralatan',
            'deskripsi'       =>
                'Inspeksi kondisi peralatan dan mesin produksi di workshop pemeliharaan lantai 2. ' .
                'Pemeriksaan mencakup kondisi fisik mesin, sistem kelistrikan, alat pelindung diri, ' .
                'serta kebersihan dan kerapian area kerja. Terdapat beberapa temuan ringan yang ' .
                'sudah dapat ditindaklanjuti langsung oleh teknisi pada hari yang sama.',
            'status'          => 'selesai',
            'catatan_approval'=> 'Semua temuan telah ditindaklanjuti dan diverifikasi. Laporan dinyatakan selesai.',
            'approved_by'     => $admin->id,
            'approved_at'     => now()->subDays(2),
        ]);

        // Foto laporan 2
        FotoLaporan::create(['laporan_id' => $laporan2->id, 'foto' => $fotoPaths[2]]);
        FotoLaporan::create(['laporan_id' => $laporan2->id, 'foto' => $fotoPaths[3]]);

        // Temuan laporan 2
        $temuan2a = Temuan::create([
            'laporan_id'    => $laporan2->id,
            'kode_temuan'   => 'TMN-' . now()->subDays(3)->format('Ymd') . '-001',
            'judul_temuan'  => 'Pelindung Mesin Bubut Tidak Terpasang',
            'deskripsi'     =>
                'Mesin bubut CNC unit B-07 beroperasi tanpa pelindung/guard pada bagian chuck dan spindle. ' .
                'Guard dilepas oleh operator dengan alasan "mempermudah penggantian material" namun ' .
                'tidak dipasang kembali. Kondisi ini berisiko tinggi menyebabkan kecelakaan kerja ' .
                'seperti terlilit atau terpotong bagian yang berputar.',
            'tingkat_risiko'=> 'tinggi',
            'rekomendasi'   =>
                '1. Hentikan operasional mesin bubut B-07 hingga guard dipasang kembali. ' .
                '2. Pasang prosedur tertulis larangan melepas guard mesin. ' .
                '3. Berikan pelatihan ulang keselamatan mesin kepada seluruh operator. ' .
                '4. Pasang interlock sensor yang menghentikan mesin otomatis jika guard dilepas.',
            'status'        => 'selesai',
        ]);

        $temuan2b = Temuan::create([
            'laporan_id'    => $laporan2->id,
            'kode_temuan'   => 'TMN-' . now()->subDays(3)->format('Ymd') . '-002',
            'judul_temuan'  => 'Kebocoran Oli Hidrolik pada Kompresor Unit C-03',
            'deskripsi'     =>
                'Ditemukan genangan oli hidrolik di bawah kompresor unit C-03 berukuran sekitar 30×20 cm. ' .
                'Sumber kebocoran berasal dari sambungan selang bertekanan tinggi yang telah aus. ' .
                'Kondisi ini dapat menyebabkan lantai licin dan risiko kebakaran jika terkena percikan api.',
            'tingkat_risiko'=> 'sedang',
            'rekomendasi'   =>
                '1. Ganti selang hidrolik yang bocor dengan selang baru sesuai spesifikasi. ' .
                '2. Bersihkan tumpahan oli dengan absorben dan buang sesuai prosedur limbah B3. ' .
                '3. Pasang drip tray permanen di bawah kompresor. ' .
                '4. Jadwalkan penggantian selang hidrolik setiap 12 bulan.',
            'status'        => 'selesai',
        ]);

        $temuan2c = Temuan::create([
            'laporan_id'    => $laporan2->id,
            'kode_temuan'   => 'TMN-' . now()->subDays(3)->format('Ymd') . '-003',
            'judul_temuan'  => 'APD Tidak Lengkap pada Area Pengelasan',
            'deskripsi'     =>
                '2 dari 4 pekerja pengelasan di area sudut barat tidak menggunakan kacamata las (face shield) ' .
                'dan hanya menggunakan kacamata biasa. Sarung tangan las milik 1 orang terlihat sudah ' .
                'sobek dan tidak layak pakai. Tidak ditemukan stok APD cadangan di area kerja.',
            'tingkat_risiko'=> 'tinggi',
            'rekomendasi'   =>
                '1. Hentikan pekerjaan pengelasan tanpa APD lengkap. ' .
                '2. Sediakan stok face shield dan sarung tangan las di setiap area pengelasan. ' .
                '3. Terapkan sistem check-out APD dengan pencatatan kondisi sebelum dan sesudah pakai. ' .
                '4. Ganti APD yang rusak/tidak layak pakai segera.',
            'status'        => 'selesai',
        ]);

        $temuan2d = Temuan::create([
            'laporan_id'    => $laporan2->id,
            'kode_temuan'   => 'TMN-' . now()->subDays(3)->format('Ymd') . '-004',
            'judul_temuan'  => 'Label Identifikasi Kimia Bahan Pelarut Tidak Terpasang',
            'deskripsi'     =>
                'Terdapat 6 drum bahan pelarut (thinner industri) yang tidak memiliki label identifikasi ' .
                'GHS/SDS yang lengkap. Label lama sebagian telah pudar dan tidak terbaca. ' .
                'Hal ini menyulitkan identifikasi bahaya dan penanganan pertolongan pertama jika terjadi paparan.',
            'tingkat_risiko'=> 'rendah',
            'rekomendasi'   =>
                '1. Pasang label GHS baru pada seluruh 6 drum pelarut dalam 2 hari kerja. ' .
                '2. Simpan lembar data keselamatan (SDS) di dekat area penyimpanan. ' .
                '3. Lakukan inventarisasi seluruh bahan kimia di workshop setiap 6 bulan.',
            'status'        => 'selesai',
        ]);

        // Foto temuan 2a
        $fotoTemuan2 = $this->makePlaceholderImage('public','temuan','demo-temuan-2.jpg','#1a5276','FOTO TEMUAN','Mesin Bubut B-07');
        FotoTemuan::create(['temuan_id' => $temuan2a->id, 'foto' => $fotoTemuan2]);

        $this->command->info('✅  Demo data berhasil dibuat:');
        $this->command->info("    - 2 CheckIn (Gudang A + Workshop)");
        $this->command->info("    - Laporan 1: {$laporan1->nomor_laporan} (Keselamatan Gudang A — Diproses) — 3 temuan");
        $this->command->info("    - Laporan 2: {$laporan2->nomor_laporan} (Peralatan Workshop — Selesai) — 4 temuan");
        $this->command->info("    - 4 foto laporan, 2 foto temuan (placeholder images)");
    }
}
