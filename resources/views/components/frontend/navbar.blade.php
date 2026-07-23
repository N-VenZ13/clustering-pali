<nav class="h-[70px] bg-[#1E3A8A] shadow-lg flex items-center justify-between px-6 sticky top-0 z-[100] border-b border-blue-900">
    <!-- Kiri: Logo & Title -->
    <a href="{{ route('home') }}" class="flex items-center gap-4 hover:opacity-90 transition cursor-pointer">
        <div class="w-10 h-10 flex items-center justify-center bg-transparent rounded-md p-1 shadow-sm">
            <img src="{{ asset('images/logo.png') }}" alt="Logo BPS" class="w-full h-full object-contain">
        </div>
        <div>
            <h1 class="text-lg md:text-xl font-bold text-white leading-tight">Sistem Pemetaan Kesejahteraan Sosial</h1>
            <p class="text-[11px] md:text-xs text-blue-200 font-semibold tracking-wide">BPS Kabupaten Penukal Abab Lematang Ilir</p>
        </div>
    </a>

    <!-- Kanan: Menu Edukasi, Bahasa & Layanan BPS -->
    <div class="flex items-center gap-3 md:gap-5">
        
        <!-- Pilihan Bahasa (Dropdown CSS Murni) -->
        <div class="relative hidden md:block group z-50">
            <!-- Tombol Pemicu -->
            <!-- <button type="button" class="flex items-center gap-2 text-sm font-semibold text-white px-3 py-1.5 transition rounded bg-white/10 hover:bg-white/20 border border-white/20 cursor-default">
               
                <img src="{{ asset('images/flag-id.png') }}" alt="ID" class="w-6 h-4 object-cover rounded-sm border border-white/20 shadow-sm" onerror="this.style.display='none'">
                <span>ID</span> 
                <svg class="w-4 h-4 opacity-70 transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
             -->
            <!-- Isi Dropdown -->
            <div class="absolute right-0 pt-2 w-36 origin-top-right opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                <div class="rounded-md bg-white shadow-xl ring-1 ring-black ring-opacity-5 overflow-hidden border border-slate-100">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-800 bg-slate-50 hover:bg-slate-100 border-b border-gray-100 transition">
                        <img src="{{ asset('images/flag-id.png') }}" alt="ID" class="w-5 h-3.5 object-cover rounded-sm shadow-sm" onerror="this.style.display='none'"> ID (Indo)
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-500 hover:bg-slate-50 hover:text-gray-800 transition">
                        <img src="{{ asset('images/flag-en.png') }}" alt="EN" class="w-5 h-3.5 object-cover rounded-sm shadow-sm" onerror="this.style.display='none'"> EN (Eng)
                    </a>
                </div>
            </div>
        </div>
        
        <div class="hidden md:block w-px h-6 bg-blue-700"></div>

        <!-- Tombol Layanan Statistik (Eksternal) -->
        <a href="https://palikab.bps.go.id/id" target="_blank" class="hidden lg:flex text-sm font-semibold text-blue-100 hover:text-white items-center gap-2 transition px-2 py-1 group">
            <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
            Layanan Statistik
        </a>

        <!-- Tombol Halaman Metadata -->
        <a href="{{ route('publik.metadata') }}" class="text-sm font-semibold text-blue-100 hover:text-white flex items-center gap-2 transition px-2 py-1 group">
            <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="hidden md:block">Metadata</span>
        </a>

        <!-- Tombol Ikon Panduan Penggunaan -->
        <a href="{{ route('publik.panduan') }}" class="text-sm font-semibold text-[#1E3A8A] bg-white hover:bg-gray-100 p-2 rounded-lg flex items-center justify-center transition shadow-md border border-gray-200" title="Buku Panduan WebGIS">
            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </a>

        <!-- Ikon Tentang (Anchor Link ke Bawah Peta) -->
        <a href="#tentang-webgis" class="w-8 h-8 rounded-full bg-blue-800 hover:bg-blue-700 text-white flex items-center justify-center transition shadow-sm border border-blue-600" title="Tentang WebGIS">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </a>
    </div>
</nav>