@extends('layouts.admin')

@section('title', 'DASHBOARD')

@section('content')
    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Total Desa -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#1E3A8A]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Total Desa</p>
            <h3 class="text-4xl font-bold text-[#1E293B] ml-2">{{ $summary['total_desa'] }}</h3>
        </div>

        <!-- Card 2: Klaster Sejahtera -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#14532d]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Klaster Sejahtera</p>
            <h3 class="text-4xl font-bold text-[#14532d] ml-2">{{ $summary['sejahtera'] }}</h3>
        </div>

        <!-- Card 3: Klaster Berkembang -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#22c55e]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Klaster Berkembang</p>
            <h3 class="text-4xl font-bold text-[#22c55e] ml-2">{{ $summary['berkembang'] }}</h3>
        </div>

        <!-- Card 4: Perlu Perhatian -->
        <div class="bg-white rounded-xl shadow-sm p-6 relative overflow-hidden border border-slate-100">
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#bbf7d0]"></div>
            <p class="text-sm font-semibold text-[#64748B] mb-2 ml-2">Perlu Perhatian</p>
            <h3 class="text-4xl font-bold text-[#bbf7d0] ml-2">{{ $summary['perhatian'] }}</h3>
        </div>

    </div>

    <!-- Area Grafik Menggunakan Chart.js -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kotak Kiri: Grafik -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 min-h-[350px] relative">
            <h3 class="text-sm font-bold text-[#1E3A8A] mb-4 uppercase tracking-wider">Komposisi Desa Berdasarkan Klaster</h3>
            
            <!-- Kanvas tempat Chart.js menggambar (Menyimpan data PHP di sini) -->
            <canvas id="klasterChart" class="w-full max-h-[250px]"
                data-sejahtera="{{ $summary['sejahtera'] }}"
                data-berkembang="{{ $summary['berkembang'] }}"
                data-perhatian="{{ $summary['perhatian'] }}">
            </canvas>
            
            <!-- Pesan jika data kosong -->
            @if($summary['sejahtera'] == 0 && $summary['berkembang'] == 0 && $summary['perhatian'] == 0)
                <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/90 z-10">
                    <p class="text-slate-400 font-medium">Belum ada data klaster.</p>
                    <p class="text-xs text-slate-400">Silakan jalankan algoritma K-Means terlebih dahulu.</p>
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
                
                @if($summary['sejahtera'] == 0 && $summary['berkembang'] == 0 && $summary['perhatian'] == 0)
                    <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <p class="text-sm text-yellow-800 font-medium">Data indikator tahun ini belum diproses. Buka menu <b>Clustering > Proses K-Means</b> untuk memulai pemetaan.</p>
                    </div>
                @else
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <p class="text-sm text-blue-800">
                            Berdasarkan perhitungan Machine Learning, komposisi wilayah saat ini adalah:
                            <ul class="list-disc ml-5 mt-2 space-y-1 font-medium">
                                <li>{{ round(($summary['sejahtera'] / $summary['total_desa']) * 100, 1) }}% Sejahtera</li>
                                <li>{{ round(($summary['berkembang'] / $summary['total_desa']) * 100, 1) }}% Berkembang</li>
                                <li>{{ round(($summary['perhatian'] / $summary['total_desa']) * 100, 1) }}% Perlu Perhatian</li>
                            </ul>
                        </p>
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
                // Baca data dari atribut HTML (Tidak ada tag PHP di dalam Javascript lagi!)
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
                                backgroundColor: ['#14532d', '#22c55e', '#bbf7d0'],
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