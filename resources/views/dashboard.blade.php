@extends('layouts.admin')

@section('title', 'DASHBOARD')

@section('content')
    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Total Desa -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#1E3A8A]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Total Desa</p>
            <h3 class="text-4xl font-bold text-[#1E293B] ml-2">71</h3>
        </div>

        <!-- Card 2: Klaster Sejahtera -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#10B981]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Klaster Sejahtera</p>
            <h3 class="text-4xl font-bold text-[#10B981] ml-2">16</h3>
        </div>

        <!-- Card 3: Klaster Berkembang -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#F59E0B]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Klaster Berkembang</p>
            <h3 class="text-4xl font-bold text-[#F59E0B] ml-2">35</h3>
        </div>

        <!-- Card 4: Perlu Perhatian -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#EF4444]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Perlu Perhatian</p>
            <h3 class="text-4xl font-bold text-[#EF4444] ml-2">20</h3>
        </div>

    </div>

    <!-- Area Grafik & Keterangan (Dummy Area) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-6 min-h-[300px] flex items-center justify-center">
            <p class="text-slate-400">Area Line Chart K-Means</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 min-h-[300px] flex items-center justify-center">
            <p class="text-slate-400">Area Bar Chart</p>
        </div>
    </div>
@endsection