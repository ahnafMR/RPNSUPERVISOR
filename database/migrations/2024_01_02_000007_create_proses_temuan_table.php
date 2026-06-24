<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proses_temuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temuan_id')->constrained('temuan')->cascadeOnDelete();
            $table->date('tanggal_proses');
            $table->string('pic');
            $table->text('tindakan');
            $table->text('catatan')->nullable();
            $table->string('foto_proses')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proses_temuan');
    }
};
