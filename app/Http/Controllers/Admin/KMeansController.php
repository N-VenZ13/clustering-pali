<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Indikator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndikatorImport;
use App\Services\KMeansService;

class KMeansController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil semua tahun unik yang ada di database
        $list_tahun = Indikator::select('tahun_data')->distinct()->orderBy('tahun_data', 'desc')->pluck('tahun_data')->toArray();

        // Jika database masih kosong sama sekali, beri default tahun sekarang
        if (empty($list_tahun)) {
            $list_tahun = [date('Y')];
        }

        // 2. Tahun aktif = Tahun dari request, ATAU tahun terbaru dari database
        $tahun_aktif = $request->tahun ?? $list_tahun[0];

        $data_desa = Indikator::with('desa')->where('tahun_data', $tahun_aktif)->get();

        $summary = [
            'klaster_1' => $data_desa->where('klaster_hasil', 1)->count(),
            'klaster_2' => $data_desa->where('klaster_hasil', 2)->count(),
            'klaster_3' => $data_desa->where('klaster_hasil', 3)->count(),
            'total' => $data_desa->count()
        ];

        // Kirim $list_tahun ke view
        return view('admin.kmeans.index', compact('data_desa', 'tahun_aktif', 'summary', 'list_tahun'));
    }

    

    public function importExcel(Request $request)
    {
        // Validasi pastikan file dan tahun ada
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls',
            'tahun' => 'required|integer'
        ]);

        try {
            // Kita kirim tahunnya ke dalam IndikatorImport
            Excel::import(new IndikatorImport($request->tahun), $request->file('file_excel'));

            return redirect()->back()->with('success', 'Data Indikator berhasil di-import!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan format Excel: ' . $e->getMessage());
        }
    }

    public function prosesKMeans(Request $request, KMeansService $kMeansService)
    {
        $tahun = $request->tahun ?? 2024;

        $berhasil = $kMeansService->process($tahun);

        if ($berhasil) {
            return redirect()->back()->with('success', 'Algoritma K-Means berhasil dijalankan! Hasil klaster telah diupdate.');
        } else {
            return redirect()->back()->with('error', 'Gagal memproses. Data desa kurang dari 3 untuk tahun ini.');
        }
    }

    public function simpanAgregasi(Request $request)
    {
        $tahun = $request->tahun;

        // Ambil semua kecamatan beserta data desa dan indikatornya di tahun tersebut
        $kecamatans = \App\Models\Kecamatan::with(['desas.indikators' => function ($query) use ($tahun) {
            $query->where('tahun_data', $tahun);
        }])->get();

        foreach ($kecamatans as $kecamatan) {
            $totalSkor = 0;
            $jumlahDesaDiproses = 0;

            // Loop semua desa di dalam kecamatan ini
            foreach ($kecamatan->desas as $desa) {
                // Ambil indikator tahun yang dipilih
                $indikator = $desa->indikators->first();

                // Jika desa ini sudah punya hasil klaster (1, 2, atau 3)
                if ($indikator && $indikator->klaster_hasil != null) {
                    $totalSkor += $indikator->klaster_hasil;
                    $jumlahDesaDiproses++;
                }
            }

            // Jika ada desa yang diproses, hitung rata-ratanya
            if ($jumlahDesaDiproses > 0) {
                $rataRata = $totalSkor / $jumlahDesaDiproses;

                // Tentukan Status Kecamatan berdasarkan nilai rata-rata (Weighted Average)
                // Klaster 1 = Sejahtera, 2 = Berkembang, 3 = Perhatian
                if ($rataRata < 1.67) {
                    $status = 'Sejahtera';
                } elseif ($rataRata >= 1.67 && $rataRata <= 2.33) {
                    $status = 'Berkembang';
                } else {
                    $status = 'Perlu Perhatian';
                }

                // Update data kecamatan
                $kecamatan->update([
                    'skor_agregasi' => round($rataRata, 2),
                    'status_akhir' => $status,
                    'status_validasi' => 'draft' // Menunggu approval Pimpinan
                ]);
            }
        }

        return redirect()->back()->with('success', 'Agregasi berhasil! Status Kecamatan telah diupdate dan masuk ke draf Laporan Pimpinan.');
    }
}
