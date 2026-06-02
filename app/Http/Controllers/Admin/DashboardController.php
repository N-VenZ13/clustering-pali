<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Indikator;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Tentukan tahun baseline (2024)
        $tahun_baseline = 2024;
        
        // 2. Ambil tahun-tahun yang ada datanya, filter >= 2024
        $db_years = Indikator::where('tahun_data', '>=', $tahun_baseline)
                             ->select('tahun_data')->distinct()->pluck('tahun_data')->toArray();
        
        // 3. Gabungkan dengan tahun saat ini (jika belum ada di DB)
        $current_year = max((int) date('Y'), $tahun_baseline);
        if (!in_array($current_year, $db_years)) {
            $db_years[] = $current_year;
        }
        
        rsort($db_years); // Urutkan terbaru ke terlama
        $tahun_aktif = request('tahun', $db_years[0]); // Ambil dari URL, atau default terbaru

        // 4. Hitung data untuk Card dan Chart berdasarkan Tahun Aktif
        $summary = [
            'total_desa' => Desa::count(),
            'sejahtera'  => Indikator::where('tahun_data', $tahun_aktif)->where('klaster_hasil', 1)->count(),
            'berkembang' => Indikator::where('tahun_data', $tahun_aktif)->where('klaster_hasil', 2)->count(),
            'perhatian'  => Indikator::where('tahun_data', $tahun_aktif)->where('klaster_hasil', 3)->count(),
        ];

        return view('dashboard', compact('summary', 'db_years', 'tahun_aktif'));
    }
}
