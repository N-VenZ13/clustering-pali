<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Kecamatan;
use App\Models\Laporan;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        // 1. HANYA ambil tahun yang Laporannya sudah di "Accepted" oleh Pimpinan
        $list_tahun = Laporan::where('status', 'accepted')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        // Jika belum ada satupun yang di ACC, kembalikan ke halaman kosong/maintenance
        if (empty($list_tahun)) {
            return view('welcome_empty');
        }

        $tahun_aktif = $request->tahun ?? $list_tahun[0];

        // 2. Ambil data dengan dinamis perhitungan agregasi
        $kecamatans = Kecamatan::with(['desas.indikators' => function ($q) use ($tahun_aktif) {
            $q->where('tahun_data', $tahun_aktif);
        }])->get();

        // Hitung Agregasi Status Kecamatan On The Fly (Metode MODUS)
        foreach ($kecamatans as $kecamatan) {
            $klaster_counts = [1 => 0, 2 => 0, 3 => 0];
            $jmlDesa = 0;

            foreach ($kecamatan->desas as $desa) {
                $ind = $desa->indikators->first();
                if ($ind && $ind->klaster_hasil) {
                    $klaster_counts[$ind->klaster_hasil]++;
                    $jmlDesa++;
                }
            }

            if ($jmlDesa > 0) {
                // Cari Modus (Key array dengan value/jumlah tertinggi)
                $modus_klaster = array_keys($klaster_counts, max($klaster_counts))[0];

                if ($modus_klaster == 1) $status = 'Sejahtera';
                elseif ($modus_klaster == 2) $status = 'Berkembang';
                else $status = 'Perlu Perhatian';

                $kecamatan->status_akhir = $status;
                // Untuk Modus, skor agregasi bisa kita ubah jadi persentase dominasi
                $kecamatan->skor_agregasi = round((max($klaster_counts) / $jmlDesa) * 100, 2) . '%';
            } else {
                $kecamatan->status_akhir = null;
            }
        }

        return view('welcome', compact('kecamatans', 'list_tahun', 'tahun_aktif'));
    }
}
