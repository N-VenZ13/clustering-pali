<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function index()
    {
        $kecamatans = Kecamatan::with('desas')->get();
        return view('admin.wilayah.index', compact('kecamatans'));
    }

    // --- CRUD KECAMATAN ---
    public function storeKecamatan(Request $request)
    {
        $request->validate(['nama_kecamatan' => 'required|string|max:255|unique:kecamatans']);
        Kecamatan::create(['nama_kecamatan' => $request->nama_kecamatan, 'status_validasi' => 'draft']);
        return redirect()->back()->with('success', 'Kecamatan berhasil ditambahkan!');
    }

    public function updateKecamatan(Request $request, $id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $request->validate(['nama_kecamatan' => 'required|string|max:255|unique:kecamatans,nama_kecamatan,' . $id]);
        $kecamatan->update(['nama_kecamatan' => $request->nama_kecamatan]);
        return redirect()->back()->with('success', 'Nama Kecamatan berhasil diperbarui!');
    }

    public function destroyKecamatan($id)
    {
        Kecamatan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kecamatan (beserta seluruh desa di dalamnya) berhasil dihapus!');
    }

    // --- CRUD DESA ---
    public function storeDesa(Request $request)
    {
        $request->validate([
            'kecamatan_id' => 'required|exists:kecamatans,id',
            'nama_desa' => 'required|string|max:255'
        ]);
        Desa::create($request->all());
        return redirect()->back()->with('success', 'Desa baru berhasil ditambahkan!');
    }

    public function updateDesa(Request $request, $id)
    {
        $desa = Desa::findOrFail($id);
        $request->validate(['nama_desa' => 'required|string|max:255']);
        $desa->update(['nama_desa' => $request->nama_desa]);
        return redirect()->back()->with('success', 'Nama Desa berhasil diperbarui!');
    }

    public function destroyDesa($id)
    {
        Desa::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Desa berhasil dihapus!');
    }
}
