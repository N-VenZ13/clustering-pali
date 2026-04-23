<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique(); // 1 Laporan per Tahun
            $table->enum('status', ['draft', 'pending', 'accepted', 'rejected'])->default('draft');
            $table->timestamp('dikunci_pada')->nullable(); // Waktu Pimpinan ACC
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
