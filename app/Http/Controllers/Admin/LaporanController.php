<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use App\Models\Laporan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tahun laporan (Mulai 2024 sampai Tahun Depan)
        $db_years = Laporan::where('tahun', '>=', 2024)->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        $current_year = max((int) date('Y'), 2024);
        $dummy_years = range($current_year + 1, 2024); // Array [2027, 2026, 2025, 2024]
        
        $list_tahun = array_unique(array_merge($db_years, $dummy_years));
        rsort($list_tahun);

        $tahun_aktif = $request->tahun ?? $list_tahun[0];
        
        $laporan_aktif = Laporan::where('tahun', $tahun_aktif)->first();
        $status_dokumen = $laporan_aktif ? $laporan_aktif->status : 'belum_ada';

        // 2. Jika laporan belum ada sama sekali, kembalikan kosongan
        if ($status_dokumen === 'belum_ada') {
            return view('admin.laporan.index', [
                'status_dokumen' => 'belum_ada',
                'tahun_aktif' => $tahun_aktif,
                'list_tahun' => $list_tahun // PERBAIKAN: Gunakan $list_tahun yang digabung di atas
            ]);
        }

        // 3. Ambil data Kecamatan dan hitung Agregasi (On The Fly) untuk tahun tersebut
        $kecamatans = Kecamatan::with(['desas.indikators' => function ($q) use ($tahun_aktif) {
            $q->where('tahun_data', $tahun_aktif);
        }])->get();

        $summary = ['sejahtera' => 0, 'berkembang' => 0, 'perhatian' => 0];

        // Hitung Agregasi Status Kecamatan (Metode Weighted Average / Rata-rata Tertimbang)
        foreach ($kecamatans as $kecamatan) {
            $jmlSejahtera = 0;  // Bobot 3
            $jmlBerkembang = 0; // Bobot 2
            $jmlPerhatian = 0;  // Bobot 1
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
                // Kalkulasi Total Skor (Sesuai Bab 4 Skripsi)
                $totalSkor = ($jmlSejahtera * 3) + ($jmlBerkembang * 2) + ($jmlPerhatian * 1);
                
                // Kalkulasi Rata-rata
                $rataRata = $totalSkor / $jmlDesa;

                // Threshold Status
                if ($rataRata >= 2.30) {
                    $status = 'Sejahtera';
                } elseif ($rataRata >= 1.70 && $rataRata <= 2.29) {
                    $status = 'Berkembang';
                } else {
                    $status = 'Perlu Perhatian';
                }

                $kecamatan->status_akhir = $status;
                $kecamatan->skor_agregasi = number_format($rataRata, 2); // Tampilkan misal: 1.70
            } else {
                $kecamatan->status_akhir = null;
                $kecamatan->skor_agregasi = null;
            }
        }

        // PERBAIKAN: Hapus baris penimpa $list_tahun di sini

        return view('admin.laporan.index', compact('kecamatans', 'summary', 'status_dokumen', 'tahun_aktif', 'list_tahun', 'laporan_aktif'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'status' => 'required|in:accepted,rejected',
            'catatan' => 'nullable|string' // Validasi catatan tambahan
        ]);

        $laporan = Laporan::where('tahun', $request->tahun)->firstOrFail();

        $laporan->update([
            'status' => $request->status,
            'catatan_pimpinan' => $request->status == 'rejected' ? $request->catatan : null,
            'dikunci_pada' => $request->status == 'accepted' ? now() : null
        ]);

        $pesan = $request->status == 'accepted' ? 'Laporan DITERIMA! Peta Publik kini diperbarui.' : 'Laporan DITOLAK! Catatan telah dikirim ke Admin.';
        return redirect()->back()->with('success', $pesan);
    }
}
