@extends('layouts.admin')

@section('title', 'KAMUS INDIKATOR K-MEANS')

@section('content')
    <div class="mb-6">
        <p class="text-[#64748B]">Berikut adalah 8 variabel indikator kesejahteraan sosial yang ditetapkan dalam sistem untuk diproses menggunakan algoritma K-Means.</p>
    </div>

    <!-- Indikator List Card -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        
        @foreach($indikators as $index => $ind)
            <div class="px-6 py-4 border-b border-slate-100 last:border-0 hover:bg-slate-50 transition flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <div class="flex items-start gap-4">
                    <!-- Nomor Urut -->
                    <div class="w-8 h-8 shrink-0 rounded-full bg-blue-50 text-[#1E3A8A] flex items-center justify-center font-bold text-sm">
                        {{ $index + 1 }}
                    </div>
                    
                    <!-- Judul & Deskripsi -->
                    <div>
                        <h3 class="text-base font-bold text-[#1E293B]">{{ $ind['nama'] }}</h3>
                        <p class="text-sm text-[#64748B] mt-1">{{ $ind['deskripsi'] }}</p>
                    </div>
                </div>

                <!-- Info Satuan (Pengganti Ikon Action) -->
                <div class="shrink-0">
                    <span class="px-3 py-1 bg-slate-100 text-slate-600 text-xs font-semibold rounded-md border border-slate-200">
                        Satuan: {{ $ind['satuan'] }}
                    </span>
                </div>

            </div>
        @endforeach

    </div>
@endsection