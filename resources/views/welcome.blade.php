<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebGIS Kesejahteraan PALI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* Agar peta penuh satu layar di bawah navbar */
        #map { height: calc(100vh - 80px); width: 100%; z-index: 10;}
        /* Animasi Panel Kanan */
        .panel-slide { transition: transform 0.4s ease-in-out; }
        .panel-hidden { transform: translateX(100%); }
        .panel-visible { transform: translateX(0); }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased overflow-hidden">

    <!-- NAVBAR (Header Publik) -->
    <nav class="h-[80px] bg-white shadow-md flex items-center justify-between px-8 relative z-50">
        <div class="flex items-center gap-4">
            
             <!-- Logo BPS -->
            <div class="w-12 h-12 flex-shrink-0 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo BPS" class="w-full h-full object-contain">
            </div>
            <div>
                <h1 class="text-xl font-bold text-[#1E293B]">Sistem Pemetaan Kesejahteraan Sosial</h1>
                <p class="text-xs text-slate-500 font-semibold">Badan Pusat Statistik Kab. Penukal Abab Lematang Ilir</p>
            </div>
        </div>
        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="bg-[#1E3A8A] hover:bg-blue-800 text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md">Masuk Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="bg-[#F97316] hover:bg-orange-600 text-white font-semibold py-2.5 px-6 rounded-lg transition shadow-md">Login Admin</a>
            @endauth
        </div>
    </nav>

    <!-- WRAPPER PETA & PANEL -->
    <div class="relative w-full">
        
        <!-- KANVAS PETA -->
        <div id="map"></div>

        <!-- FILTER TAHUN (Melayang Kanan Atas) -->
        <div class="absolute top-6 right-6 z-40 bg-white p-3 rounded-xl shadow-lg border border-slate-100 flex items-center gap-3">
            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <form action="{{ route('home') }}" method="GET">
                <select name="tahun" onchange="this.form.submit()" class="border-none bg-transparent text-[#1E293B] font-bold focus:ring-0 cursor-pointer text-sm">
                    @foreach($list_tahun as $thn)
                        <option value="{{ $thn }}" {{ $tahun_aktif == $thn ? 'selected' : '' }}>Pilih Tahun : {{ $thn }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- LEGEND / KETERANGAN WARNA (Melayang Kiri Bawah) -->
        <div class="absolute bottom-10 left-6 z-40 bg-white p-4 rounded-xl shadow-lg border border-slate-100">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Keterangan Klaster</h4>
            <div class="space-y-2">
                <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-full bg-[#10B981] border-2 border-white shadow-sm"></span><span class="text-sm font-semibold text-[#1E293B]">Klaster I (Sejahtera)</span></div>
                <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-full bg-[#F59E0B] border-2 border-white shadow-sm"></span><span class="text-sm font-semibold text-[#1E293B]">Klaster II (Berkembang)</span></div>
                <div class="flex items-center gap-3"><span class="w-4 h-4 rounded-full bg-[#EF4444] border-2 border-white shadow-sm"></span><span class="text-sm font-semibold text-[#1E293B]">Klaster III (Perlu Perhatian)</span></div>
            </div>
        </div>

        <!-- DYNAMIC PANEL KANAN (Info Kecamatan & Desa) -->
        <div id="info-panel" class="absolute top-0 right-0 h-full w-[400px] bg-white shadow-[-10px_0_30px_rgba(0,0,0,0.1)] z-40 panel-slide panel-hidden flex flex-col">
            <!-- Header Panel -->
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-[#F8FAFC]">
                <h2 id="panel-title" class="text-2xl font-bold text-[#1E293B]">Pilih Kecamatan</h2>
                <button onclick="closePanel()" class="p-2 text-slate-400 hover:text-red-500 transition rounded-full hover:bg-red-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Konten Panel (Bisa di-scroll) -->
            <div class="p-6 flex-1 overflow-y-auto">
                <!-- Status Badge -->
                <div id="panel-status-box" class="inline-block px-4 py-1.5 rounded-lg text-sm font-bold mb-6"></div>
                
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 border-b pb-2">Komposisi Desa (Tahun {{ $tahun_aktif }})</h3>
                
                <!-- List Desa disuntikkan lewat JS -->
                <div id="panel-desa-list" class="space-y-3">
                    <p class="text-sm text-slate-500 italic">Klik salah satu wilayah di peta untuk melihat data detail desa.</p>
                </div>

                <!-- Insight Box -->
                <!-- <div class="mt-8 p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <div class="flex items-center gap-2 mb-2">
                        <span>🤖</span><h4 class="font-bold text-[#1E3A8A]">Insight K-Means</h4>
                    </div>
                    <p class="text-sm text-blue-800 leading-relaxed">Status wilayah ini didapatkan menggunakan metode Bottom-Up Aggregation berdasarkan modus (mayoritas) nilai klaster desa-desa di dalamnya.</p>
                </div> -->
            </div>
        </div>

    </div>

    <!-- Sembunyikan Data PHP di sini (VS Code tidak akan error) -->
    <script id="data-kecamatan" type="application/json">
        {!! json_encode($kecamatans) !!}
    </script>

    <!-- Leaflet JS & Logika Peta -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // 1. TERIMA DATA DARI HTML
        const dbData = JSON.parse(document.getElementById('data-kecamatan').textContent);

        // 2. INISIALISASI PETA
        const map = L.map('map', { zoomControl: false }).setView([-3.25, 104.0], 10);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: 'Map data © Google'
        }).addTo(map);

        function getColor(status) {
            if(status === 'Sejahtera') return '#10B981'; 
            if(status === 'Berkembang') return '#F59E0B'; 
            if(status === 'Perlu Perhatian') return '#EF4444'; 
            return '#94A3B8'; 
        }

        // Variabel penampung layer
        let kecamatanLayer;
        let desaLayer;
        const panel = document.getElementById('info-panel');

        // 3. MUAT PETA KECAMATAN (TAMPILAN AWAL)
        fetch('/geojson/kecamatan.json')
            .then(response => response.json())
            .then(data => {
                kecamatanLayer = L.geoJSON(data, {
                    style: function(feature) {
                        let namaKec = feature.properties.nm_kecamatan;
                        let matchDb = dbData.find(k => k.nama_kecamatan.toLowerCase() === namaKec.toLowerCase());
                        
                        let fillColor = '#94A3B8';
                        if(matchDb && matchDb.status_akhir) fillColor = getColor(matchDb.status_akhir);

                        return { fillColor: fillColor, weight: 2, opacity: 1, color: 'white', fillOpacity: 0.7 };
                    },
                    onEachFeature: function(feature, layer) {
                        // Tambahkan label Tooltip saat kursor di atas kecamatan
                        layer.bindTooltip("<div class='text-center'><b>Kec. " + feature.properties.nm_kecamatan + "</b><br>Klik untuk membedah desa</div>", {sticky: true});

                        layer.on('click', function(e) {
                            let namaKec = feature.properties.nm_kecamatan;
                            let kodeKec = feature.properties.kd_kecamatan; // KUNCI RAHASIA FILTER DESA
                            let matchDb = dbData.find(k => k.nama_kecamatan.toLowerCase() === namaKec.toLowerCase());
                            
                            // Zoom ke kecamatan yang diklik
                            map.fitBounds(layer.getBounds());
                            
                            // Buka panel & Bedah Desanya!
                            openPanel(namaKec, matchDb, kodeKec);
                        });

                        layer.on('mouseover', function() { this.setStyle({ fillOpacity: 0.9 }); });
                        layer.on('mouseout', function() { kecamatanLayer.resetStyle(this); });
                    }
                }).addTo(map);
                
                map.fitBounds(kecamatanLayer.getBounds());
            });

        // 4. FUNGSI MEMBEDAH DESA (DRILL-DOWN)
        function openPanel(namaKec, dbInfo, kodeKec) {
            // Tampilkan Panel Kanan
            panel.classList.remove('panel-hidden');
            panel.classList.add('panel-visible');

            // HILANGKAN Peta Kecamatan dari layar
            if(map.hasLayer(kecamatanLayer)) {
                map.removeLayer(kecamatanLayer);
            }

            // MUNCULKAN Peta Desa
            fetch('/geojson/desa.json')
                .then(response => response.json())
                .then(data => {
                    // Hapus layer desa sebelumnya jika ada
                    if(desaLayer && map.hasLayer(desaLayer)) {
                        map.removeLayer(desaLayer);
                    }

                    desaLayer = L.geoJSON(data, {
                        // FILTER MAGIS: Hanya ambil desa yang berada di dalam kecamatan yang diklik
                        filter: function(feature) {
                            return feature.properties.kd_kecamatan === kodeKec;
                        },
                        style: function(feature) {
                            let namaDesaGeo = feature.properties.nm_kelurahan;
                            let fillColor = '#94A3B8'; // Default abu-abu

                            // Cocokkan warna desa dengan hasil K-Means di Database
                            if(dbInfo && dbInfo.desas) {
                                let matchDesaDb = dbInfo.desas.find(d => d.nama_desa.toLowerCase() === namaDesaGeo.toLowerCase());
                                if(matchDesaDb && matchDesaDb.indikators.length > 0) {
                                    let klaster = matchDesaDb.indikators[0].klaster_hasil;
                                    if(klaster == 1) fillColor = '#10B981';
                                    if(klaster == 2) fillColor = '#F59E0B';
                                    if(klaster == 3) fillColor = '#EF4444';
                                }
                            }
                            return { fillColor: fillColor, weight: 1.5, opacity: 1, color: 'white', fillOpacity: 0.85 };
                        },
                        onEachFeature: function(feature, layer) {
                            layer.bindTooltip("<b>" + feature.properties.nm_kelurahan + "</b>", {sticky: true, direction: 'top'});
                            
                            layer.on('mouseover', function() { this.setStyle({ fillOpacity: 1, weight: 3 }); });
                            layer.on('mouseout', function() { desaLayer.resetStyle(this); });
                            
                            // --- TAMBAHKAN INI: Deteksi Klik Desa ---
                            layer.on('click', function(e) {
                                // Mencegah klik tembus ke bawah (Penting di Leaflet)
                                L.DomEvent.stopPropagation(e);
                                
                                let namaDesaGeo = feature.properties.nm_kelurahan;
                                // Cari data indikator desa tersebut dari variabel dbData
                                let dataDesaDb = null;
                                if(dbInfo && dbInfo.desas) {
                                    dataDesaDb = dbInfo.desas.find(d => d.nama_desa.toLowerCase() === namaDesaGeo.toLowerCase());
                                }
                                
                                // Panggil fungsi ubah panel
                                showDetailDesa(namaKec, dbInfo, kodeKec, namaDesaGeo, dataDesaDb);
                            });
                        }
                    }).addTo(map);
                });

            // --- RENDER TEKS PANEL KANAN (Sama seperti sebelumnya) ---
            document.getElementById('panel-title').innerText = "Kec. " + namaKec;
            let statusBox = document.getElementById('panel-status-box');
            if(dbInfo && dbInfo.status_akhir) {
                let colorClass = '';
                if(dbInfo.status_akhir == 'Sejahtera') colorClass = 'bg-[#10B981]/10 text-[#10B981] border-[#10B981]/30';
                if(dbInfo.status_akhir == 'Berkembang') colorClass = 'bg-[#F59E0B]/10 text-[#F59E0B] border-[#F59E0B]/30';
                if(dbInfo.status_akhir == 'Perlu Perhatian') colorClass = 'bg-[#EF4444]/10 text-[#EF4444] border-[#EF4444]/30';
                
                statusBox.className = `inline-block px-4 py-2 rounded-lg text-sm font-bold mb-6 border ${colorClass}`;
                statusBox.innerHTML = `Status: ${dbInfo.status_akhir} <br><span class="text-xs font-normal opacity-80">Skor Agregasi: ${dbInfo.skor_agregasi}</span>`;
            } else {
                statusBox.className = "inline-block px-4 py-2 rounded-lg text-sm font-bold mb-6 border bg-slate-100 text-slate-500";
                statusBox.innerHTML = "Belum ada data K-Means";
            }

            let listHTML = '';
            if(dbInfo && dbInfo.desas && dbInfo.desas.length > 0) {
                dbInfo.desas.forEach(desa => {
                    let ind = desa.indikators.length > 0 ? desa.indikators[0] : null;
                    let klaster = ind ? ind.klaster_hasil : null;
                    let dotColor = 'bg-slate-300';
                    let label = 'Belum diproses';
                    if(klaster == 1) { dotColor = 'bg-[#10B981]'; label = 'Sejahtera'; }
                    if(klaster == 2) { dotColor = 'bg-[#F59E0B]'; label = 'Berkembang'; }
                    if(klaster == 3) { dotColor = 'bg-[#EF4444]'; label = 'Perlu Perhatian'; }

                    listHTML += `
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg border border-slate-100 hover:bg-slate-100 transition cursor-pointer">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full ${dotColor} shadow-sm"></span>
                                <span class="font-semibold text-[#1E293B]">${desa.nama_desa}</span>
                            </div>
                            <span class="text-xs text-slate-500 font-medium">${label}</span>
                        </div>
                    `;
                });
            } else {
                listHTML = '<p class="text-sm text-red-500">Data desa tidak ditemukan di tahun ini.</p>';
            }
            document.getElementById('panel-desa-list').innerHTML = listHTML;
        }

        // --- FUNGSI BARU: MENAMPILKAN 8 INDIKATOR DESA ---
        function showDetailDesa(namaKecamatan, dataKecamatanDb, kodeKec, namaDesa, dataDesaDb) {
            
            // 1. Tombol KEMBALI
            let backButton = `<button onclick="openPanel('${namaKecamatan}', dbData.find(k => k.nama_kecamatan.toLowerCase() === '${namaKecamatan}'.toLowerCase()), '${kodeKec}')" class="text-sm font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 mb-4">
                &larr; Kembali ke Kec. ${namaKecamatan}
            </button>`;

            // 2. Set Judul Panel
            document.getElementById('panel-title').innerHTML = `<div class="flex flex-col"><span class="text-sm font-normal text-slate-500">Desa/Kelurahan</span>${namaDesa}</div>`;
            
            // 3. Set Kotak Status (Hasil K-Means Desa)
            let statusBox = document.getElementById('panel-status-box');
            let klaster = dataDesaDb && dataDesaDb.indikators.length > 0 ? dataDesaDb.indikators[0].klaster_hasil : null;
            
            if(klaster == 1) {
                statusBox.className = 'inline-block px-4 py-2 rounded-lg text-sm font-bold mb-6 border bg-[#10B981]/10 text-[#10B981] border-[#10B981]/30';
                statusBox.innerText = 'Status: Klaster I (Sejahtera)';
            } else if(klaster == 2) {
                statusBox.className = 'inline-block px-4 py-2 rounded-lg text-sm font-bold mb-6 border bg-[#F59E0B]/10 text-[#F59E0B] border-[#F59E0B]/30';
                statusBox.innerText = 'Status: Klaster II (Berkembang)';
            } else if(klaster == 3) {
                statusBox.className = 'inline-block px-4 py-2 rounded-lg text-sm font-bold mb-6 border bg-[#EF4444]/10 text-[#EF4444] border-[#EF4444]/30';
                statusBox.innerText = 'Status: Klaster III (Perlu Perhatian)';
            } else {
                statusBox.className = 'inline-block px-4 py-2 rounded-lg text-sm font-bold mb-6 border bg-slate-100 text-slate-500 border-slate-200';
                statusBox.innerText = 'Status: Belum diproses K-Means';
            }

            // 4. Render 8 Indikator & Insight
            let contentHTML = backButton;
            
            if(dataDesaDb && dataDesaDb.indikators.length > 0) {
                let ind = dataDesaDb.indikators[0];
                
                // ==========================================
                // 🤖 LOGIKA "DYNAMIC INSIGHT" (AI RULE-BASED)
                // ==========================================
                let penyakitDesa = [];
                
                // Cek indikator yang nilainya "Jelek/Kurang" berdasarkan threshold logika umum
                if(ind.listrik_pln < 50) penyakitDesa.push("minimnya rasio keluarga berlistrik PLN");
                if(ind.fasilitas_pendidikan < 2) penyakitDesa.push("kurangnya fasilitas pendidikan (SD/SMP)");
                if(ind.akses_sma > 10) penyakitDesa.push("jauhnya akses tempuh menuju SMA/SMK");
                if(ind.faskes_desa < 1) penyakitDesa.push("ketiadaan fasilitas kesehatan desa (Poskesdes/Polindes)");
                if(ind.akses_puskesmas > 15) penyakitDesa.push("sulitnya akses menuju Puskesmas Kecamatan");
                if(ind.kualitas_sinyal <= 2) penyakitDesa.push("buruknya kualitas sinyal telekomunikasi");
                if(ind.keamanan_bencana <= 5) penyakitDesa.push("tingginya kerentanan terhadap bencana");

                // Merangkai kalimat AI
                let kalimatInsight = "";
                let namaKlaster = klaster == 1 ? 'Sejahtera' : (klaster == 2 ? 'Berkembang' : 'Perlu Perhatian');
                
                if(klaster == 1) {
                    kalimatInsight = `Desa ini masuk ke dalam <b>Klaster ${namaKlaster}</b>. Berdasarkan analisis sistem, mayoritas indikator kesejahteraan sudah berada di atas rata-rata. Pemda direkomendasikan untuk mempertahankan fasilitas yang ada dan fokus pada pemberdayaan ekonomi lanjutan.`;
                } else {
                    if(penyakitDesa.length > 0) {
                        // Gabungkan array penyakit dengan kata "dan" di akhir
                        let masalahString = penyakitDesa.slice(0, -1).join(', ');
                        if(penyakitDesa.length > 1) masalahString += ' dan ' + penyakitDesa[penyakitDesa.length - 1];
                        else masalahString = penyakitDesa[0];

                        kalimatInsight = `Desa ini ditetapkan sebagai <b>Klaster ${namaKlaster}</b>. Analisis sistem mendeteksi bahwa faktor utama yang menghambat kesejahteraan desa ini adalah <b>${masalahString}</b>. <br><br><b>Rekomendasi:</b> Pemda disarankan memprioritaskan pembangunan/intervensi pada sektor-sektor tersebut untuk tahun anggaran berikutnya.`;
                    } else {
                        kalimatInsight = `Desa ini ditetapkan sebagai <b>Klaster ${namaKlaster}</b>. Meskipun tidak ada indikator yang terdeteksi sangat kritis secara individual, akumulasi dari seluruh 8 indikator masih berada di bawah standar klaster sejahtera berdasarkan perhitungan jarak <i>Euclidean</i>.`;
                    }
                }

                // Render HTML
                contentHTML += `
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 border-b pb-2">Data Indikator (Tahun ${ind.tahun_data})</h3>
                    <div class="space-y-3 mb-8 text-sm">
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Listrik PLN</span> <span class="font-bold ${ind.listrik_pln < 50 ? 'text-red-500' : 'text-[#1E293B]'}">${ind.listrik_pln}%</span></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Fasilitas Ekonomi</span> <span class="font-bold text-[#1E293B]">${ind.fasilitas_ekonomi} Unit</span></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Fasilitas Pendidikan</span> <span class="font-bold ${ind.fasilitas_pendidikan < 2 ? 'text-red-500' : 'text-[#1E293B]'}">${ind.fasilitas_pendidikan} Unit</span></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Jarak Akses SMA</span> <span class="font-bold ${ind.akses_sma > 10 ? 'text-red-500' : 'text-[#1E293B]'}">${ind.akses_sma} Km</span></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Faskes Desa</span> <span class="font-bold ${ind.faskes_desa < 1 ? 'text-red-500' : 'text-[#1E293B]'}">${ind.faskes_desa} Unit</span></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Jarak Puskesmas</span> <span class="font-bold ${ind.akses_puskesmas > 15 ? 'text-red-500' : 'text-[#1E293B]'}">${ind.akses_puskesmas} Km</span></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Kualitas Sinyal</span> <span class="font-bold ${ind.kualitas_sinyal <= 2 ? 'text-red-500' : 'text-[#1E293B]'}">${ind.kualitas_sinyal} Skor</span></div>
                        <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Keamanan Bencana</span> <span class="font-bold ${ind.keamanan_bencana <= 5 ? 'text-red-500' : 'text-[#1E293B]'}">${ind.keamanan_bencana} Skor</span></div>
                    </div>

                    <div class="p-5 bg-blue-50 rounded-xl border border-blue-100 shadow-inner">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xl">🤖</span><h4 class="font-bold text-[#1E3A8A] text-lg">Smart Insight</h4>
                        </div>
                        <p class="text-sm text-blue-900 leading-relaxed text-justify">
                            ${kalimatInsight}
                        </p>
                    </div>
                `;
            } else {
                contentHTML += `<p class="text-sm text-red-500 bg-red-50 p-4 rounded-lg">Data indikator untuk desa ini belum diinput oleh Admin BPS.</p>`;
            }

            document.getElementById('panel-desa-list').innerHTML = contentHTML;
        }

        // 5. FUNGSI MENUTUP PANEL & KEMBALI KE PETA KECAMATAN
        function closePanel() {
            panel.classList.remove('panel-visible');
            panel.classList.add('panel-hidden');

             // Kembalikan judul default
            document.getElementById('panel-title').innerText = "Pilih Kecamatan";
            
            // Hapus Peta Desa
            if(desaLayer && map.hasLayer(desaLayer)) {
                map.removeLayer(desaLayer);
            }
            // Munculkan kembali Peta Kecamatan
            if(kecamatanLayer && !map.hasLayer(kecamatanLayer)) {
                map.addLayer(kecamatanLayer);
                map.fitBounds(kecamatanLayer.getBounds());
            }
        }

        
    </script>
</body>
</html>