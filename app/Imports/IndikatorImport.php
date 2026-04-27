<?php

namespace App\Imports;

use App\Models\Desa;
use App\Models\Indikator;
use App\Models\Kecamatan;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class IndikatorImport implements ToModel, WithStartRow
{
    protected $tahun;

    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    public function startRow(): int
    {
        return 2; // Mulai baris 2 (abaikan header)
    }

    public function model(array $row)
    {
        // 1. Cek apakah baris kosong atau ini adalah baris Header yang terbaca
        if (!isset($row[0]) || strtolower(trim($row[0])) === 'nama_kecamatan') {
            return null;
        }

        // Ambil nama dari Excel, gunakan trim() untuk menghapus spasi tidak sengaja
        $nama_kecamatan = trim($row[0]);
        $nama_desa      = trim($row[1]);

        // 2. SMART IMPORT: Cari atau Buat Kecamatan Otomatis
        $kecamatan = Kecamatan::firstOrCreate(
            ['nama_kecamatan' => $nama_kecamatan],
            ['status_validasi' => 'draft']
        );

        // 3. SMART IMPORT: Cari atau Buat Desa (disambung ke Kecamatan di atas)
        $desa = Desa::firstOrCreate(
            [
                'nama_desa' => $nama_desa,
                'kecamatan_id' => $kecamatan->id
            ]
        );

        // 4. Masukkan angka ke tabel Indikator (Disambung ke ID Desa yang baru saja dicek/dibuat)
        return Indikator::updateOrCreate(
            [
                'desa_id' => $desa->id,         
                'tahun_data' => $this->tahun,  
            ],
            [
                'listrik_pln'          => floatval(str_replace(',', '.', $row[2] ?? 0)), // Kolom C (Ubah koma jadi titik jika ada)
                'fasilitas_ekonomi'    => floatval(str_replace(',', '.', $row[3] ?? 0)), // Kolom D
                'fasilitas_pendidikan' => floatval(str_replace(',', '.', $row[4] ?? 0)), // Kolom E
                'akses_sma'            => floatval(str_replace(',', '.', $row[5] ?? 0)), // Kolom F
                'faskes_desa'          => floatval(str_replace(',', '.', $row[6] ?? 0)), // Kolom G
                'akses_puskesmas'      => floatval(str_replace(',', '.', $row[7] ?? 0)), // Kolom H
                'kualitas_sinyal'      => floatval(str_replace(',', '.', $row[8] ?? 0)), // Kolom I
                'keamanan_bencana'     => floatval(str_replace(',', '.', $row[9] ?? 0)), // Kolom J
                'klaster_hasil'        => null
            ]
        );
    }
}
