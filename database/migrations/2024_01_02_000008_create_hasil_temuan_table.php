<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hasil_temuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temuan_id')->constrained('temuan')->cascadeOnDelete();
            $table->date('tanggal_selesai');
            $table->text('hasil_perbaikan');
            $table->text('catatan_akhir')->nullable();
            $table->string('foto_hasil')->nullable();
            $table->enum('status', ['selesai', 'belum_selesai'])->default('selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_temuan');
    }
};
