@extends('layouts.admin')

@section('title', 'DATA K-MEANS')

@section('content')

<!-- CONTROL PANEL (Atas) -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6 flex flex-col lg:flex-row justify-between items-center gap-4">

    <!-- Filter Tahun -->
    <form action="{{ route('kmeans.index') }}" method="GET" class="flex items-center gap-3 w-full lg:w-auto">
        <label class="text-sm font-semibold text-[#64748B]">Tahun Data:</label>
        <select name="tahun" onchange="this.form.submit()" class="border-gray-200 rounded-lg text-sm focus:ring-[#1E3A8A] focus:border-[#1E3A8A]">
            @foreach($list_tahun as $thn)
            <option value="{{ $thn }}" {{ $tahun_aktif == $thn ? 'selected' : '' }}>{{ $thn }}</option>
            @endforeach
        </select>
    </form>

    @if(Auth::user()->role === 'admin')
    <!-- Aksi K-Means -->
    <div class="flex items-center gap-3 w-full lg:w-auto">

        <!-- Tombol RESET DATA (Merah) -->
        <form id="form-reset-data" action="{{ route('kmeans.reset') }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
            <button type="button"
                @click="$dispatch('open-confirm', { title: 'Bersihkan Data {{ $tahun_aktif }}?', msg: 'SEMUA data indikator desa untuk tahun ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.', btnText: 'Ya, Bersihkan', btnColor: 'red', formId: 'form-reset-data' })"
                class="bg-red-50 hover:bg-red-100 text-red-600 font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition text-sm border border-red-200" title="Reset/Hapus Semua Data Tahun Ini">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Reset Data
            </button>
        </form>

        <!-- Form Upload Excel -->
        <form action="{{ route('kmeans.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
            <input type="file" name="file_excel" id="file_excel" class="hidden" onchange="this.form.submit()" accept=".xlsx, .xls">
            <label for="file_excel" class="bg-slate-100 hover:bg-slate-200 text-[#1E293B] font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition text-sm cursor-pointer border border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Upload Excel
            </label>
        </form>

        <!-- Tombol Eksekusi -->
        <form id="form-kmeans" action="{{ route('kmeans.proses') }}" method="POST">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
            <button type="button"
                @click="$dispatch('open-confirm', { title: 'Jalankan K-Means?', msg: 'Sistem akan menghitung klaster data tahun {{ $tahun_aktif }} menggunakan Machine Learning.', btnText: 'Ya, Jalankan', btnColor: 'orange', formId: 'form-kmeans' })"
                class="bg-[#F97316] hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2 transition text-sm shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                Jalankan K-Means
            </button>
        </form>
    </div>
    @endif

</div>

<!-- BANNER STATUS LAPORAN (Khusus jika ditolak/menunggu) -->
@if(isset($laporan_aktif))
@if($laporan_aktif->status === 'rejected')
<div class="mb-6 p-4 rounded-xl bg-red-100 border-2 border-red-300 flex items-start gap-4 shadow-sm animate-pulse">
    <div class="p-2 bg-red-500 rounded-full text-white shrink-0"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg></div>
    <div>
        <h4 class="text-red-800 font-bold text-lg">LAPORAN DITOLAK PIMPINAN!</h4>
        <p class="text-red-700 text-sm mt-1 mb-3">Laporan tahun {{ $tahun_aktif }} ditolak oleh Pimpinan. Silakan lakukan perbaikan data</p>
    </div>
    <div class="bg-white/60 p-3 rounded-lg border border-red-100">
        <span class="text-xs font-bold uppercase text-red-800 tracking-wider">Catatan Pimpinan:</span>
        <p class="text-sm font-medium text-[#1E293B] mt-1 italic">"{{ $laporan_aktif->catatan_pimpinan ?? 'Tidak ada catatan khusus.' }}"</p>
    </div>
</div>
@elseif($laporan_aktif->status === 'pending')
<div class="mb-6 p-4 rounded-xl bg-yellow-50 border border-yellow-200 flex items-center gap-3 shadow-sm">
    <svg class="w-6 h-6 text-yellow-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <p class="text-yellow-800 text-sm font-medium">Laporan tahun {{ $tahun_aktif }} sedang berada di meja Pimpinan (Menunggu Persetujuan). Anda tidak bisa menghapus data saat ini.</p>
</div>
@elseif($laporan_aktif->status === 'accepted')
<div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 flex items-center gap-3 shadow-sm">
    <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <p class="text-green-800 text-sm font-bold">Laporan tahun {{ $tahun_aktif }} sudah DISETUJUI dan DIKUNCI! Peta sudah tayang di halaman publik.</p>
</div>
@endif
@endif

<!-- SUMMARY CARDS (Tengah) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 flex items-center gap-4">
        <div class="w-3 h-12 bg-[#14532d] rounded-full"></div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klaster I (Sejahtera)</p>
            <h4 class="text-2xl font-bold text-[#1E293B]">{{ $summary['klaster_1'] }} <span class="text-sm font-normal text-slate-500">Desa</span></h4>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 flex items-center gap-4">
        <div class="w-3 h-12 bg-[#22c55e] rounded-full"></div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klaster II (Berkembang)</p>
            <h4 class="text-2xl font-bold text-[#1E293B]">{{ $summary['klaster_2'] }} <span class="text-sm font-normal text-slate-500">Desa</span></h4>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 flex items-center gap-4">
        <div class="w-3 h-12 bg-[#bbf7d0] rounded-full"></div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klaster III (Perlu Perhatian)</p>
            <h4 class="text-2xl font-bold text-[#1E293B]">{{ $summary['klaster_3'] }} <span class="text-sm font-normal text-slate-500">Desa</span></h4>
        </div>
    </div>
