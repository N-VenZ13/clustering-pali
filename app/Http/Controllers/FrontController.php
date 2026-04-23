<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil ketersediaan tahun dari database
        $list_tahun = Indikator::select('tahun_data')->distinct()->orderBy('tahun_data', 'desc')->pluck('tahun_data')->toArray();
        $tahun_aktif = $request->tahun ?? ($list_tahun[0] ?? date('Y'));

        // 2. Ambil data Kecamatan beserta Desa dan Indikatornya
        // Catatan: Idealnya kita beri ->where('status_validasi', 'accepted') di sini
        // Tapi untuk testing development, kita ambil semua yang sudah diproses K-Means saja.
        $kecamatans = Kecamatan::with(['desas.indikators' => function ($q) use ($tahun_aktif) {
            $q->where('tahun_data', $tahun_aktif);
        }])->whereNotNull('status_akhir')->get();

        return view('welcome', compact('kecamatans', 'list_tahun', 'tahun_aktif'));
    }
}
