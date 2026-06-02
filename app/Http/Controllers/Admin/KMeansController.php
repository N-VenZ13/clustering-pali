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
        // 1. Ambil tahun-tahun yang sudah ada di database (Mulai 2024)
        $db_years = Indikator::where('tahun_data', '>=', 2024)
                             ->select('tahun_data')->distinct()->pluck('tahun_data')->toArray();
        
        // 2. Buat daftar tahun dari 2024 sampai tahun depan (Maksimal masa depan +1)
        $current_year = max((int) date('Y'), 2024);
        $default_years = range($current_year + 1, 2024); // Menghasilkan array [2027, 2026, 2025, 2024]

        // 3. Gabungkan tahun dari DB dan tahun default, hapus duplikat
        $list_tahun = array_unique(array_merge($db_years, $default_years));
        rsort($list_tahun);

        $tahun_aktif = $request->tahun ?? $current_year;

        // Ambil data untuk tabel
        $data_desa = Indikator::with('desa')->where('tahun_data', $tahun_aktif)->get();

        // Hitung summary
        $summary = [
            'klaster_1' => $data_desa->where('klaster_hasil', 1)->count(),
            'klaster_2' => $data_desa->where('klaster_hasil', 2)->count(),
            'klaster_3' => $data_desa->where('klaster_hasil', 3)->count(),
            'total' => $data_desa->count()
        ];

        // TAMBAHKAN BARIS INI: Cek status laporan tahun aktif
        $laporan_aktif = \App\Models\Laporan::where('tahun', $tahun_aktif)->first();

        return view('admin.kmeans.index', compact('data_desa', 'tahun_aktif', 'summary', 'list_tahun', 'laporan_aktif'));
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

        $laporan = \App\Models\Laporan::where('tahun', $tahun)->first();
        if ($laporan && $laporan->status === 'accepted') {
            return redirect()->back()->with('error', 'DATA TERKUNCI! Laporan tahun ' . $tahun . ' sudah disetujui Pimpinan.');
        }

        // Buat atau Update Laporan jadi 'Pending'
        \App\Models\Laporan::updateOrCreate(
            ['tahun' => $tahun],
            ['status' => 'pending']
        );

        return redirect()->route('laporan.index')->with('success', 'Agregasi K-Means menggunakan metode Modus berhasil! Laporan tahun ' . $tahun . ' telah diajukan ke Pimpinan.');
    }

    // public function simpanAgregasi(Request $request)
    // {
    //     $tahun = $request->tahun;

    //     // 1. Cek apakah laporan tahun ini sudah di-ACC Pimpinan?
    //     $laporan = \App\Models\Laporan::where('tahun', $tahun)->first();

    //     if ($laporan && $laporan->status === 'accepted') {
    //         return redirect()->back()->with('error', 'DATA TERKUNCI! Laporan tahun ' . $tahun . ' sudah disetujui Pimpinan dan tidak bisa diubah lagi.');
    //     }

    //     // 2. Jika belum terkunci, Buat atau Update Laporan jadi 'Pending' (Menunggu ACC)
    //     \App\Models\Laporan::updateOrCreate(
    //         ['tahun' => $tahun],
    //         ['status' => 'pending'] // Naik ke meja pimpinan
    //     );

    //     // (Kita tidak perlu lagi menyimpan agregasi ke tabel Kecamatan.
    //     // Nanti agregasi akan kita hitung secara dinamis (on the fly) saat Pimpinan / Peta memanggilnya,
    //     // Ini adalah Best Practice database agar tidak ada redundansi data).

    //     return redirect()->route('laporan.index')->with('success', 'Agregasi K-Means berhasil! Laporan tahun ' . $tahun . ' telah diajukan ke Pimpinan.');
    // }

    public function resetData(Request $request)
    {
        $tahun = $request->tahun;

        // Cek apakah Laporan sudah di-ACC Pimpinan (Jika ya, data tidak boleh dihapus!)
        $laporan = \App\Models\Laporan::where('tahun', $tahun)->first();
        if ($laporan && $laporan->status === 'accepted') {
            return redirect()->back()->with('error', 'Gagal mereset data! Laporan tahun ' . $tahun . ' sudah disetujui Pimpinan dan terkunci permanen.');
        }

        // Hapus SEMUA data indikator pada tahun tersebut
        \App\Models\Indikator::where('tahun_data', $tahun)->delete();

        // Hapus draf laporan tahun tersebut (jika ada) agar bersih total
        if ($laporan) {
            $laporan->delete();
        }

        return redirect()->back()->with('success', 'Data Indikator tahun ' . $tahun . ' berhasil DIBERSIHKAN. Anda bisa meng-upload ulang file Excel yang benar.');
    }

    public function logPerhitungan(Request $request, \App\Services\KMeansService $kMeansService)
    {
        $tahun = $request->tahun ?? date('Y');
        
        // Panggil fungsi khusus pembuat Log dari Service
        $logData = $kMeansService->getCalculationLog($tahun);

        if (!$logData) {
            return redirect()->route('kmeans.index')->with('error', 'Tidak dapat menampilkan log. Data desa kurang atau belum diinput untuk tahun ' . $tahun);
        }

        // HITUNG AGREGASI KECAMATAN UNTUK DITAMPILKAN DI LOG
        // ====================================================
        $kecamatans = \App\Models\Kecamatan::with(['desas.indikators' => function($q) use ($tahun) {
            $q->where('tahun_data', $tahun);
        }])->get();

        $logAgregasi = [];
        foreach ($kecamatans as $kecamatan) {
            $jmlSejahtera = 0; $jmlBerkembang = 0; $jmlPerhatian = 0;
            $jmlDesa = 0;

            foreach ($kecamatan->desas as $desa) {
                $ind = $desa->indikators->first();
                if ($ind && $ind->klaster_hasil) {
                    if ($ind->klaster_hasil == 1) $jmlSejahtera++;
                    if ($ind->klaster_hasil == 2) $jmlBerkembang++;
                    if ($ind->klaster_hasil == 3) $jmlPerhatian++;
                    $jmlDesa++;
                }
            }

            if ($jmlDesa > 0) {
                $totalSkor = ($jmlSejahtera * 3) + ($jmlBerkembang * 2) + ($jmlPerhatian * 1);
                $rataRata = $totalSkor / $jmlDesa;

                if ($rataRata >= 2.30) $status = 'Sejahtera';
                elseif ($rataRata >= 1.70 && $rataRata <= 2.29) $status = 'Berkembang';
                else $status = 'Perlu Perhatian';

                $logAgregasi[] = [
                    'nama_kecamatan' => $kecamatan->nama_kecamatan,
                    'total_desa' => $jmlDesa,
                    's' => $jmlSejahtera,
                    'b' => $jmlBerkembang,
                    'p' => $jmlPerhatian,
                    'total_skor' => $totalSkor,
                    'rata_rata' => number_format($rataRata, 2),
                    'status_akhir' => $status
                ];
            }
        }

        return view('admin.kmeans.log', compact('logData', 'tahun', 'logAgregasi'));
    }
}
