@extends('layouts.admin')
@section('title', 'LAPORAN HASIL PEMETAAN')

@section('content')

    <!-- ACTION BAR (SELALU MUNCUL) -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        
        <!-- Filter Tahun & Status Dokumen -->
        <div class="flex items-center gap-4">
            <form action="{{ route('laporan.index') }}" method="GET" class="flex items-center gap-2">
                <label class="text-sm font-semibold text-slate-500">Tahun Laporan:</label>
                <div class="relative">
                    <select name="tahun" onchange="this.form.submit()" class="appearance-none bg-blue-50 border border-blue-200 text-[#1E3A8A] font-bold text-sm rounded-lg pl-3 pr-8 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                        @foreach($list_tahun as $thn)
                            <option value="{{ $thn }}" {{ $tahun_aktif == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#1E3A8A]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </form>

            <span class="text-sm font-semibold text-slate-500 border-l pl-4">Status Dokumen:</span>
            @if($status_dokumen == 'accepted')
                <span class="px-3 py-1 bg-green-100 text-green-700 font-bold rounded-md text-sm">✓ Disetujui (Published)</span>
            @elseif($status_dokumen == 'rejected')
                <span class="px-3 py-1 bg-red-100 text-red-700 font-bold rounded-md text-sm">✗ Ditolak (Revisi)</span>
            @elseif($status_dokumen == 'pending')
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 font-bold rounded-md text-sm">⚠ Menunggu Review Pimpinan</span>
            @else
                <span class="px-3 py-1 bg-slate-100 text-slate-500 font-bold rounded-md text-sm">Kosong</span>
            @endif
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center gap-3">
            
            {{-- Tombol KHUSUS PIMPINAN (Hanya muncul jika status pending) --}}
            @if(Auth::user()->role === 'pimpinan' && $status_dokumen === 'pending')
                <div x-data="{ showCatatan: false }" class="flex items-center gap-2">
                    <form action="{{ route('laporan.status') }}" method="POST" class="flex items-center gap-2" x-show="showCatatan" x-cloak>
                        @csrf
                        <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
                        <input type="hidden" name="status" value="rejected">
                        <input type="text" name="catatan" placeholder="Tulis alasan penolakan..." required class="border-red-300 rounded-lg text-sm px-3 py-1.5 focus:ring-red-500 min-w-[250px]">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-1.5 px-3 rounded-lg text-sm transition">Kirim Penolakan</button>
                        <button type="button" @click="showCatatan = false" class="text-slate-400 hover:text-slate-600 px-2">Batal</button>
                    </form>

                    <div x-show="!showCatatan" class="flex items-center gap-2">
                        <button type="button" @click="showCatatan = true" class="bg-red-100 text-red-600 hover:bg-red-200 font-semibold py-2 px-4 rounded-lg text-sm transition border border-red-200">Tolak Laporan</button>
                        
                        <form id="form-accept" action="{{ route('laporan.status') }}" method="POST">
                            @csrf
                            <input type="hidden" name="tahun" value="{{ $tahun_aktif }}">
                            <input type="hidden" name="status" value="accepted">
                            <button type="button" 
                                    @click="$dispatch('open-confirm', { title: 'Terima & Publish?', msg: 'Peta K-Means tahun {{ $tahun_aktif }} akan otomatis muncul di halaman publik.', btnText: 'Ya, Publish', btnColor: 'green', formId: 'form-accept' })" 
                                    class="bg-[#10B981] hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded-lg text-sm transition shadow-sm">Terima & Publish</button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Tombol Cetak PDF --}}
            <button onclick="window.print()" class="py-2 px-4 rounded-lg font-semibold text-sm flex items-center gap-2 transition {{ $status_dokumen == 'accepted' ? 'bg-[#1E3A8A] hover:bg-blue-800 text-white shadow-md' : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}" {{ $status_dokumen != 'accepted' ? 'disabled' : '' }}>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak PDF
            </button>
        </div>
    </div>

    <!-- AREA BAWAH: PESAN KOSONG ATAU KERTAS A4 -->
    @if($status_dokumen === 'belum_ada')
        <div class="bg-white p-8 rounded-xl shadow-sm border border-slate-100 text-center">
            <h2 class="text-xl font-bold text-slate-500 mb-2">Belum Ada Laporan (Tahun {{ $tahun_aktif }})</h2>
            <p class="text-slate-400">Admin belum melakukan proses K-Means dan Agregasi untuk tahun ini.</p>
        </div>
    @else
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
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shrink-0">
                    <img src="{{ asset('images/logo.png') }}" class="w-full h-full object-contain">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[#1E293B] uppercase tracking-wide">LAPORAN PEMETAAN KESEJAHTERAAN SOSIAL</h1>
                    <p class="text-lg text-[#1E293B]">Kabupaten Penukal Abab Lematang Ilir (PALI) Tahun {{ $tahun_aktif }}</p>
                    <p class="text-sm text-[#64748B]">Sistem Informasi Geografis Menggunakan K-Means Clustering</p>
                </div>
            </div>

            <!-- TEKS PENGANTAR -->
            <div class="mb-8 text-justify text-[#1E293B] leading-relaxed relative z-10">
                <p class="mb-4">Dokumen ini merupakan laporan resmi hasil pemrosesan algoritma <i>Machine Learning (K-Means Clustering)</i> terhadap 8 (delapan) indikator kesejahteraan sosial pada seluruh desa dan kelurahan di wilayah administratif Kabupaten Penukal Abab Lematang Ilir (PALI) untuk tahun data <b>{{ $tahun_aktif }}</b>.</p>
                <p>Berdasarkan perhitungan nilai kedekatan jarak (*Euclidean Distance*) yang diinisiasi melalui <i>Deterministic Initialization</i>, wilayah perdesaan telah diklasifikasikan menjadi tiga tingkatan klaster, yakni: <b>Klaster Sejahtera</b>, <b>Klaster Berkembang</b>, dan <b>Klaster Perlu Perhatian</b>. Status akhir tiap kecamatan ditetapkan melalui metode <i>Bottom-Up Aggregation</i> berdasarkan frekuensi modus terbanyak dari desa-desa di wilayahnya.</p>
            </div>

            <!-- TABEL REKAPITULASI -->
            <table class="w-full border-collapse border border-gray-300 mb-12 relative z-10">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-4 py-2 text-left">No.</th>
                        <th class="border border-gray-300 px-4 py-2 text-left">Nama Kecamatan</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Persentase Mayoritas</th>
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
                            {{ $kec->status_akhir == 'Sejahtera' ? 'text-[#14532d]' : ($kec->status_akhir == 'Perlu Perhatian' ? 'text-[#059669]' : 'text-[#22c55e]') }}">
                            {{ $kec->status_akhir }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- TABEL RINCIAN PER DESA -->
            <h3 class="text-lg font-bold text-[#1E293B] mt-12 mb-4 relative z-10 border-b pb-2">Rincian Klasifikasi Tingkat Desa</h3>
            
            @foreach($kecamatans as $kec)
                <div class="mb-6 relative z-10 break-inside-avoid">
                    <h4 class="font-bold text-[#1E3A8A] mb-2 uppercase tracking-wider text-sm bg-slate-100 p-2 rounded-t-md">
                        KECAMATAN {{ $kec->nama_kecamatan }} 
                        <span class="text-slate-500 font-normal lowercase">(Status Agregasi: {{ $kec->status_akhir ?? 'Belum Diproses' }})</span>
                    </h4>
                    
                    <table class="w-full text-sm border-collapse border border-gray-200">
                        <tbody>
                            @forelse($kec->desas as $desa)
                                @php 
                                    $ind = $desa->indikators->first();
                                    $klaster = $ind ? $ind->klaster_hasil : null;
                                    $label = '-'; $warna = '';
                                    if($klaster == 1) { $label = 'Sejahtera'; $warna = 'text-[#14532d]'; }
                                    elseif($klaster == 2) { $label = 'Berkembang'; $warna = 'text-[#22c55e]'; }
                                    elseif($klaster == 3) { $label = 'Perlu Perhatian'; $warna = 'text-[#059669]'; }
                                @endphp
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 px-4 w-1/2">{{ $desa->nama_desa }}</td>
                                    <td class="py-2 px-4 border-l border-gray-100 font-semibold {{ $warna }}">{{ $label }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="py-2 px-4 text-slate-400 italic">Belum ada data desa terinput.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endforeach

            <!-- TANDA TANGAN -->
            <div class="flex justify-end mt-16 relative z-10">
                <div class="text-center w-64">
                    <p class="mb-1 text-[#1E293B]">Talang Ubi, {{ $laporan_aktif && $laporan_aktif->dikunci_pada ? \Carbon\Carbon::parse($laporan_aktif->dikunci_pada)->format('d F Y') : '......................' }}</p>
                    <p class="font-bold text-[#1E293B] mb-24">Kepala Badan Pusat Statistik</p>
                    <p class="font-bold text-[#1E293B] underline">Ir. Budi Santoso, M.Si</p>
                    <p class="text-sm text-[#1E293B]">NIP. 19780101 200501 1 001</p>
                </div>
            </div>

            <!-- TIMESTAMP UNDUH -->
            <div class="absolute bottom-6 left-12 text-[10px] text-gray-400 font-mono">
                Dokumen di-generate oleh Sistem WebGIS PALI pada: {{ now()->format('d M Y - H:i:s') }} WIB
            </div>
        </div>

        <!-- CSS KHUSUS PRINT -->
        <style>
            @media print {
                body * { visibility: hidden; }
                #print-area, #print-area * { visibility: visible; }
                #print-area { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none; border: none; padding: 0; }
            }
        </style>
    @endif
@endsection