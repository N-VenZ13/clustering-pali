<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebGIS Kesejahteraan PALI</title>
    @vite(['resources/css/app.css'])
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        [x-cloak] { display: none !important; }
        .map-label {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            font-weight: 600;
            color: #ffffff;
            text-shadow: 1px 1px 2px black, -1px -1px 2px black, 1px -1px 2px black, -1px 1px 2px black;
            font-size: 11px;
            text-align: center;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased" x-data="{ modalKamus: false, modalTentang: false, openLang: false }">

    <!-- Memanggil Komponen Navbar -->
    @include('components.frontend.navbar')

    <!-- CONTAINER UTAMA (Scrollable) -->
    
    <main class="max-w-[1400px] mx-auto p-4 md:p-6 space-y-6 mt-4">
        
        

        <!-- GRID 3/4 PETA & 1/4 KONTROL -->
        <div class="flex flex-col lg:flex-row gap-6 h-[700px]">
            
            <!-- SISI KIRI (PETA LEAFLET) -->
            <div class="w-full lg:w-[65%] h-full bg-slate-200 rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative z-10">
                
                <!-- Opsi Basemap (Sudah Di-styling) -->
                <div class="absolute top-4 right-4 z-[400] bg-white/90 backdrop-blur p-2 rounded-xl shadow-md border border-slate-200">
                    <select id="basemap-selector" onchange="changeBasemap(this.value)" class="text-xs font-bold text-slate-700 bg-transparent border-none focus:ring-0 cursor-pointer pl-2 pr-6">
                        <option value="carto">Peta Dasar (Carto)</option>
                        <option value="osm">Jalan (OpenStreetMap)</option>
                        <option value="satellite">Satelit (Esri)</option>
                        <option value="none">Polos (Tanpa Peta)</option>
                    </select>
                </div>

                <div id="map" class="w-full h-full z-10"></div>
            </div>

            <!-- SISI KANAN (KONTROL PANEL PETA) -->
            <div class="w-full lg:w-[35%] h-full bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col relative gap-6 overflow-hidden z-20">

                <form action="{{ route('home') }}" method="GET" class="flex items-center gap-3">
                <label class="text-sm font-semibold text-slate-500">Pilih Tahun Data:</label>
                <div class="relative">
                    <select name="tahun" onchange="this.form.submit()" class="appearance-none bg-blue-50 border border-blue-200 text-[#1E3A8A] font-bold text-sm rounded-lg pl-4 pr-10 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer shadow-sm">
                        @foreach($list_tahun as $thn)
                            <option value="{{ $thn }}" {{ $tahun_aktif == $thn ? 'selected' : '' }}>Tahun {{ $thn }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-[#1E3A8A]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </form>
                
                <!-- 1. Level Wilayah -->
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tampilan Wilayah</h3>
                    <div class="flex flex-col gap-2">
                        <button onclick="changeLevel('kecamatan')" id="btn-lvl-kec" class="w-full py-2 text-sm font-bold rounded-md bg-slate-800 text-white transition shadow-sm border border-slate-800">Tingkat Kecamatan</button>
                        <button onclick="changeLevel('desa')" id="btn-lvl-desa" class="w-full py-2 text-sm font-semibold rounded-md bg-white text-slate-600 hover:bg-slate-50 transition border border-slate-200">Tingkat Desa / Kelurahan</button>
                    </div>
                </div>

                <!-- 2. Toggle Label Wilayah -->
                <div class="border-t border-slate-100 pt-5">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" id="toggle-label" class="w-5 h-5 rounded text-[#1E3A8A] focus:ring-[#1E3A8A]">
                        <span class="text-sm font-bold text-slate-700">Tampilkan Nama Wilayah</span>
                    </label>
                </div>

                <!-- 3. Filter Klaster -->
                <div class="border-t border-slate-100 pt-5 flex-1">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Filter Kesejahteraan</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" checked value="1" class="chk-cluster w-5 h-5 rounded border-gray-300 text-[#064e3b] focus:ring-[#064e3b]">
                            <span class="w-4 h-4 rounded bg-[#064e3b] border border-slate-300"></span>
                            <span class="text-sm font-bold text-slate-700">Klaster Sejahtera</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" checked value="2" class="chk-cluster w-5 h-5 rounded border-gray-300 text-[#10b981] focus:ring-[#10b981]">
                            <span class="w-4 h-4 rounded bg-[#10b981] border border-slate-300"></span>
                            <span class="text-sm font-bold text-slate-700">Klaster Berkembang</span>
                        </label>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" checked value="3" class="chk-cluster w-5 h-5 rounded border-gray-300 text-[#a7f3d0] focus:ring-[#a7f3d0]">
                            <span class="w-4 h-4 rounded bg-[#a7f3d0] border border-slate-300"></span>
                            <span class="text-sm font-bold text-slate-700">Perlu Perhatian</span>
                        </label>
                    </div>
                </div>

                <!-- =============================================== -->
                <!-- OVERLAY PANEL DETAIL (MUNCUL SAAT PETA DIKLIK) -->
                <!-- Hanya menimpa area 30% ini saja! -->
                <!-- =============================================== -->
                <div id="panel-detail" class="absolute inset-0 w-full h-full bg-white z-[500] flex flex-col transition-transform duration-300 translate-x-full">
                    
                    <!-- Header Panel -->
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 shrink-0">
                        <button onclick="closeDetailPanel()" class="text-sm font-bold text-[#1E3A8A] flex items-center gap-1 hover:text-blue-800">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            Kembali
                        </button>
                        <button onclick="closeDetailPanel()" class="text-slate-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    
                    <!-- Konten Scrollable -->
                    <div class="p-6 flex-1 overflow-y-auto">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1" id="detail-subtitle">Kecamatan</p>
                        <h2 id="detail-title" class="text-2xl font-bold text-[#1E293B] mb-4">Nama Wilayah</h2>
                        
                        <div id="detail-status" class="inline-block px-4 py-2 rounded-lg text-sm font-bold border bg-slate-100 text-slate-500 mb-6">Status Belum Diketahui</div>
                        
                        <div id="detail-content">
                            <!-- Konten list desa atau 8 indikator -->
                        </div>
                    </div>
                </div>

                
            </div>
        </div>

        

        <!-- AREA TENTANG WEBGIS & LEGENDA (Anchor Target) -->
        <div id="tentang-webgis" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 mb-12 scroll-mt-24">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Kiri: Tentang WebGIS -->
                <div>
                    <h3 class="text-xl font-bold text-[#1E3A8A] mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Tentang WebGIS PALI
                    </h3>
                    <div class="text-sm text-slate-600 space-y-3 text-justify leading-relaxed">
                        <p>Website ini adalah <b>Sistem Informasi Geografis (SIG)</b> resmi yang dikembangkan untuk memetakan tingkat kesejahteraan sosial di Kabupaten Penukal Abab Lematang Ilir (PALI).</p>
                        <p>Sistem ini menggunakan kecerdasan buatan (algoritma <b>Machine Learning K-Means Clustering</b>) untuk memproses 8 indikator infrastruktur dan sosial secara matematis guna mengelompokkan wilayah ke dalam 3 klaster tanpa adanya bias manusia.</p>
                        <p>Penentuan status tingkat Kecamatan dilakukan menggunakan metode <i>Bottom-Up Aggregation</i> berdasarkan nilai mayoritas (Modus) dari desa-desa di dalamnya.</p>
                    </div>
                </div>

                <!-- Kanan: Legenda Peta -->
                <div>
                    <h3 class="text-xl font-bold text-[#1E3A8A] mb-4 border-b pb-2 flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                        Legenda Peta (Choropleth)
                    </h3>
                    <p class="text-sm text-slate-600 mb-4">Peta disajikan menggunakan skema warna monokromatik hijau. Semakin pekat warna hijau, semakin tinggi tingkat kesejahteraan wilayah tersebut.</p>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-6 bg-[#14532d] rounded border border-green-900 shadow-sm flex-shrink-0"></div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Klaster I (Sejahtera)</h4>
                                <p class="text-xs text-slate-500">Nilai indikator fasilitas berada di atas rata-rata.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-6 bg-[#22c55e] rounded border border-green-600 shadow-sm flex-shrink-0"></div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Klaster II (Berkembang)</h4>
                                <p class="text-xs text-slate-500">Infrastruktur memadai namun butuh peningkatan.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-6 bg-[#bbf7d0] rounded border border-green-300 shadow-sm flex-shrink-0"></div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Klaster III (Perlu Perhatian)</h4>
                                <p class="text-xs text-slate-500">Banyak fasilitas desa berada di bawah standar.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-6 bg-[#cbd5e1] rounded border border-slate-300 shadow-sm flex-shrink-0"></div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Belum Diproses</h4>
                                <p class="text-xs text-slate-500">Data desa kosong atau belum dijalankan algoritma.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>

    </main>

    <!-- Memanggil Komponen Footer -->
    @include('components.frontend.footer')

    <!-- Sembunyikan Data PHP -->
    <script id="data-kecamatan" type="application/json">
        {!! json_encode($kecamatans) !!}
    </script>

    <!-- LEAFLET JS SCRIPT (Nanti Kita Bangun Ulang Di Bawah Ini) -->

    <!-- Sembunyikan Data PHP -->
    <script id="data-kecamatan" type="application/json">
        {!! json_encode($kecamatans) !!}
    </script>

    <!-- LEAFLET JS & LOGIKA PETA -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const dbData = JSON.parse(document.getElementById('data-kecamatan').textContent);

        // ==========================================
        // 1. STATE MANAGEMENT (Menyimpan Pilihan User)
        // ==========================================
        let activeLevel = 'kecamatan'; // Mode default: Kecamatan
        let activeClusters = [1, 2, 3]; // Klaster 1,2,3 tercentang default
        let showLabel = false; // Label nama disembunyikan default
        let currentBasemap = 'carto'; // Tema Peta Dasar default

        // Kamus Data Desa (Agar cepat mencari data saat level desa aktif)
        const desaDbMap = {};
        dbData.forEach(kec => {
            if(kec.desas) {
                kec.desas.forEach(desa => {
                    desaDbMap[desa.nama_desa.toLowerCase()] = desa;
                });
            }
        });

        // ==========================================
        // 2. SETUP PETA LEAFLET
        // ==========================================
        const map = L.map('map', { zoomControl: false }).setView([-3.25, 104.0], 10);
        L.control.zoom({ position: 'bottomright' }).addTo(map);

        // Definisikan Peta Dasar (Basemaps)
        const basemaps = {
            carto: L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { attribution: '© OpenStreetMap, © CartoDB' }),
            osm: L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OpenStreetMap' }),
            satellite: L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', { maxZoom: 20, subdomains: ['mt0', 'mt1', 'mt2', 'mt3'], attribution: '© Google' }),
            none: L.tileLayer('') // Kosong / Putih
        };

        // Pasang basemap default
        basemaps[currentBasemap].addTo(map);

        // Fungsi Ganti Basemap dari Dropdown
        function changeBasemap(type) {
            map.removeLayer(basemaps[currentBasemap]);
            basemaps[type].addTo(map);
            currentBasemap = type;
        }

        // ==========================================
        // 3. LOGIKA WARNA (THE GREEN PALETTE BPS)
        // ==========================================
        function getColorByKlaster(klaster) {
            if(klaster == 1) return '#064e3b'; // Hijau Tua (Sejahtera)
            if(klaster == 2) return '#10b981'; // Hijau Sedang (Berkembang)
            if(klaster == 3) return '#a7f3d0'; // Hijau Pucat (Perlu Perhatian)
            return '#cbd5e1'; // Abu-abu (Belum diproses)
        }

        // Variabel penampung geojson
        let rawKecGeo = null;
        let rawDesaGeo = null;
        let currentMapLayer = null;

        // Muat File GeoJSON (Jalankan renderMap setelah file ter-load)
        Promise.all([
            fetch('/geojson/kecamatan.json').then(r => r.json()),
            fetch('/geojson/desa.json').then(r => r.json())
        ]).then(([kecData, desaData]) => {
            rawKecGeo = kecData;
            rawDesaGeo = desaData;
            renderMap(); // Panggil fungsi utama untuk menggambar
        }).catch(err => console.error("Gagal memuat GeoJSON:", err));

        // ==========================================
        // 4. FUNGSI UTAMA: MENGGAMBAR ULANG PETA
        // ==========================================
        // 4. FUNGSI UTAMA: MENGGAMBAR ULANG PETA
        // ==========================================
        function renderMap() {
            if(currentMapLayer) { map.removeLayer(currentMapLayer); }

            let geoData = activeLevel === 'kecamatan' ? rawKecGeo : rawDesaGeo;

            currentMapLayer = L.geoJSON(geoData, {
                style: function(feature) {
                    let klaster = null;
                    let isFilteredOut = false;

                    if (activeLevel === 'kecamatan') {
                        let nama = feature.properties.nm_kecamatan;
                        let match = dbData.find(k => k.nama_kecamatan.toLowerCase() === nama.toLowerCase());
                        if(match && match.status_akhir) {
                            if(match.status_akhir == 'Sejahtera') klaster = 1;
                            if(match.status_akhir == 'Berkembang') klaster = 2;
                            if(match.status_akhir == 'Perlu Perhatian') klaster = 3;
                        }
                    } else {
                        let nama = feature.properties.nm_kelurahan;
                        let match = desaDbMap[nama.toLowerCase()];
                        if(match && match.indikators && match.indikators.length > 0) {
                            klaster = match.indikators[0].klaster_hasil;
                        }
                    }

                    if (klaster && !activeClusters.includes(klaster)) isFilteredOut = true;

                    let finalColor = klaster ? getColorByKlaster(klaster) : '#cbd5e1';
                    
                    if (isFilteredOut) return { fillColor: '#e2e8f0', weight: 1, opacity: 0.3, color: '#94a3b8', fillOpacity: 0.1 };
                    return { fillColor: finalColor, weight: 1.5, opacity: 1, color: 'white', fillOpacity: 0.85 };
                },
                
                onEachFeature: function(feature, layer) {
                    let namaWilayah = activeLevel === 'kecamatan' ? feature.properties.nm_kecamatan : feature.properties.nm_kelurahan;
                    
                    if (!showLabel) layer.bindTooltip("<b>" + namaWilayah + "</b>", {sticky: true});

                    if(showLabel) {
                        layer.bindTooltip(namaWilayah.toUpperCase(), { permanent: true, direction: 'center', className: 'map-label' }).openTooltip();
                    } else {
                        layer.unbindTooltip();
                        layer.bindTooltip("<b>" + namaWilayah + "</b>", {sticky: true});
                    }

                    layer.on('mouseover', function() { if(this.options.fillOpacity > 0.5) this.setStyle({ fillOpacity: 1, weight: 3 }); });
                    layer.on('mouseout', function() { currentMapLayer.resetStyle(this); });

                    // --- MENGEMBALIKAN FITUR KLIK POLIGON ---
                    layer.on('click', function(e) {
                        L.DomEvent.stopPropagation(e);
                        map.fitBounds(layer.getBounds());
                        
                        if (activeLevel === 'kecamatan') {
                            let matchDb = dbData.find(k => k.nama_kecamatan.toLowerCase() === namaWilayah.toLowerCase());
                            openDetailPanel('Kecamatan', namaWilayah, matchDb);
                        } else {
                            let matchDb = desaDbMap[namaWilayah.toLowerCase()];
                            openDetailPanel('Desa/Kelurahan', namaWilayah, matchDb);
                        }
                    });
                }
            }).addTo(map);

            if(activeLevel === 'kecamatan') map.fitBounds(currentMapLayer.getBounds());
        }

        // ==========================================
        // 5. LOGIKA PANEL DETAIL (MUNCUL SAAT KLIK)
        // ==========================================
        const panelDetail = document.getElementById('panel-detail');

        function openDetailPanel(tipe, nama, dbInfo) {
            document.getElementById('detail-subtitle').innerText = tipe;
            document.getElementById('detail-title').innerText = nama;
            
            let statusBox = document.getElementById('detail-status');
            let contentHTML = '';

            // JIKA KECAMATAN DIKLIK
            if (tipe === 'Kecamatan') {
                if(dbInfo && dbInfo.status_akhir) {
                    let colorClass = dbInfo.status_akhir == 'Sejahtera' ? 'bg-[#14532d]/10 text-[#14532d] border-[#14532d]/30' : (dbInfo.status_akhir == 'Berkembang' ? 'bg-[#22c55e]/10 text-[#22c55e] border-[#22c55e]/30' : 'bg-[#bbf7d0] text-green-800 border-green-400');
                    statusBox.className = `inline-block px-4 py-2 rounded-lg text-sm font-bold border mb-6 ${colorClass}`;
                    statusBox.innerHTML = `Status Agregasi: ${dbInfo.status_akhir} <br><span class="text-xs font-normal opacity-80">Skor Mayoritas: ${dbInfo.skor_agregasi}</span>`;
                } else {
                    statusBox.className = "inline-block px-4 py-2 rounded-lg text-sm font-bold border bg-slate-100 text-slate-500 mb-6";
                    statusBox.innerText = "Belum ada data K-Means";
                }

                contentHTML += `<h3 class="text-sm font-bold text-slate-400 uppercase mb-3 border-b pb-2">Komposisi Desa</h3><div class="space-y-2">`;
                
                if(dbInfo && dbInfo.desas && dbInfo.desas.length > 0) {
                    dbInfo.desas.forEach(desa => {
                        let klaster = desa.indikators.length > 0 ? desa.indikators[0].klaster_hasil : null;
                        let dotColor = 'bg-slate-300'; let label = 'Belum diproses';
                        if(klaster == 1) { dotColor = 'bg-[#14532d]'; label = 'Sejahtera'; }
                        if(klaster == 2) { dotColor = 'bg-[#22c55e]'; label = 'Berkembang'; }
                        if(klaster == 3) { dotColor = 'bg-[#bbf7d0] border border-green-300'; label = 'Perlu Perhatian'; }

                        contentHTML += `
                            <div class="flex items-center justify-between p-2 bg-slate-50 rounded border border-slate-100">
                                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full ${dotColor}"></span><span class="text-sm font-bold text-[#1E293B]">${desa.nama_desa}</span></div>
                                <span class="text-xs font-semibold text-slate-500">${label}</span>
                            </div>`;
                    });
                } else {
                    contentHTML += `<p class="text-sm text-red-500">Kosong</p>`;
                }
                contentHTML += `</div>`;
            } 
            // JIKA DESA DIKLIK
            else {
                let klaster = dbInfo && dbInfo.indikators.length > 0 ? dbInfo.indikators[0].klaster_hasil : null;
                
                if(klaster) {
                    let colorClass = klaster == 1 ? 'bg-[#14532d]/10 text-[#14532d] border-[#14532d]/30' : (klaster == 2 ? 'bg-[#22c55e]/10 text-[#22c55e] border-[#22c55e]/30' : 'bg-[#bbf7d0] text-green-800 border-green-400');
                    let label = klaster == 1 ? 'Sejahtera' : (klaster == 2 ? 'Berkembang' : 'Perlu Perhatian');
                    statusBox.className = `inline-block px-4 py-2 rounded-lg text-sm font-bold border mb-6 ${colorClass}`;
                    statusBox.innerText = `Status Desa: ${label}`;
                } else {
                    statusBox.className = "inline-block px-4 py-2 rounded-lg text-sm font-bold border bg-slate-100 text-slate-500 mb-6";
                    statusBox.innerText = "Belum diproses K-Means";
                }

                if(dbInfo && dbInfo.indikators.length > 0) {
                    let ind = dbInfo.indikators[0];
                    
                    // =====================================
                    // LOGIKA INSIGHT DINAMIS (FRIENDLY AI)
                    // =====================================
                    let perhatianKhusus = [];
                    if(ind.listrik_pln < 500) perhatianKhusus.push("pemerataan jaringan listrik PLN");
                    if(ind.fasilitas_ekonomi < 2) perhatianKhusus.push("pembangunan fasilitas ekonomi/pasar");
                    if(ind.fasilitas_pendidikan < 2) perhatianKhusus.push("penambahan fasilitas pendidikan");
                    if(ind.akses_sma > 10) perhatianKhusus.push("akses jalan menuju SMA yang cukup jauh");
                    if(ind.faskes_desa < 1) perhatianKhusus.push("pengadaan Poskesdes/Polindes");
                    if(ind.akses_puskesmas > 15) perhatianKhusus.push("akses jalan menuju Puskesmas yang jauh");
                    if(ind.kualitas_sinyal <= 2) perhatianKhusus.push("pembangunan menara telekomunikasi (sinyal blankspot)");
                    
                    let narasiInsight = "";
                    if(klaster == 1) {
                        narasiInsight = "Desa ini memiliki profil kesejahteraan yang <b>sangat baik</b> secara keseluruhan. Hampir seluruh fasilitas dasar sudah terpenuhi. Langkah selanjutnya yang bisa dilakukan oleh pemerintah adalah fokus pada program pemberdayaan ekonomi lanjutan.";
                    } else if (klaster == 2) {
                        narasiInsight = "Desa ini tergolong <b>cukup berkembang</b> dengan infrastruktur dasar yang memadai. Namun, masih ada beberapa ruang untuk peningkatan agar bisa setara dengan desa-desa di klaster sejahtera.";
                        if(perhatianKhusus.length > 0) {
                            narasiInsight += ` Beberapa hal yang bisa menjadi fokus perhatian Pemda di antaranya adalah ${perhatianKhusus.slice(0, 2).join(' dan ')}.`;
                        }
                    } else if (klaster == 3) {
                        narasiInsight = "Desa ini membutuhkan <b>intervensi khusus</b> dari pemerintah daerah karena nilai indikatornya berada di bawah rata-rata. ";
                        if(perhatianKhusus.length > 0) {
                            narasiInsight += `Rekomendasi utama yang perlu diprioritaskan untuk desa ini meliputi ${perhatianKhusus.join(', ')}.`;
                        } else {
                            narasiInsight += "Disarankan untuk melakukan peninjauan lapangan terkait perbaikan infrastruktur secara menyeluruh.";
                        }
                    }

                    contentHTML += `
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-4 border-b pb-2">8 Indikator Kesejahteraan</h3>
                        <div class="space-y-3 mb-8 text-sm">
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Listrik PLN</span> <span class="font-bold text-[#1E293B]">${ind.listrik_pln} KK</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Fasilitas Ekonomi</span> <span class="font-bold text-[#1E293B]">${ind.fasilitas_ekonomi} Unit</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Fasilitas Pendidikan</span> <span class="font-bold text-[#1E293B]">${ind.fasilitas_pendidikan} Unit</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Jarak Akses SMA</span> <span class="font-bold text-[#1E293B]">${ind.akses_sma} Km</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Faskes Desa</span> <span class="font-bold text-[#1E293B]">${ind.faskes_desa} Unit</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Jarak Puskesmas</span> <span class="font-bold text-[#1E293B]">${ind.akses_puskesmas} Km</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Kualitas Sinyal</span> <span class="font-bold text-[#1E293B]">${ind.kualitas_sinyal} Skor</span></div>
                            <div class="flex justify-between border-b border-slate-100 pb-2"><span class="text-slate-600">Keamanan Bencana</span> <span class="font-bold text-[#1E293B]">${ind.keamanan_bencana} Skor</span></div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-[#1E3A8A] mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Kesimpulan Analisis
                            </h4>
                            <p class="text-sm text-slate-700 leading-relaxed text-justify">${narasiInsight}</p>
                        </div>
                    `;
                }
            }

            document.getElementById('detail-content').innerHTML = contentHTML;
            panelDetail.classList.remove('translate-x-full'); // Geser panel masuk ke layar
        }

        function closeDetailPanel() {
            panelDetail.classList.add('translate-x-full'); // Geser panel keluar layar
            map.fitBounds(currentMapLayer.getBounds()); // Zoom out ke peta
        }

        // ==========================================
        // 6. EVENT LISTENER DARI PANEL KANAN (UI)
        // ==========================================
        // (Sama seperti sebelumnya)
        function changeLevel(level) {
            activeLevel = level;
            closeDetailPanel(); // Tutup panel detail jika level berubah
            
            let btnKec = document.getElementById('btn-lvl-kec');
            let btnDesa = document.getElementById('btn-lvl-desa');
            
            if(level === 'kecamatan') {
                btnKec.className = "w-full py-2 text-sm font-bold rounded-md bg-slate-800 text-white transition shadow-sm border border-slate-800";
                btnDesa.className = "w-full py-2 text-sm font-semibold rounded-md bg-white text-slate-600 hover:bg-slate-50 transition border border-slate-200";
            } else {
                btnDesa.className = "w-full py-2 text-sm font-bold rounded-md bg-slate-800 text-white transition shadow-sm border border-slate-800";
                btnKec.className = "w-full py-2 text-sm font-semibold rounded-md bg-white text-slate-600 hover:bg-slate-50 transition border border-slate-200";
            }
            renderMap();
        }

        document.querySelectorAll('.chk-cluster').forEach(chk => {
            chk.addEventListener('change', function() {
                let klasterId = parseInt(this.value);
                if(this.checked) {
                    if(!activeClusters.includes(klasterId)) activeClusters.push(klasterId);
                } else {
                    activeClusters = activeClusters.filter(id => id !== klasterId);
                }
                renderMap();
            });
        });

        const toggleInput = document.getElementById('toggle-label');
        if(toggleInput) {
            toggleInput.addEventListener('change', function() {
                showLabel = this.checked;
                renderMap();
            });
        }
    </script>
    

</body>
</html>