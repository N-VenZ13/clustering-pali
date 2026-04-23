<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // Ambil semua data kecamatan
        $kecamatans = Kecamatan::all();

        // Hitung Summary Kecamatan untuk Grafik/Chart
        $summary = [
            'sejahtera' => $kecamatans->where('status_akhir', 'Sejahtera')->count(),
            'berkembang' => $kecamatans->where('status_akhir', 'Berkembang')->count(),
            'perhatian' => $kecamatans->where('status_akhir', 'Perlu Perhatian')->count(),
        ];

        // Cek status validasi (Apakah ada yang masih draf?)
        $status_dokumen = $kecamatans->pluck('status_validasi')->unique()->first() ?? 'draft';

        return view('admin.laporan.index', compact('kecamatans', 'summary', 'status_dokumen'));
    }

    public function updateStatus(Request $request)
    {
        // Fungsi ini khusus untuk tombol Approve/Reject Pimpinan
        $status = $request->status; // 'accepted' atau 'rejected'

        Kecamatan::query()->update(['status_validasi' => $status]);

        $pesan = $status == 'accepted' ? 'Laporan DITERIMA! Peta Publik kini diperbarui.' : 'Laporan DITOLAK! Silakan revisi data K-Means.';
        return redirect()->back()->with('success', $pesan);
    }
}
