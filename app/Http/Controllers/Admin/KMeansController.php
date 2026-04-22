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
        // Ambil tahun dari request (dropdown), default ke 2024 atau 2025
        $tahun_aktif = $request->tahun ?? 2024;

        // Ambil data indikator beserta nama desanya untuk tahun tersebut
        $data_desa = Indikator::with('desa')->where('tahun_data', $tahun_aktif)->get();

        // Hitung Summary (Berapa desa yang masuk klaster 1, 2, 3)
        $summary = [
            'klaster_1' => $data_desa->where('klaster_hasil', 1)->count(),
            'klaster_2' => $data_desa->where('klaster_hasil', 2)->count(),
            'klaster_3' => $data_desa->where('klaster_hasil', 3)->count(),
            'total' => $data_desa->count()
        ];

        return view('admin.kmeans.index', compact('data_desa', 'tahun_aktif', 'summary'));
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
}
