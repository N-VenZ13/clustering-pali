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
        Schema::create('indikators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desas')->cascadeOnDelete();
            $table->year('tahun_data');

            // 8 Variabel K-Means (Float karena bisa desimal/persentase/skor)
            $table->float('listrik_pln')->default(0);
            $table->float('fasilitas_ekonomi')->default(0);
            $table->float('fasilitas_pendidikan')->default(0);
            $table->float('akses_sma')->default(0);
            $table->float('faskes_desa')->default(0);
            $table->float('akses_puskesmas')->default(0);
            $table->float('kualitas_sinyal')->default(0);
            $table->float('keamanan_bencana')->default(0);

            // Hasil K-Means untuk Desa (1, 2, atau 3)
            $table->integer('klaster_hasil')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indikators');
    }
};
