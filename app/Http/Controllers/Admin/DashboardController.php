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
        // Hitung data aktual dari database untuk Card dan Chart
        $summary = [
            'total_desa' => Desa::count(),
            'sejahtera'  => Indikator::where('klaster_hasil', 1)->count(),
            'berkembang' => Indikator::where('klaster_hasil', 2)->count(),
            'perhatian'  => Indikator::where('klaster_hasil', 3)->count(),
        ];

        return view('dashboard', compact('summary'));
    }
}
