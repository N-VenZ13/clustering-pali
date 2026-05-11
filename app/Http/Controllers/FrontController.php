<?php

namespace App\Http\Controllers;

use App\Models\Indikator;
use App\Models\Kecamatan;
use App\Models\Laporan;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index(Request $request)
    {
        // 1. HANYA ambil tahun yang Laporannya sudah di "Accepted" oleh Pimpinan
        // 1. Ambil tahun-tahun yang Laporannya sudah "Accepted"
        $db_years = Laporan::where('status', 'accepted')->orderBy('tahun', 'desc')->pluck('tahun')->toArray();
        
        // 2. Buat tahun buatan (Dummy) 5 tahun ke belakang agar dropdown tidak kosong melompong (Jika DB kosong)
        $current_year = (int) date('Y');
        $dummy_years = range($current_year, $current_year - 4); 
        
        $list_tahun = array_unique(array_merge($db_years, $dummy_years));
        rsort($list_tahun); // Urutkan terbaru ke terlama

        $tahun_aktif = $request->tahun ?? $list_tahun[0];

        // Jika belum ada satupun yang di ACC, kembalikan ke halaman kosong/maintenance
        if (empty($list_tahun)) {
            return view('welcome_empty');
        }

        // $tahun_aktif = $request->tahun ?? $list_tahun[0];

        // 2. Ambil data dengan dinamis perhitungan agregasi
        $kecamatans = Kecamatan::with(['desas.indikators' => function ($q) use ($tahun_aktif) {
            $q->where('tahun_data', $tahun_aktif);
        }])->get();

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

        return view('welcome', compact('kecamatans', 'list_tahun', 'tahun_aktif'));
    }

    public function metadata()
    {
        // Data 8 indikator sama persis seperti yang di Admin
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

        return view('publik.metadata', compact('indikators'));
    }

    public function panduan()
    {
        return view('publik.panduan');
    }
}
