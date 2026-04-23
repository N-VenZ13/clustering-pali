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

        // 1. Cek apakah laporan tahun ini sudah di-ACC Pimpinan?
        $laporan = \App\Models\Laporan::where('tahun', $tahun)->first();

        if ($laporan && $laporan->status === 'accepted') {
            return redirect()->back()->with('error', 'DATA TERKUNCI! Laporan tahun ' . $tahun . ' sudah disetujui Pimpinan dan tidak bisa diubah lagi.');
        }

        // 2. Jika belum terkunci, Buat atau Update Laporan jadi 'Pending' (Menunggu ACC)
        \App\Models\Laporan::updateOrCreate(
            ['tahun' => $tahun],
            ['status' => 'pending'] // Naik ke meja pimpinan
        );

        // (Kita tidak perlu lagi menyimpan agregasi ke tabel Kecamatan.
        // Nanti agregasi akan kita hitung secara dinamis (on the fly) saat Pimpinan / Peta memanggilnya,
        // Ini adalah Best Practice database agar tidak ada redundansi data).

        return redirect()->route('laporan.index')->with('success', 'Agregasi K-Means berhasil! Laporan tahun ' . $tahun . ' telah diajukan ke Pimpinan.');
    }
}
