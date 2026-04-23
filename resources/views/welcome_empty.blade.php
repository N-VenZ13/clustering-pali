<!DOCTYPE html>
<html>
<head>
    <title>WebGIS Kesejahteraan PALI</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 flex flex-col items-center justify-center min-h-screen">
    <div class="text-center max-w-lg">
        <h1 class="text-3xl font-bold text-[#1E3A8A] mb-4">WebGIS Pemetaan Kesejahteraan</h1>
        <p class="text-slate-500 mb-8">Peta belum dapat ditampilkan karena Laporan K-Means tahun ini sedang dalam tahap evaluasi dan persetujuan oleh Kepala BPS PALI.</p>
        <a href="{{ route('login') }}" class="bg-[#F97316] text-white px-6 py-3 rounded-lg font-bold">Login Admin</a>
    </div>
</body>
</html>