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
        // 1. HANYA ambil tahun yang Laporannya sudah "Accepted" dan >= 2024
        $db_years = Laporan::where('status', 'accepted')
                             ->where('tahun', '>=', 2024)
                             ->orderBy('tahun', 'desc')
                             ->pluck('tahun')
                             ->toArray();
        
        // 2. Buat tahun dummy dari 2024 sampai Tahun Saat Ini (Bukan tahun depan untuk publik)
        $current_year = max((int) date('Y'), 2024);
        $dummy_years = range($current_year, 2024); 
        
        $list_tahun = array_unique(array_merge($db_years, $dummy_years));
        rsort($list_tahun);

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

        // Hitung Agregasi Status Kecamatan (Metode Weighted Average / Rata-rata Tertimbang)
        foreach ($kecamatans as $kecamatan) {
            $jmlSejahtera = 0;  // Bobot 3
            $jmlBerkembang = 0; // Bobot 2
            $jmlPerhatian = 0;  // Bobot 1
            $jmlDesa = 0;

            foreach ($kecamatan->desas as $desa) {
                $ind = $desa->indikators->first();
                if ($ind && $ind->klaster_hasil) {
                    if ($ind->klaster_hasil == 1) $jmlSejahtera++;
                    if ($ind->klaster_hasil == 2) $jmlBerkembang++;
                    if ($ind->klaster_hasil == 3) $jmlPerhatian++;
                    $jmlDesa++;
                }
            }

            if ($jmlDesa > 0) {
                // Kalkulasi Total Skor (Sesuai Bab 4 Skripsi)
                $totalSkor = ($jmlSejahtera * 3) + ($jmlBerkembang * 2) + ($jmlPerhatian * 1);
                
                // Kalkulasi Rata-rata
                $rataRata = $totalSkor / $jmlDesa;

                // Threshold Status
                if ($rataRata >= 2.30) {
                    $status = 'Sejahtera';
                } elseif ($rataRata >= 1.70 && $rataRata <= 2.29) {
                    $status = 'Berkembang';
                } else {
                    $status = 'Perlu Perhatian';
                }

                $kecamatan->status_akhir = $status;
                $kecamatan->skor_agregasi = number_format($rataRata, 2); // Tampilkan misal: 1.70
            } else {
                $kecamatan->status_akhir = null;
                $kecamatan->skor_agregasi = null;
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
