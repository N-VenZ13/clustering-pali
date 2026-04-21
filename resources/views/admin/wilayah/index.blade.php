@extends('layouts.admin')

@section('title', 'DATA WILAYAH')

@section('content')
    <!-- Action Bar (Search & Tambah) -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="relative w-full md:w-1/3">
            <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" placeholder="Cari Kecamatan atau Desa..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-[#1E3A8A] focus:border-[#1E3A8A]">
        </div>
        <button class="w-full md:w-auto bg-[#F97316] hover:bg-orange-600 text-white font-semibold py-2 px-6 rounded-lg flex items-center justify-center gap-2 transition shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Kecamatan
        </button>
    </div>

    <!-- Accordion List Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
        @forelse($kecamatans as $kecamatan)
            <!-- Alpine.js State per Kecamatan -->
            <div x-data="{ open: false }" class="border-b border-slate-100 last:border-0">
                
                <!-- Baris Kecamatan (Parent) -->
                <div class="bg-[#F8FAFC] px-6 py-4 flex items-center justify-between hover:bg-slate-100 transition cursor-pointer" @click="open = !open">
                    <h3 class="text-lg font-bold text-[#1E293B]">{{ $kecamatan->nama_kecamatan }}</h3>
                    
                    <!-- Ikon Aksi Kecamatan -->
                    <div class="flex items-center gap-4">
                        <button class="text-slate-500 hover:text-[#1E3A8A]" title="Edit Kecamatan">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        </button>
                        <button class="text-slate-500 hover:text-red-500" title="Hapus Kecamatan">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                        <button class="text-slate-500 hover:text-[#10B981]" title="Tambah Desa">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </button>
                        
                        <!-- Ikon Chevron (Berputar jika diklik) -->
                        <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-slate-500 transition-transform duration-200 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <!-- Baris Desa (Child) - Terbuka/Tertutup oleh Alpine -->
                <div x-show="open" x-collapse>
                    <div class="bg-white">
                        @if($kecamatan->desas->count() > 0)
                            @foreach($kecamatan->desas as $desa)
                                <div class="px-6 py-3 border-b border-slate-50 flex items-center justify-between hover:bg-slate-50 transition">
                                    <p class="text-[#1E293B] pl-8 flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        {{ $desa->nama_desa }}
                                    </p>
                                    <div class="flex items-center gap-4">
                                        <button class="text-slate-400 hover:text-[#1E3A8A]"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                        <button class="text-slate-400 hover:text-red-500"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="px-6 py-4 text-center text-slate-400 text-sm">
                                Belum ada data desa di kecamatan ini.
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @empty
            <div class="p-6 text-center text-slate-500">
                Belum ada data kecamatan.
            </div>
        @endforelse

    </div>
@endsection