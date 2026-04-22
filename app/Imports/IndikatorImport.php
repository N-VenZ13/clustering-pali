<?php

namespace App\Imports;

use App\Models\Indikator;
use Maatwebsite\Excel\Concerns\ToModel;

class IndikatorImport implements ToModel
{
    protected $tahun;

    // Menangkap tahun dari Controller
    public function __construct($tahun)
    {
        $this->tahun = $tahun;
    }

    // WAJIB: Abaikan baris ke-1 (Judul Kolom), mulai dari baris ke-2
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        // 1. Jika baris kosong atau bukan angka, lewati
        if (!isset($row[0]) || !is_numeric($row[0])) {
            return null;
        }

        $id_desa = $row[0];

        // 2. TRIK BEST PRACTICE: Cek apakah desa ada?
        // Jika tidak ada, sistem akan membuat desa "dummy" otomatis 
        // agar proses upload tidak gagal (Foreign Key aman)
        \App\Models\Desa::firstOrCreate(
            ['id' => $id_desa],
            [
                'kecamatan_id' => 1, // Masukkan ke kecamatan id 1 secara default
                'nama_desa' => 'Desa Baru (ID: ' . $id_desa . ')' // Penanda bagi admin
            ]
        );

        // 3. Masukkan data ke indikator
        return Indikator::updateOrCreate(
            [
                'desa_id' => $id_desa,         
                'tahun_data' => $this->tahun,  
            ],
            [
                'listrik_pln'          => floatval($row[1] ?? 0),
                'fasilitas_ekonomi'    => floatval($row[2] ?? 0),
                'fasilitas_pendidikan' => floatval($row[3] ?? 0),
                'akses_sma'            => floatval($row[4] ?? 0),
                'faskes_desa'          => floatval($row[5] ?? 0),
                'akses_puskesmas'      => floatval($row[6] ?? 0),
                'kualitas_sinyal'      => floatval($row[7] ?? 0),
                'keamanan_bencana'     => floatval($row[8] ?? 0),
                'klaster_hasil'        => null
            ]
        );
    }

    

    // /**
    // * @param array $row
    // *
    // * @return \Illuminate\Database\Eloquent\Model|null
    // */
    // public function model(array $row)
    // {
    //     return new Indikator([
    //         //
    //     ]);
    // }
}
