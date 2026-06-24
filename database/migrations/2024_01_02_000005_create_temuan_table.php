<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan_inspeksi')->cascadeOnDelete();
            $table->string('kode_temuan')->unique();
            $table->string('judul_temuan');
            $table->text('deskripsi');
            $table->enum('tingkat_risiko', ['rendah', 'sedang', 'tinggi']);
            $table->text('rekomendasi')->nullable();
            $table->enum('status', ['menunggu_review', 'diproses', 'selesai', 'ditolak'])->default('menunggu_review');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temuan');
    }
};
