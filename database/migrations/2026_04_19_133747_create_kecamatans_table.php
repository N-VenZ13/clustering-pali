<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kecamatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kecamatan');
            $table->longText('file_geojson')->nullable(); // Untuk menyimpan raw string geojson/path file
            
            // Hasil Agregasi dari Desa (Bottom-Up)
            $table->float('skor_agregasi')->nullable(); 
            $table->string('status_akhir')->nullable(); // Sejahtera / Berkembang / Perlu Perhatian
            $table->enum('status_validasi', ['draft', 'accepted', 'rejected'])->default('draft');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kecamatans');
    }
};
