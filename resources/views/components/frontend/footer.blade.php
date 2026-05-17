<footer class="bg-white border-t border-gray-200 py-6 px-8 mt-12 relative z-50">
    <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-4">
        <p class="text-sm text-slate-500">
            <!-- Teks Copyright ini berfungsi sebagai Link Rahasia Login -->
            <a href="{{ route('login') }}" class="hover:text-[#1E3A8A] transition cursor-pointer">©</a> 
            {{ date('Y') }} Badan Pusat Statistik Kabupaten PALI. All rights reserved.
        </p>
        <div class="flex items-center gap-2">
            <img src="{{ asset('images/logo.png') }}" alt="BPS" class="h-6 opacity-70 grayscale hover:grayscale-0 transition">
        </div>
    </div>
</footer>