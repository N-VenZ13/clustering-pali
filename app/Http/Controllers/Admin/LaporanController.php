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
        // 1. Ambil Laporan Tahun Terbaru, ATAU sesuai pilihan dropdown
        $laporan_terbaru = Laporan::orderBy('tahun', 'desc')->first();
        $tahun_aktif = $request->tahun ?? ($laporan_terbaru ? $laporan_terbaru->tahun : date('Y'));

        $laporan_aktif = Laporan::where('tahun', $tahun_aktif)->first();
        $status_dokumen = $laporan_aktif ? $laporan_aktif->status : 'belum_ada';

        // 2. Jika laporan belum ada sama sekali, kembalikan kosongan
        if ($status_dokumen === 'belum_ada') {
            return view('admin.laporan.index', [
                'status_dokumen' => 'belum_ada',
                'tahun_aktif' => $tahun_aktif,
                'list_tahun' => Laporan::pluck('tahun')->toArray()
            ]);
        }

        // 3. Ambil data Kecamatan dan hitung Agregasi (On The Fly) untuk tahun tersebut
        $kecamatans = Kecamatan::with(['desas.indikators' => function ($q) use ($tahun_aktif) {
            $q->where('tahun_data', $tahun_aktif);
        }])->get();

        $summary = ['sejahtera' => 0, 'berkembang' => 0, 'perhatian' => 0];

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
                if ($rataRata < 1.67) {
                    $status = 'Sejahtera';
                    $summary['sejahtera']++;
                } elseif ($rataRata <= 2.33) {
                    $status = 'Berkembang';
                    $summary['berkembang']++;
                } else {
                    $status = 'Perlu Perhatian';
                    $summary['perhatian']++;
                }
                $kecamatan->status_akhir = $status;
                $kecamatan->skor_agregasi = round($rataRata, 2);
            } else {
                $kecamatan->status_akhir = '-';
                $kecamatan->skor_agregasi = '-';
            }
        }

        $list_tahun = Laporan::orderBy('tahun', 'desc')->pluck('tahun')->toArray();

        return view('admin.laporan.index', compact('kecamatans', 'summary', 'status_dokumen', 'tahun_aktif', 'list_tahun', 'laporan_aktif'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'tahun' => 'required',
            'status' => 'required|in:accepted,rejected'
        ]);

        $laporan = Laporan::where('tahun', $request->tahun)->firstOrFail();

        $laporan->update([
            'status' => $request->status,
            'dikunci_pada' => $request->status == 'accepted' ? now() : null
        ]);

        $pesan = $request->status == 'accepted' ? 'Laporan DITERIMA! Peta Publik kini diperbarui.' : 'Laporan DITOLAK! Admin kini bisa merevisi data.';
        return redirect()->back()->with('success', $pesan);
    }
}
