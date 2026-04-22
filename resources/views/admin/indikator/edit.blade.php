@extends('layouts.admin')
@section('title', 'EDIT DATA INDIKATOR MANUAL')
@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-slate-100 p-8">
        
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#1E293B]">{{ $indikator->desa->nama_desa }}</h2>
                <p class="text-[#64748B]">Tahun Data: {{ $indikator->tahun_data }}</p>
            </div>
            <a href="{{ route('kmeans.index', ['tahun' => $indikator->tahun_data]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold flex items-center gap-1">
                &larr; Kembali ke Tabel
            </a>
        </div>

        <form action="{{ route('indikator.update', $indikator->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Input 1 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Listrik PLN (%)</label>
                    <input type="number" step="0.01" name="listrik_pln" value="{{ $indikator->listrik_pln }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <!-- Input 2 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Fasilitas Ekonomi (Unit)</label>
                    <input type="number" step="0.01" name="fasilitas_ekonomi" value="{{ $indikator->fasilitas_ekonomi }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <!-- Input 3 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Fasilitas Pendidikan (Unit)</label>
                    <input type="number" step="0.01" name="fasilitas_pendidikan" value="{{ $indikator->fasilitas_pendidikan }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <!-- Input 4 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Akses SMA/SMK (Km)</label>
                    <input type="number" step="0.01" name="akses_sma" value="{{ $indikator->akses_sma }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <!-- Input 5 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Faskes Desa (Unit)</label>
                    <input type="number" step="0.01" name="faskes_desa" value="{{ $indikator->faskes_desa }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <!-- Input 6 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Jarak Puskesmas (Km)</label>
                    <input type="number" step="0.01" name="akses_puskesmas" value="{{ $indikator->akses_puskesmas }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <!-- Input 7 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Kualitas Sinyal (Skor)</label>
                    <input type="number" step="0.01" name="kualitas_sinyal" value="{{ $indikator->kualitas_sinyal }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
                <!-- Input 8 -->
                <div>
                    <label class="block text-sm font-semibold text-[#1E293B] mb-2">Keamanan Bencana (Skor)</label>
                    <input type="number" step="0.01" name="keamanan_bencana" value="{{ $indikator->keamanan_bencana }}" class="w-full border-gray-200 rounded-lg focus:ring-[#1E3A8A]">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-[#F97316] hover:bg-orange-600 text-white font-bold py-2.5 px-8 rounded-lg transition shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection