<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebGIS PALI - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans antialiased bg-[#F8FAFC] text-[#1E293B]">

    <!-- Alpine.js State dengan Global Event Listener -->
    <div x-data="{ 
            sidebarOpen: true, 
            confirmModal: false, 
            confirmFormId: '', 
            confirmTitle: '', 
            confirmMessage: '', 
            confirmBtnText: '', 
            confirmBtnColor: '' 
         }"
        @open-confirm.window="
            confirmTitle = $event.detail.title;
            confirmMessage = $event.detail.msg;
            confirmBtnText = $event.detail.btnText;
            confirmBtnColor = $event.detail.btnColor;
            confirmFormId = $event.detail.formId;
            confirmModal = true;
         "
        class="min-h-screen flex relative">

        <!-- SIDEBAR (Kiri) -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-[#1E3A8A] text-white flex flex-col shadow-xl z-20 hidden md:flex transition-all duration-300">
            <!-- Logo & Toggle Button -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-white/10">
                <div class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
                    <!-- Icon Peta / Logo -->
                    <!-- <svg class="w-8 h-8 flex-shrink-0 text-orange-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg> -->
                    <span x-show="sidebarOpen" class="text-xl font-bold tracking-wider">WebGIS PALI</span>
                </div>
                <!-- Toggle Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="p-1 text-gray-300 hover:text-white focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Menu Navigasi -->
            <nav class="flex-1 px-3 py-6 space-y-2 overflow-hidden">

                <!-- Menu Dashboard -->
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg transition group {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white font-medium' : 'text-[#CBD5E1] hover:bg-white/5 hover:text-white' }}" title="Dashboard">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Dashboard</span>
                </a>

                <!-- Menu Data Wilayah -->
                <a href="{{ route('wilayah.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg transition group {{ request()->routeIs('wilayah.*', 'kecamatan.*', 'desa.*') ? 'bg-white/10 text-white font-medium' : 'text-[#CBD5E1] hover:bg-white/5 hover:text-white' }}" title="Data Wilayah">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Data Wilayah</span>
                </a>

                <!-- Menu CLUSTERING (Dropdown) -->
                @php $isClustering = request()->routeIs('indikator.*', 'kmeans.*'); @endphp
                <div x-data="{ openCluster: {{ $isClustering ? 'true' : 'false' }} }" class="space-y-1">
                    <button @click="openCluster = !openCluster" class="w-full flex items-center justify-between px-3 py-3 rounded-lg transition group {{ $isClustering ? 'bg-white/10 text-white font-medium' : 'text-[#CBD5E1] hover:bg-white/5 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span x-show="sidebarOpen" class="whitespace-nowrap">Clustering</span>
                        </div>
                        <svg x-show="sidebarOpen" :class="openCluster ? 'rotate-180' : ''" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="openCluster && sidebarOpen" x-collapse class="pl-11 pr-3 space-y-1">
                        <a href="{{ route('indikator.index') }}" class="block px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('indikator.*') ? 'bg-white/20 text-white font-bold' : 'text-[#CBD5E1] hover:bg-white/10 hover:text-white' }}">Kamus Indikator</a>
                        <a href="{{ route('kmeans.index') }}" class="block px-3 py-2 rounded-lg text-sm transition {{ request()->routeIs('kmeans.*') ? 'bg-white/20 text-white font-bold' : 'text-[#CBD5E1] hover:bg-white/10 hover:text-white' }}">Proses K-Means</a>
                    </div>
                </div>

                <!-- Menu Data User -->
                <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg transition group {{ request()->routeIs('users.*') ? 'bg-white/10 text-white font-medium' : 'text-[#CBD5E1] hover:bg-white/5 hover:text-white' }}" title="Data User">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Data User</span>
                </a>

                <!-- Menu Laporan -->
                <a href="{{ route('laporan.index') }}" class="flex items-center gap-3 px-3 py-3 rounded-lg transition group {{ request()->routeIs('laporan.*') ? 'bg-white/10 text-white font-medium' : 'text-[#CBD5E1] hover:bg-white/5 hover:text-white' }}" title="Laporan">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap">Laporan</span>
                </a>
            </nav>
        </aside>

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-1 flex flex-col min-w-0 transition-all duration-300">
            <!-- Header / Topbar -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10">
                <h1 class="text-xl font-bold text-[#334155]">@yield('title')</h1>

                <!-- Logo Topbar -->
                    <!-- <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-full border border-slate-200 object-contain p-0.5"> -->

                <!-- Profile / Logout -->
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-[#64748B]">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-500 font-semibold hover:text-red-700">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Area Dinamis -->
            <div class="p-8 flex-1 overflow-y-auto">

                <!-- GLOBAL ALERT COMPONENTS -->
                @if(session('success'))
                <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-start gap-3 shadow-sm">
                    <svg class="w-5 h-5 text-red-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
                </div>
                @endif
                <!-- END GLOBAL ALERT -->

                @yield('content')
            </div>
        </main>

        <!-- ======================= GLOBAL CONFIRMATION MODAL ======================= -->
        <div x-show="confirmModal" class="fixed inset-0 z-[99] flex items-center justify-center" x-cloak>
            <!-- Background Overlay -->
            <div x-show="confirmModal" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="confirmModal = false"></div>

            <!-- Modal Box -->
            <div x-show="confirmModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 overflow-hidden mx-4 text-center">

                <!-- Icon Peringatan (Kuning/Merah dinamis) -->
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full mb-4" :class="confirmBtnColor == 'red' ? 'bg-red-100' : 'bg-orange-100'">
                    <svg :class="confirmBtnColor == 'red' ? 'text-red-600' : 'text-orange-600'" class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <!-- Teks Dinamis -->
                <h3 class="text-xl font-bold text-slate-800 mb-2" x-text="confirmTitle"></h3>
                <p class="text-slate-500 text-sm mb-8" x-text="confirmMessage"></p>

                <!-- Tombol Aksi -->
                <div class="flex items-center justify-center gap-3">
                    <button type="button" @click="confirmModal = false" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-xl hover:bg-slate-50 transition w-full">Batal</button>

                    <!-- Tombol ini yang akan meng-submit form rahasia -->
                    <button type="button"
                        @click="document.getElementById(confirmFormId).submit(); confirmModal = false;"
                        :class="confirmBtnColor == 'red' ? 'bg-red-500 hover:bg-red-600' : 'bg-orange-500 hover:bg-orange-600'"
                        class="px-5 py-2.5 text-white font-semibold rounded-xl transition w-full shadow-sm" x-text="confirmBtnText">
                    </button>
                </div>
            </div>
        </div>

    </div>
</body>

</html>