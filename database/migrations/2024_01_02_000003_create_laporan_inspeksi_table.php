<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_inspeksi', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_laporan')->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('lokasi_id')->constrained('lokasi')->cascadeOnDelete();
            $table->foreignId('checkin_id')->constrained('checkin')->cascadeOnDelete();
            $table->date('tanggal_inspeksi');
            $table->string('area');
            $table->string('kategori');
            $table->text('deskripsi');
            $table->enum('status', ['menunggu_review', 'diproses', 'selesai', 'ditolak'])->default('menunggu_review');
            $table->text('catatan_approval')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_inspeksi');
    }
};