</div>

<!-- Tombol Lihat Log Perhitungan -->
<div class="mb-6 flex justify-end">
    <a href="{{ route('kmeans.log', ['tahun' => $tahun_aktif]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 bg-blue-50 px-4 py-2 rounded-lg transition border border-blue-100">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        Lihat Log Perhitungan
    </a>
</div>

<!-- DATA TABLE (Bawah) -->
<div x-data="{ search: '' }" class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">

    <!-- Header Tabel & Search Bar -->
    <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-white">
        <h3 class="font-bold text-[#1E293B]">Data Indikator Desa</h3>
        <div class="relative w-full md:w-72">
            <svg class="w-4 h-4 absolute left-3 top-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input x-model="search" type="text" placeholder="Cari nama desa..." class="w-full pl-9 pr-4 py-2 text-sm border-slate-200 rounded-lg focus:ring-[#1E3A8A] focus:border-[#1E3A8A] shadow-sm">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-[#F8FAFC] text-xs uppercase text-[#64748B] border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 font-bold">Desa</th>
                    <th class="px-4 py-4">Listrik PLN</th>
                    <th class="px-4 py-4">Fas. Ekonomi</th>
                    <th class="px-4 py-4">Fas. Pendidikan</th>
                    <th class="px-4 py-4">Akses SMA</th>
                    <th class="px-4 py-4">Faskes Desa</th>
                    <th class="px-4 py-4">Jarak Pusk.</th>
                    <th class="px-4 py-4">Sinyal</th>
                    <th class="px-4 py-4">Bencana</th>
                    <th class="px-6 py-4 text-right font-bold text-[#1E3A8A]">Hasil Klaster</th>
                    @if(Auth::user()->role === 'admin')
                    <th class="px-6 py-4 text-center font-bold">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($data_desa as $row)
                <tr x-show="search === '' || '{{ strtolower($row->desa->nama_desa) }}'.includes(search.toLowerCase())" class="border-b border-slate-50 hover:bg-slate-50 transition">
                    <td class="px-6 py-4 font-bold text-[#1E293B]">{{ $row->desa->nama_desa }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->listrik_pln }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->fasilitas_ekonomi }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->fasilitas_pendidikan }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->akses_sma }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->faskes_desa }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->akses_puskesmas }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->kualitas_sinyal }}</td>
                    <td class="px-4 py-4 text-slate-500">{{ $row->keamanan_bencana }}</td>
                    <td class="px-6 py-4 text-right">
                        @if($row->klaster_hasil == 1)
                        <span class="inline-block whitespace-nowrap px-3 py-1 bg-[#14532d] text-white font-bold rounded-md">1 - Sejahtera</span>
                                @elseif($row->klaster_hasil == 2)
                                    <span class="inline-block whitespace-nowrap px-3 py-1 bg-[#22c55e] text-white font-bold rounded-md">2 - Berkembang</span>
                                @elseif($row->klaster_hasil == 3)
                                    <span class="inline-block whitespace-nowrap px-3 py-1 bg-[#bbf7d0] text-green-900 font-bold rounded-md">3 - Perhatian</span>
                        @else
                        <span class="inline-block whitespace-nowrap text-slate-400 italic">- Belum Diproses -</span>
                        @endif
                    </td>
                    @if(Auth::user()->role === 'admin')
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('indikator.edit', $row->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition" title="Edit Manual">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </a>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-6 py-8 text-center text-slate-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p>Data indikator untuk tahun ini masih kosong.</p>
                            <p class="text-sm">Silakan klik "Upload Excel" untuk memasukkan data.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- TOMBOL BOTTOM-UP AGGREGATION -->
     @if(Auth::user()->role === 'admin')
    <div class="bg-slate-50 border-t border-slate-100 p-4 flex justify-end">
        <form id="form-agregasi" action="{{ route('kmeans.agregasi') }}" method="POST">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">

            @php
            // Cek apakah sudah ada data yang diproses K-Means?
            $isReadyToAggregate = $data_desa->whereNotNull('klaster_hasil')->count() > 0;
            @endphp

            @if($isReadyToAggregate)
            <button type="button"
                @click="$dispatch('open-confirm', { title: 'Simpan & Agregasi?', msg: 'Hasil desa akan di-agregasi untuk menentukan status kecamatan. Laporan akan dikirim ke Pimpinan.', btnText: 'Simpan Data', btnColor: 'orange', formId: 'form-agregasi' })"
                class="bg-[#F97316] hover:bg-orange-600 text-white font-bold py-2.5 px-8 rounded-lg flex items-center gap-2 transition shadow-md">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Simpan & Update Data Kecamatan
            </button>
            @else
            <button type="button" disabled class="bg-slate-300 text-slate-500 font-bold py-2.5 px-8 rounded-lg flex items-center gap-2 cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Simpan (K-Means Belum Diproses)
            </button>
            @endif
        </form>
    </div>
    @endif

</div>

@endsection