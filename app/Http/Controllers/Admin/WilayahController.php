<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        // Ambil semua data kecamatan beserta data desa di dalamnya
        $kecamatans = Kecamatan::with('desas')->get();
        return view('admin.wilayah.index', compact('kecamatans'));
    }
}
