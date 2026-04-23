@extends('layouts.admin')
@section('title', 'LAPORAN HASIL PEMETAAN')

@section('content')

    @if($status_dokumen === 'belum_ada')
        <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100 text-center">
            <h2 class="text-xl font-bold text-slate-500 mb-2">Belum Ada Laporan</h2>
            <p class="text-slate-400">Admin belum melakukan proses K-Means dan Agregasi untuk tahun ini.</p>
        </div>
    @else

    <!-- ACTION BAR -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        
        <!-- Status & Filter Tahun -->
        <div class="flex items-center gap-4">
            <form action="{{ route('laporan.index') }}" method="GET">
                <select name="tahun" onchange="this.form.submit()" class="border-gray-200 rounded-lg text-sm focus:ring-[#1E3A8A] focus:border-[#1E3A8A] bg-slate-50">
                    @foreach($list_tahun as $thn)
                        <option value="{{ $thn }}" {{ $tahun_aktif == $thn ? 'selected' : '' }}>Tahun: {{ $thn }}</option>
                    @endforeach
                </select>
            </form>

            <span class="text-sm font-semibold text-slate-500 border-l pl-4">Status Dokumen:</span>
            @if($status_dokumen == 'accepted')
                <span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-md text-sm">✓ Disetujui (Published)</span>
            @elseif($status_dokumen == 'rejected')
                <span class="px-3 py-1 bg-red-100 text-red-700 font-bold rounded-md text-sm">✗ Ditolak (Revisi)</span>
            @else
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-bold rounded-md text-sm">⚠ Menunggu Review Pimpinan</span>
            @endif
        </div>

        <!-- Tombol Aksi (Tergantung Role) -->
        <div class="flex items-center gap-3">
            
            {{-- Tombol KHUSUS PIMPINAN --}}
            @if(Auth::user()->role === 'pimpinan' && $status_dokumen !== 'accepted')
                <form action="{{ route('laporan.status') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" onclick="return confirm('Tolak Laporan ini?')" class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition">Tolak Laporan</button>
                </form>
                <form action="{{ route('laporan.status') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" onclick="return confirm('Terima Laporan? Peta publik akan otomatis diperbarui.')" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition">Terima & Publish</button>
                </form>
            @endif

            {{-- Tombol Cetak --}}
            <button onclick="window.print()" class="py-2 px-4 rounded-lg font-semibold text-sm flex items-center gap-2 transition {{ $status_dokumen == 'accepted' ? 'bg-[#1E3A8A] hover:bg-blue-800 text-white shadow-md' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}" {{ $status_dokumen != 'accepted' ? 'disabled' : '' }}>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak PDF
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- KERTAS A4 (Area Cetak) -->
    <div id="print-area" class="max-w-[800px] mx-auto bg-white p-12 shadow-2xl border border-gray-300 min-h-[1122px] relative">
        
        <!-- Watermark jika belum ACC -->
        @if($status_dokumen !== 'accepted')
            <div class="absolute inset-0 flex items-center justify-center opacity-10 pointer-events-none z-0">
                <span class="text-8xl font-black text-red-500 transform -rotate-45">DRAFT</span>
            </div>
        @endif

        <!-- KOP SURAT -->
        <div class="flex items-center gap-6 border-b-4 border-double border-[#1E293B] pb-6 mb-8 relative z-10">
            <div class="w-20 h-20 bg-slate-200 rounded-full flex items-center justify-center shrink-0">
                <span class="text-xs text-slate-500">Logo</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-[#1E293B] uppercase tracking-wide">LAPORAN PEMETAAN KESEJAHTERAAN SOSIAL</h1>
                <p class="text-lg text-[#1E293B]">Kabupaten Penukal Abab Lematang Ilir (PALI) Tahun {{ $tahun_aktif }}</p>
                <p class="text-sm text-[#64748B]">Sistem Informasi Geografis Menggunakan K-Means Clustering</p>
            </div>
        </div>

        <!-- TEKS PENGANTAR -->
        <div class="mb-8 text-justify text-[#1E293B] leading-relaxed relative z-10">
            <p class="mb-4">Berdasarkan hasil pemrosesan data menggunakan algoritma Machine Learning (K-Means Clustering) terhadap 8 indikator kesejahteraan sosial pada desa/kelurahan di Kabupaten Penukal Abab Lematang Ilir (PALI), telah diperoleh pemetaan wilayah yang terbagi menjadi 3 tingkatan klaster.</p>
            <p>Laporan ini disusun secara otomatis oleh sistem sebagai landasan pengambilan keputusan strategis. Berikut adalah ringkasan sebaran tingkat kesejahteraan kecamatan berdasarkan agregasi nilai desa:</p>
        </div>

        <!-- TABEL REKAPITULASI -->
        <table class="w-full border-collapse border border-gray-300 mb-12 relative z-10">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 text-left">No.</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Nama Kecamatan</th>
                    <th class="border border-gray-300 px-4 py-2 text-center">Skor Agregasi</th>
                    <th class="border border-gray-300 px-4 py-2 text-left">Status Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kecamatans as $index => $kec)
                <tr>
                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $index + 1 }}</td>
                    <td class="border border-gray-300 px-4 py-2 font-semibold">{{ $kec->nama_kecamatan }}</td>
                    <td class="border border-gray-300 px-4 py-2 text-center">{{ $kec->skor_agregasi }}</td>
                    <td class="border border-gray-300 px-4 py-2 font-bold 
                        {{ $kec->status_akhir == 'Sejahtera' ? 'text-green-600' : ($kec->status_akhir == 'Perlu Perhatian' ? 'text-red-600' : 'text-yellow-600') }}">
                        {{ $kec->status_akhir }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TANDA TANGAN -->
        <div class="flex justify-end mt-16 relative z-10">
            <div class="text-center w-64">
                <p class="mb-1 text-[#1E293B]">Talang Ubi, {{ $laporan_aktif && $laporan_aktif->dikunci_pada ? \Carbon\Carbon::parse($laporan_aktif->dikunci_pada)->format('d F Y') : '......................' }}</p>
                <p class="font-bold text-[#1E293B] mb-24">Kepala Badan Pusat Statistik</p>
                <p class="font-bold text-[#1E293B] underline">Ir. Budi Santoso, M.Si</p>
                <p class="text-sm text-[#1E293B]">NIP. 19780101 200501 1 001</p>
            </div>
        </div>

    </div>

    <!-- SCRIPT CSS KHUSUS PRINT -->
    <style>
        @media print {
            body * { visibility: hidden; }
            #print-area, #print-area * { visibility: visible; }
            #print-area { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; padding: 0; }
        }
    </style>
    @endif
@endsection