@extends('layouts.admin')

@section('title', 'KAMUS INDIKATOR K-MEANS')

@section('content')
    <!-- HEADER & TOMBOL DOWNLOAD -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-xl shadow-sm border border-blue-100">
        <div class="max-w-3xl">
            <h2 class="text-xl font-bold text-[#1E3A8A] mb-2 flex items-center gap-2">
                <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Metadata & Template Pemetaan
            </h2>
            <p class="text-[#64748B] text-sm leading-relaxed text-justify">
                Halaman ini memuat referensi variabel yang digunakan oleh algoritma K-Means. Kolom <b>"Sumber Data (Podes)"</b> merujuk pada kode variabel kuesioner Potensi Desa (Podes) BPS guna memudahkan staf dalam melakukan rekapitulasi data tahunan sebelum di-upload ke dalam sistem.
            </p>
        </div>
        
        <!-- Tombol Download Template Excel -->
        <a href="{{ asset('downloads/Template_Data_KMeans_PALI.xlsx') }}" download class="bg-[#1E3A8A] hover:bg-blue-800 text-white font-bold py-3 px-6 rounded-lg flex items-center gap-2 transition shadow-md shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Download Template Data K-Means
        </a>
    </div>

    <!-- INDIKATOR LIST CARD -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
    <!-- 1. Listrik PLN -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">1</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Pengguna Listrik PLN</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Keluarga/Unit</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Jumlah keluarga yang menggunakan fasilitas listrik dari PLN.</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Variabel <b>r501a1</b>.
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Fasilitas Ekonomi -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">2</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Fasilitas Ekonomi</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Total Unit</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Total ketersediaan sarana ekonomi (pasar, pertokoan, minimarket) di dalam desa.</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Hasil penjumlahan variabel <b>r1209ak2 + r1209bk2 + r1209ck2</b>.
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Fasilitas Pendidikan -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">3</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Fasilitas Pendidikan</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Total Unit</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Total fasilitas pendidikan dari tingkat dasar (SD) hingga menengah atas (SMA/SMK).</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs leading-relaxed">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Hasil penjumlahan variabel<br>
                        <b>r701dk2 + r701ek2 + r701fk2 + r701gk2 + r701hk2 + r701ik2 + r701jk2</b>.
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Akses SMA -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">4</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Akses Jarak SMA/SMK</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Kilometer (Km)</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Jarak tempuh terdekat dari pusat desa menuju fasilitas pendidikan SMA/SMK.</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Mengambil nilai terdekat antara variabel <b>r701hk4</b> atau <b>r701jk4</b> (Hanya salah satu).
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Faskes Desa -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">5</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Fasilitas Kesehatan Desa</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Total Unit</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Jumlah fasilitas kesehatan dasar yang ada di dalam desa (Poskesdes, Polindes, Posyandu, dll).</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Hasil penjumlahan variabel <b>r704jk2 + r704ik2 + r704lk2</b>.
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Akses Puskesmas -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">6</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Jarak ke Puskesmas</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Kilometer (Km)</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Jarak tempuh dari pusat desa ke Puskesmas Kecamatan terdekat.</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Variabel <b>r704dk3</b>.
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Sinyal -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">7</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Kualitas Sinyal Internet/Telepon</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Skor (1-4)</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Skor kualitas jaringan komunikasi di desa. (1: Sangat Kuat, 2: Kuat, 3: Lemah, 4: Blankspot).</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Variabel <b>r1005d</b>.
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Bencana -->
        <div class="px-6 py-5 border-b border-slate-100 hover:bg-slate-50 transition">
            <div class="flex items-start gap-4">
                <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm border border-blue-100">8</div>
                <div class="w-full">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-base font-bold text-[#1E293B]">Keamanan dari Bencana Alam</h3>
                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">Skor Akumulasi</span>
                    </div>
                    <p class="text-sm text-[#64748B] mb-3">Skor kerawanan bencana alam (banjir, longsor, dll). Semakin kecil skor, semakin aman desa tersebut.</p>
                    <div class="bg-blue-50/50 p-3 rounded border border-blue-100 text-xs">
                        <span class="font-bold text-blue-800">Sumber Data (Podes):</span> Hasil penjumlahan variabel <b>r601ak2 + r601bk2 + r601dk2 + r601ik2</b>.
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection