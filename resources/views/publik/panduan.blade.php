<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Panduan Penggunaan - WebGIS PALI</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-800">

    @include('components.frontend.navbar')

    <main class="max-w-4xl mx-auto p-6 md:p-12 my-8 bg-white rounded-2xl shadow-sm border border-slate-100">
        
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 mb-8 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali ke Peta
        </a>

        <div class="flex items-center gap-4 mb-8">
            <div class="p-3 bg-orange-100 rounded-xl"><svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg></div>
            <div>
                <h1 class="text-3xl font-extrabold text-[#1E3A8A]">Buku Panduan Penggunaan WebGIS</h1>
                <p class="text-slate-500">Cara membaca peta dan memanfaatkan fitur sistem.</p>
            </div>
        </div>

        <div class="space-y-10">
            <!-- Section 1 -->
            <section>
                <h2 class="text-xl font-bold text-[#1E293B] mb-3 flex items-center gap-2"><span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">1</span> Membaca Peta Choropleth (Gradasi Warna)</h2>
                <p class="text-slate-600 leading-relaxed mb-4 text-justify">Sistem ini menggunakan teknik kartografi <i>Choropleth</i> monokromatik biru. Semakin pekat warna biru pada suatu poligon wilayah, maka semakin tinggi tingkat kesejahteraannya. Wilayah dengan warna biru sangat pucat menandakan daerah tersebut berstatus "Perlu Perhatian".</p>
                <div class="flex gap-4 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <div class="flex flex-col items-center"><div class="w-16 h-8 bg-[#08519C] rounded shadow-sm"></div><span class="text-xs mt-1 font-bold">Sejahtera</span></div>
                    <div class="flex flex-col items-center"><div class="w-16 h-8 bg-[#6BAED6] rounded shadow-sm"></div><span class="text-xs mt-1 font-bold">Berkembang</span></div>
                    <div class="flex flex-col items-center"><div class="w-16 h-8 bg-[#9ECAE1] rounded shadow-sm"></div><span class="text-xs mt-1 font-bold">Perhatian</span></div>
                </div>
            </section>

            <!-- Section 2 -->
            <section>
                <h2 class="text-xl font-bold text-[#1E293B] mb-3 flex items-center gap-2"><span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">2</span> Fitur Filter (Panel Kanan)</h2>
                <p class="text-slate-600 leading-relaxed text-justify">Gunakan panel kontrol di sebelah kanan peta untuk melakukan analisis mendalam. Anda dapat melihat histori pemetaan dengan mengganti <b>Tahun Laporan</b>. Anda juga dapat melakukan <i>filtering</i> dengan menghilangkan centang pada klaster tertentu untuk menyoroti (highlight) wilayah-wilayah yang spesifik.</p>
            </section>

            <!-- Section 3 -->
            <section>
                <h2 class="text-xl font-bold text-[#1E293B] mb-3 flex items-center gap-2"><span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm">3</span> Fitur Drill-Down (Membedah Wilayah)</h2>
                <p class="text-slate-600 leading-relaxed text-justify mb-4">Secara default, peta menampilkan agregasi tingkat Kecamatan. Untuk membedahnya menjadi tingkat desa, ubah "Tampilan Wilayah" pada panel kanan menjadi <b>Tingkat Desa / Kelurahan</b>. Klik salah satu pulau di peta, maka sebuah panel pintar akan meluncur dari kanan menampilkan 8 detail angka indikator dan analisis kesimpulan dari sistem AI kami.</p>
            </section>
        </div>

    </main>

    @include('components.frontend.footer')
</body>
</html>