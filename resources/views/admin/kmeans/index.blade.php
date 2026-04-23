@extends('layouts.admin')

@section('title', 'PROSES CLUSTERING K-MEANS')

@section('content')

<!-- ALERT NOTIFIKASI -->
<!-- @if(session('success'))
<div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3">
    <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3">
    <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <div class="text-red-800 text-sm font-medium">
        <p>Gagal meng-upload data:</p>
        <p class="font-normal mt-1">{{ session('error') }}</p>
    </div>
</div>
@endif -->

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

    <!-- Aksi K-Means -->
    <div class="flex items-center gap-3 w-full lg:w-auto">
        <!-- Tombol Upload Excel -->
        <!-- <button class="bg-slate-100 hover:bg-slate-200 text-[#1E293B] font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Upload Excel
            </button> -->

        <!-- Form Upload Excel -->
        <form action="{{ route('kmeans.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center">
            @csrf
            <!-- TAMBAHKAN BARIS INI: Mengirim tahun diam-diam -->
            <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">

            <!-- Input File disembunyikan, dipicu lewat label -->
            <input type="file" name="file_excel" id="file_excel" class="hidden" onchange="this.form.submit()" accept=".xlsx, .xls">
            <label for="file_excel" class="bg-slate-100 hover:bg-slate-200 text-[#1E293B] font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition text-sm cursor-pointer border border-slate-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Upload Excel
            </label>
        </form>

        <!-- Tombol Eksekusi -->
        <form action="{{ route('kmeans.proses') }}" method="POST">
            @csrf
            <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
            <button type="submit" onclick="return confirm('Jalankan algoritma K-Means sekarang?')" class="bg-[#F97316] hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2 transition text-sm shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                </svg>
                Jalankan K-Means
            </button>
        </form>
    </div>
</div>

<!-- SUMMARY CARDS (Tengah) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 flex items-center gap-4">
        <div class="w-3 h-12 bg-[#10B981] rounded-full"></div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klaster I (Sejahtera)</p>
            <h4 class="text-2xl font-bold text-[#1E293B]">{{ $summary['klaster_1'] }} <span class="text-sm font-normal text-slate-500">Desa</span></h4>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 flex items-center gap-4">
        <div class="w-3 h-12 bg-[#F59E0B] rounded-full"></div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klaster II (Berkembang)</p>
            <h4 class="text-2xl font-bold text-[#1E293B]">{{ $summary['klaster_2'] }} <span class="text-sm font-normal text-slate-500">Desa</span></h4>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 border border-slate-100 flex items-center gap-4">
        <div class="w-3 h-12 bg-[#EF4444] rounded-full"></div>
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klaster III (Perlu Perhatian)</p>
            <h4 class="text-2xl font-bold text-[#1E293B]">{{ $summary['klaster_3'] }} <span class="text-sm font-normal text-slate-500">Desa</span></h4>
        </div>
    </div>
</div>

<!-- DATA TABLE (Bawah) -->
<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
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
                    <th class="px-6 py-4 text-center font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data_desa as $row)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
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
                        <span class="inline-block whitespace-nowrap px-3 py-1 bg-[#10B981]/10 text-[#10B981] font-bold rounded-md border border-[#10B981]/20">1 - Sejahtera</span>
                        @elseif($row->klaster_hasil == 2)
                        <span class="inline-block whitespace-nowrap px-3 py-1 bg-[#F59E0B]/10 text-[#F59E0B] font-bold rounded-md border border-[#F59E0B]/20">2 - Berkembang</span>
                        @elseif($row->klaster_hasil == 3)
                        <span class="inline-block whitespace-nowrap px-3 py-1 bg-[#EF4444]/10 text-[#EF4444] font-bold rounded-md border border-[#EF4444]/20">3 - Perhatian</span>
                        @else
                        <span class="inline-block whitespace-nowrap text-slate-400 italic">- Belum Diproses -</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <!-- Tombol Edit mengarah ke halaman edit khusus -->
                        <a href="{{ route('indikator.edit', $row->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-md bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition" title="Edit Manual">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-8 text-center text-slate-500">
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

    </table>
</div>

<!-- TOMBOL BOTTOM-UP AGGREGATION -->
<div class="bg-slate-50 border-t border-slate-100 p-4 flex justify-end">
    <form action="{{ route('kmeans.agregasi') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
        <button type="submit" onclick="return confirm('Simpan hasil ini dan update status Kecamatan?')" class="bg-[#F97316] hover:bg-orange-600 text-white font-bold py-2.5 px-8 rounded-lg flex items-center gap-2 transition shadow-md">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
            </svg>
            Simpan & Update Data Kecamatan
        </button>
    </form>
</div>
</div>
@endsection