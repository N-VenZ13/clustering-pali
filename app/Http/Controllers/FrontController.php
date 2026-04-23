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

        // 3. Hitung Agregasi Status Kecamatan On The Fly
        foreach ($kecamatans as $kecamatan) {
            $totalSkor = 0;
            $jmlDesa = 0;
            foreach ($kecamatan->desas as $desa) {
                $ind = $desa->indikators->first();
                if ($ind && $ind->klaster_hasil) {
                    $totalSkor += $ind->klaster_hasil;
                    $jmlDesa++;
                }
            }

            if ($jmlDesa > 0) {
                $rataRata = $totalSkor / $jmlDesa;
                if ($rataRata < 1.67) $status = 'Sejahtera';
                elseif ($rataRata <= 2.33) $status = 'Berkembang';
                else $status = 'Perlu Perhatian';

                // Tempelkan secara virtual untuk dikirim ke WebGIS
                $kecamatan->status_akhir = $status;
                $kecamatan->skor_agregasi = round($rataRata, 2);
            } else {
                $kecamatan->status_akhir = null;
            }
        }

        return view('welcome', compact('kecamatans', 'list_tahun', 'tahun_aktif'));
    }
}
