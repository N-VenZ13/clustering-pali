<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - WebGIS PALI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <!-- Card Login Utama -->
    <div class="w-full max-w-4xl bg-white rounded-[24px] shadow-[0_10px_30px_rgba(0,0,0,0.08)] flex flex-col md:flex-row overflow-hidden min-h-[500px]">
        
        <!-- Sisi Kiri: Form Login -->
        <div class="w-full md:w-1/2 p-10 lg:p-14 flex flex-col justify-center bg-white">
            <div class="mb-10">
                <h2 class="text-3xl font-extrabold text-[#1E293B] tracking-tight">Welcome Back</h2>
                <p class="text-[#64748B] mt-2 font-medium">Silakan masuk ke akun Admin/Pimpinan.</p>
            </div>

            <!-- Pesan Error Bawaan Laravel -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Input Username -->
                <div>
                    <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus autocomplete="username" 
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] outline-none transition placeholder:text-gray-400 font-medium text-[#1E293B]" 
                        placeholder="Username">
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Input Password -->
                <div>
                    <input id="password" type="password" name="password" required autocomplete="current-password" 
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-[#F97316]/20 focus:border-[#F97316] outline-none transition placeholder:text-gray-400 font-medium text-[#1E293B]" 
                        placeholder="Password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Opsi Remember Me (Disembunyikan tapi aktif) -->
                <div class="block hidden">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <!-- Tombol Login -->
                <button type="submit" class="w-full bg-[#F97316] hover:bg-[#EA580C] text-white font-bold py-3.5 px-4 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 tracking-wide">
                    Login
                </button>
            </form>
        </div>

        <!-- Sisi Kanan: Branding / Gambar (Sembunyi di HP, Tampil di Laptop) -->
        <div class="hidden md:flex w-full md:w-1/2 bg-[#1E3A8A] flex-col items-center justify-center p-12 relative overflow-hidden">
            <!-- Dekorasi Lingkaran Abstrak -->
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-blue-600/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-blue-400/20 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 text-center flex flex-col items-center">
                <!-- Ganti dengan Logo BPS / PALI nanti -->
                <div class="w-24 h-24 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 flex items-center justify-center mb-8 shadow-2xl shadow-blue-900/50">
                    <span class="text-4xl font-black text-white">P</span>
                </div>
                
                <h1 class="text-3xl font-bold text-white mb-2 leading-tight">Sistem Pemetaan<br>Kesejahteraan Sosial</h1>
                <div class="w-16 h-1 bg-[#F97316] rounded-full mx-auto my-6"></div>
                <p class="text-blue-100 font-medium tracking-wide">Kabupaten Penukal Abab Lematang Ilir</p>
                <p class="text-blue-200/60 text-sm mt-1">Menggunakan Algoritma K-Means</p>
            </div>
        </div>

    </div>
</body>
</html>