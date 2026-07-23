@extends('layouts.admin')

@section('title', 'DASHBOARD')

@section('content')

    <!-- Header & Filter Tahun -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        <h2 class="text-lg font-bold text-[#1E293B]">Ringkasan Statistik Pemetaan</h2>
        
        <form action="{{ route('dashboard') }}" method="GET" class="flex items-center gap-2">
            <label class="text-sm font-semibold text-slate-500">Tahun Data:</label>
            <div class="relative">
                <select name="tahun" onchange="this.form.submit()" class="appearance-none bg-blue-50 border border-blue-200 text-[#1E3A8A] font-bold text-sm rounded-lg pl-3 pr-8 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                    @foreach($db_years as $thn)
                        <option value="{{ $thn }}" {{ $tahun_aktif == $thn ? 'selected' : '' }}>Tahun {{ $thn }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#1E3A8A]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Total Desa -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#22c55e]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Total Desa / Kelurahan</p>
            <h3 class="text-4xl font-bold text-[#1E293B] ml-2">{{ $summary['total_desa'] }}</h3>
        </div>

        <!-- Card 2: Klaster Sejahtera -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#08519C]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Klaster Sejahtera</p>
            <h3 class="text-4xl font-bold text-[#1E293B] ml-2">{{ $summary['sejahtera'] }}</h3>
        </div>

        <!-- Card 3: Klaster Berkembang -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#6BAED6]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Klaster Berkembang</p>
            <h3 class="text-4xl font-bold text-[#1E293B] ml-2">{{ $summary['berkembang'] }}</h3>
        </div>

        <!-- Card 4: Perlu Perhatian -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#9ECAE1]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Perlu Perhatian</p>
            <!-- Warna teks diubah menjadi green-800 agar bisa dibaca di layar putih -->
            <h3 class="text-4xl font-bold text-[#1E293B] ml-2">{{ $summary['perhatian'] }}</h3>
        </div>

    </div>

    <!-- Area Grafik Menggunakan Chart.js -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Kotak Kiri: Grafik -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 min-h-[350px] relative flex flex-col">
            <h3 class="text-sm font-bold text-[#1E3A8A] mb-4 uppercase tracking-wider shrink-0">Komposisi Desa Berdasarkan Klaster</h3>
            
            <!-- Kanvas tempat Chart.js menggambar (Menyimpan data PHP di sini) -->
            <div class="flex-1 relative w-full">
                <canvas id="klasterChart" class="absolute inset-0 w-full h-full"
                    data-sejahtera="{{ $summary['sejahtera'] }}"
                    data-berkembang="{{ $summary['berkembang'] }}"
                    data-perhatian="{{ $summary['perhatian'] }}">
                </canvas>
            </div>
            
            <!-- Pesan jika data kosong -->
            @if($summary['sejahtera'] == 0 && $summary['berkembang'] == 0 && $summary['perhatian'] == 0)
                <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/95 z-10 rounded-xl">
                    <p class="text-slate-500 font-bold mb-1">Belum Ada Data Klaster</p>
                    <p class="text-xs text-slate-400">Silakan jalankan algoritma K-Means untuk tahun ini.</p>
                </div>
            @endif
        </div>

        <!-- Kotak Kanan: Insight Cepat -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-sm font-bold text-[#1E3A8A] mb-4 uppercase tracking-wider flex items-center gap-2">
                <span>🤖</span> Insight Sistem
            </h3>
            
            <div class="space-y-4">
                <p class="text-slate-600 text-sm leading-relaxed">
                    Sistem saat ini mendeteksi total <b class="text-[#1E293B]">{{ $summary['total_desa'] }} Desa</b> yang terdaftar di dalam database Kabupaten Penukal Abab Lematang Ilir.
                </p>
                
                @php
                    $totalDiproses = $summary['sejahtera'] + $summary['berkembang'] + $summary['perhatian'];
                @endphp

                @if($totalDiproses == 0)
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800 font-medium">Data indikator untuk tahun {{ $tahun_aktif }} belum diproses. Buka menu <b>Clustering > Data K-Means</b> untuk memulai pemetaan.</p>
                    </div>
                @else
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-sm text-blue-800">
                            Berdasarkan perhitungan Machine Learning tahun <b>{{ $tahun_aktif }}</b>, komposisi wilayah saat ini adalah:
                            <ul class="list-disc ml-5 mt-3 space-y-1.5 font-bold">
                                <li>{{ round(($summary['sejahtera'] / $totalDiproses) * 100, 1) }}% Sejahtera</li>
                                <li>{{ round(($summary['berkembang'] / $totalDiproses) * 100, 1) }}% Berkembang</li>
                                <li>{{ round(($summary['perhatian'] / $totalDiproses) * 100, 1) }}% Perlu Perhatian</li>
                            </ul>
                        </p>
                        <p class="text-xs text-blue-600 mt-4 italic">*Persentase dihitung dari total {{ $totalDiproses }} desa yang datanya telah diproses K-Means.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Panggil CDN Chart.js dan Inisialisasi Grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('klasterChart');
            
            if (ctx) {
                const jmlSejahtera = parseInt(ctx.getAttribute('data-sejahtera'));
                const jmlBerkembang = parseInt(ctx.getAttribute('data-berkembang'));
                const jmlPerhatian = parseInt(ctx.getAttribute('data-perhatian'));
                
                const totalData = jmlSejahtera + jmlBerkembang + jmlPerhatian;

                // Render grafik hanya jika ada data
                if (totalData > 0) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['Sejahtera', 'Berkembang', 'Perlu Perhatian'],
                            datasets: [{
                                label: 'Jumlah Desa',
                                data: [jmlSejahtera, jmlBerkembang, jmlPerhatian],
                                backgroundColor: ['#08519C', '#6BAED6', '#9ECAE1'],
                                borderRadius: 6,
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) { return context.parsed.y + ' Desa'; }
                                    }
                                }
                            },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }
            }
        });
    </script>
@endsection