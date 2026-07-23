<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pemetaan Kesejahteraan - BPS PALI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased h-screen overflow-hidden flex flex-col">

    <!-- Navbar -->
    @include('components.frontend.navbar')

    <!-- Konten Kosong -->
    <div class="flex-1 flex flex-col items-center justify-center text-center p-6">
        
        <div class="bg-white p-12 rounded-2xl shadow-sm border border-slate-100 max-w-lg">
            <svg class="w-20 h-20 text-orange-400 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            
            <h2 class="text-2xl font-bold text-[#1E3A8A] mb-2">Peta Tahun {{ $tahun_aktif }} Belum Tersedia</h2>
            
            <p class="text-slate-500 mb-8 text-sm leading-relaxed">
                Laporan pemetaan kesejahteraan sosial untuk tahun <b>{{ $tahun_aktif }}</b> saat ini masih dalam tahap penyusunan dan belum mendapatkan persetujuan resmi (Published) dari Pimpinan Badan Pusat Statistik Kabupaten PALI.
            </p>

            <!-- Dropdown Pilihan Tahun (Agar user tidak terjebak) -->
            <form action="{{ route('home') }}" method="GET" class="inline-block relative">
                <select name="tahun" onchange="this.form.submit()" class="appearance-none bg-blue-50 border border-blue-200 text-[#1E3A8A] font-bold text-sm rounded-lg pl-4 pr-10 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                    <option value="">-- Lihat Tahun Lainnya --</option>
                    @foreach($list_tahun as $thn)
                        <option value="{{ $thn }}" {{ $tahun_aktif == $thn ? 'selected' : '' }}>Tahun Laporan: {{ $thn }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-[#1E3A8A]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </form>
        </div>

    </div>

    <!-- Footer -->
    @include('components.frontend.footer')
</body>
</html>