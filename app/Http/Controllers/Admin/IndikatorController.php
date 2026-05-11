<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    public function index()
    {
        // 8 Indikator Hardcoded sesuai 
        $indikators = [
            ['nama' => 'Pengguna Listrik PLN', 'kolom' => 'listrik_pln', 'satuan' => 'Keluarga/Unit', 'deskripsi' => 'Jumlah keluarga atau rumah tangga yang menggunakan fasilitas listrik dari PLN.'],
            ['nama' => 'Fasilitas Ekonomi', 'kolom' => 'fasilitas_ekonomi', 'satuan' => 'Unit', 'deskripsi' => 'Jumlah fasilitas ekonomi dasar seperti pasar, pertokoan, atau minimarket di desa.'],
            ['nama' => 'Fasilitas Pendidikan', 'kolom' => 'fasilitas_pendidikan', 'satuan' => 'Unit', 'deskripsi' => 'Jumlah fasilitas pendidikan dasar hingga menengah (SD/SMP/SMA) di desa.'],
            ['nama' => 'Akses SMA/SMK Terdekat', 'kolom' => 'akses_sma', 'satuan' => 'Km', 'deskripsi' => 'Jarak tempuh rata-rata dari pusat desa ke fasilitas SMA/SMK terdekat.'],
            ['nama' => 'Fasilitas Kesehatan Desa', 'kolom' => 'faskes_desa', 'satuan' => 'Unit', 'deskripsi' => 'Jumlah Poskesdes, Polindes, atau Posyandu aktif di wilayah desa.'],
            ['nama' => 'Jarak ke Puskesmas Terdekat', 'kolom' => 'akses_puskesmas', 'satuan' => 'Km', 'deskripsi' => 'Jarak tempuh dari pusat pemerintahan desa ke Puskesmas tingkat kecamatan.'],
            ['nama' => 'Kualitas Sinyal Telepon/Internet', 'kolom' => 'kualitas_sinyal', 'satuan' => 'Skor (1-4)', 'deskripsi' => 'Skor kualitas sinyal komunikasi (1: Sangat Kuat, 2: Kuat, 3: Lemah, 4: Blankspot).'],
            ['nama' => 'Keamanan dari Bencana Alam', 'kolom' => 'keamanan_bencana', 'satuan' => 'Skor', 'deskripsi' => 'Skor tingkat kerawanan dan keamanan desa terhadap bencana alam (banjir/longsor).'],
        ];

        return view('admin.indikator.index', compact('indikators'));
    }

    public function edit($id)
    {
        $indikator = \App\Models\Indikator::with('desa')->findOrFail($id);
        return view('admin.indikator.edit', compact('indikator'));
    }

    public function update(\Illuminate\Http\Request $request, $id)
    {
        $indikator = \App\Models\Indikator::findOrFail($id);

        $indikator->update($request->except(['_token', '_method']));

        // Reset klaster_hasil menjadi null karena datanya berubah (wajib K-Means ulang)
        $indikator->update(['klaster_hasil' => null]);

        return redirect()->route('kmeans.index', ['tahun' => $indikator->tahun_data])
            ->with('success', 'Data Desa ' . $indikator->desa->nama_desa . ' berhasil diperbarui! Silakan jalankan ulang K-Means.');
    }
}
