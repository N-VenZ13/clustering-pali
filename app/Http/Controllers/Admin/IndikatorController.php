<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndikatorController extends Controller
{
    public function index()
    {
        // 8 Indikator Hardcoded sesuai skripsi/database
        $indikators = [
            ['nama' => 'Persentase Keluarga Miskin', 'kolom' => 'var_miskin', 'satuan' => '% (Persen)', 'deskripsi' => 'Rasio jumlah keluarga miskin terhadap total kepala keluarga di desa.'],
            ['nama' => 'Persentase Rumah Tidak Layak Huni (RTLH)', 'kolom' => 'var_rtlh', 'satuan' => '% (Persen)', 'deskripsi' => 'Rasio rumah tidak layak huni terhadap total rumah di desa.'],
            ['nama' => 'Rasio Faskes Pendidikan', 'kolom' => 'var_rasio_pendidikan', 'satuan' => 'Rasio', 'deskripsi' => 'Jumlah fasilitas pendidikan dasar (SD/SMP) per jumlah anak usia sekolah.'],
            ['nama' => 'Jarak ke SMA/SMK Terdekat', 'kolom' => 'var_jarak_sekolah', 'satuan' => 'Km', 'deskripsi' => 'Jarak tempuh rata-rata dari pusat desa ke SMA/SMK terdekat.'],
            ['nama' => 'Rasio Posyandu & Polindes', 'kolom' => 'var_rasio_posyandu', 'satuan' => 'Rasio', 'deskripsi' => 'Jumlah Posyandu/Polindes aktif per 1000 balita/ibu hamil.'],
            ['nama' => 'Jarak ke Puskesmas Terdekat', 'kolom' => 'var_jarak_puskesmas', 'satuan' => 'Km', 'deskripsi' => 'Jarak tempuh dari pusat pemerintahan desa ke Puskesmas kecamatan.'],
            ['nama' => 'Persentase Rumah Berlistrik PLN', 'kolom' => 'var_listrik', 'satuan' => '% (Persen)', 'deskripsi' => 'Rasio rumah yang menggunakan listrik PLN berbanding total rumah.'],
            ['nama' => 'Persentase Jalan Beraspal/Beton', 'kolom' => 'var_jalan', 'satuan' => '% (Persen)', 'deskripsi' => 'Rasio panjang jalan kondisi baik (aspal/beton) terhadap total jalan poros desa.'],
        ];

        return view('admin.indikator.index', compact('indikators'));
    }
}
