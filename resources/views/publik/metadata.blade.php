<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Metadata Indikator - WebGIS PALI</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    @include('components.frontend.navbar')

    <main class="max-w-4xl mx-auto p-6 md:p-12 my-8 bg-white rounded-2xl shadow-sm border border-slate-100">
        
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 mb-8 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Peta
        </a>

        <h1 class="text-3xl font-extrabold text-[#1E3A8A] mb-2">Metadata Indikator Pemetaan</h1>
        <p class="text-slate-500 mb-8">Definisi operasional dari 8 variabel yang digunakan dalam algoritma K-Means Clustering.</p>

        <div class="space-y-6">
            @foreach($indikators as $index => $ind)
                <div class="flex gap-4 p-6 bg-slate-50 rounded-xl border border-slate-100 hover:border-blue-200 transition">
                    <div class="w-10 h-10 shrink-0 bg-white border border-slate-200 text-[#1E3A8A] font-bold text-lg rounded-full flex items-center justify-center shadow-sm">
                        {{ $index + 1 }}
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-[#1E293B] mb-1">{{ $ind['nama'] }}</h3>
                        <p class="text-sm font-semibold text-orange-600 mb-2">Satuan: {{ $ind['satuan'] }}</p>
                        <p class="text-slate-600 leading-relaxed">{{ $ind['deskripsi'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12 p-6 bg-blue-50 rounded-xl border border-blue-100 text-sm text-blue-900 leading-relaxed text-justify">
            <b>Catatan Sumber Data:</b><br>
            Seluruh data indikator di atas diperoleh dari hasil pendataan <i>Potensi Desa (Podes)</i> yang diselenggarakan secara berkala oleh Badan Pusat Statistik. Data ini dikumpulkan untuk memotret kondisi infrastruktur, ekonomi, dan sosial-budaya di tingkat pemerintahan terkecil guna menunjang perencanaan pembangunan nasional dan daerah.
        </div>
    </main>

    @include('components.frontend.footer')
</body>
</html>