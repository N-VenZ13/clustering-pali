<?php

namespace App\Imports;

use App\Models\Indikator;
use Maatwebsite\Excel\Concerns\ToModel;

class IndikatorImport implements ToModel
{

    public function startRow(): int
    {
        return 2; // Mulai baca data dari baris ke-2
    }

    public function model(array $row)
    {
        // UpdateOrCreate: Jika data desa di tahun tersebut sudah ada, update. Jika belum, buat baru.
        return Indikator::updateOrCreate(
            [
                'desa_id' => $row[0],     // Kolom A di Excel (ID Desa)
                'tahun_data' => $row[1],  // Kolom B di Excel (Tahun)
            ],
            [
                'listrik_pln' => $row[2],           // Kolom C
                'fasilitas_ekonomi' => $row[3],     // Kolom D
                'fasilitas_pendidikan' => $row[4],  // Kolom E
                'akses_sma' => $row[5],             // Kolom F
                'faskes_desa' => $row[6],           // Kolom G
                'akses_puskesmas' => $row[7],       // Kolom H
                'kualitas_sinyal' => $row[8],       // Kolom I
                'keamanan_bencana' => $row[9],      // Kolom J
                'klaster_hasil' => null             // Kosongkan dulu sebelum di K-Means
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
