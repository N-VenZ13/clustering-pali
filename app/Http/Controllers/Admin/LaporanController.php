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
        // 1. Ambil tahun laporan (Jika kosong, buat dummy 5 tahun mundur)
        $db_years = Laporan::orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        $current_year = (int) date('Y');
        $dummy_years = range($current_year, $current_year - 4); 
        
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
