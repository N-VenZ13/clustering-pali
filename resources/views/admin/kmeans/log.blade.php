@extends('layouts.admin')
@section('title', 'LOG PERHITUNGAN K-MEANS')
@section('content')
    <div class="mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-100">
        <div>
            <h2 class="text-xl font-bold text-[#1E293B]">Detail Kalkulasi Matematika K-Means</h2>
            <p class="text-slate-500 text-sm">Tahun Data: {{ $tahun }} | Total Iterasi: {{ $logData['total_iterations'] }} kali hingga konvergen.</p>
        </div>
        <a href="{{ route('kmeans.index', ['tahun' => $tahun]) }}" class="bg-[#1E3A8A] hover:bg-blue-800 text-white px-6 py-2 rounded-lg font-semibold transition shadow-sm">Tutup Log</a>
    </div>

    <!-- TAHAP 1: PRE-PROCESSING -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
        <h3 class="font-bold text-[#1E3A8A] text-lg border-b pb-2 mb-4">TAHAP 1: Pre-Processing (Logika Invers)</h3>
        <p class="text-slate-700 text-sm mb-4 leading-relaxed text-justify">
            Algoritma K-Means mengasumsikan bahwa <b>semua variabel searah</b> (Makin besar angka = Makin baik). Namun, pada dataset ini terdapat variabel yang bersifat <i>Cost</i> (Makin besar = Makin buruk), yaitu variabel Jarak (Km) dan Skor Sinyal (1=Bagus, 4=Jelek). Oleh karena itu, dilakukan proses Invers:
        </p>
        <ul class="list-disc ml-6 text-sm text-slate-600 mb-4 space-y-1">
            <li><b>Akses SMA:</b> Skor = Nilai Maksimum ({{ $logData['min_max_raw']['akses_sma']['max'] }}) - Jarak Desa</li>
            <li><b>Akses Puskesmas:</b> Skor = Nilai Maksimum ({{ $logData['min_max_raw']['akses_puskesmas']['max'] }}) - Jarak Desa</li>
            <li><b>Kualitas Sinyal:</b> Skor = 5 - Sinyal Desa</li>
        </ul>
    </div>

    <!-- TAHAP 2: NORMALISASI -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
        <h3 class="font-bold text-[#1E3A8A] text-lg border-b pb-2 mb-4">TAHAP 2: Normalisasi Min-Max Scaler</h3>
        <p class="text-slate-700 text-sm mb-4">Menyamakan skala seluruh data ke dalam rentang <b>0 hingga 1</b> agar tidak ada variabel yang mendominasi variabel lain (karena satuan Listrik PLN ribuan, sedangkan fasilitas ekonomi hanya satuan).</p>
        
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 mb-6">
            <h4 class="font-bold text-blue-900 mb-2">Rumus Min-Max:</h4>
            <code class="block bg-white p-3 rounded border text-blue-800 text-sm">Nilai Baru = (Nilai Asli - Nilai Minimum) / (Nilai Maksimum - Nilai Minimum)</code>
            
            <h4 class="font-bold text-blue-900 mt-4 mb-2">Contoh Perhitungan ({{ $logData['raw_data'][0]->desa->nama_desa }} - Variabel Listrik PLN):</h4>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>Nilai Asli = {{ $logData['raw_data'][0]->listrik_pln }}</li>
                <li>Nilai Min = {{ $logData['min_max_processed']['listrik_pln']['min'] }} | Nilai Max = {{ $logData['min_max_processed']['listrik_pln']['max'] }}</li>
                <li><b>Hasil</b> = ({{ $logData['raw_data'][0]->listrik_pln }} - {{ $logData['min_max_processed']['listrik_pln']['min'] }}) / ({{ $logData['min_max_processed']['listrik_pln']['max'] }} - {{ $logData['min_max_processed']['listrik_pln']['min'] }}) = <b>{{ number_format($logData['normalized'][0]['listrik_pln'], 4) }}</b></li>
            </ul>
        </div>

        <div class="overflow-x-auto mt-4">
            <table class="w-full text-xs text-left border-collapse border border-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="border border-slate-200 p-2">Desa</th>
                        <th class="border border-slate-200 p-2">Listrik</th>
                        <th class="border border-slate-200 p-2">Fas. Eko</th>
                        <th class="border border-slate-200 p-2">Fas. Pend</th>
                        <th class="border border-slate-200 p-2">Akses SMA</th>
                        <th class="border border-slate-200 p-2">Faskes</th>
                        <th class="border border-slate-200 p-2">Jarak Pusk</th>
                        <th class="border border-slate-200 p-2">Sinyal</th>
                        <th class="border border-slate-200 p-2">Bencana</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logData['normalized'] as $index => $norm)
                    <tr class="hover:bg-slate-50">
                        <td class="border border-slate-200 p-2 font-bold">{{ $logData['raw_data'][$index]->desa->nama_desa }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['listrik_pln'], 4) }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['fasilitas_ekonomi'], 4) }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['fasilitas_pendidikan'], 4) }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['skor_akses_sma'], 4) }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['faskes_desa'], 4) }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['skor_akses_puskesmas'], 4) }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['skor_kualitas_sinyal'], 4) }}</td>
                        <td class="border border-slate-200 p-2">{{ number_format($norm['keamanan_bencana'], 4) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAHAP 3: INISIALISASI CENTROID -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-6">
        <h3 class="font-bold text-[#1E3A8A] text-lg border-b pb-2 mb-4">TAHAP 3: Inisialisasi Titik Pusat (Centroid Awal)</h3>
        <p class="text-slate-700 text-sm mb-4">Untuk menghindari hasil yang berubah-ubah (Inkonsisten), sistem menggunakan metode <i>Deterministic Initialization</i>. Sistem mencari nilai rata-rata tiap baris data, lalu mengambil data dengan Ranking 1 (Tertinggi), Median (Tengah), dan Terendah sebagai Centroid awal.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Centroid 1 -->
            @php $c1 = $logData['initial_centroids'][0]; @endphp
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <p class="font-bold text-[#1E3A8A] mb-1">Centroid 1 (Tertinggi)</p>
                <p class="text-xs text-slate-500 mb-2">Diambil dari: <b class="text-green-800">{{ $c1['_nama_desa'] }}</b></p>
                <div class="text-[11px] bg-white p-2 rounded border border-slate-100 font-mono overflow-x-auto whitespace-nowrap">
                    [ {{ number_format($c1['listrik_pln'] ?? 0, 2) }}, {{ number_format($c1['fasilitas_ekonomi'] ?? 0, 2) }}, {{ number_format($c1['fasilitas_pendidikan'] ?? 0, 2) }}, {{ number_format($c1['skor_akses_sma'] ?? 0, 2) }}, {{ number_format($c1['faskes_desa'] ?? 0, 2) }}, {{ number_format($c1['skor_akses_puskesmas'] ?? 0, 2) }}, {{ number_format($c1['skor_kualitas_sinyal'] ?? 0, 2) }}, {{ number_format($c1['keamanan_bencana'] ?? 0, 2) }} ]
                </div>
            </div>

            <!-- Centroid 2 -->
            @php $c2 = $logData['initial_centroids'][1]; @endphp
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <p class="font-bold text-[#1E3A8A] mb-1">Centroid 2 (Median)</p>
                <p class="text-xs text-slate-500 mb-2">Diambil dari: <b class="text-green-600">{{ $c2['_nama_desa'] }}</b></p>
                <div class="text-[11px] bg-white p-2 rounded border border-slate-100 font-mono overflow-x-auto whitespace-nowrap">
                    [ {{ number_format($c2['listrik_pln'] ?? 0, 2) }}, {{ number_format($c2['fasilitas_ekonomi'] ?? 0, 2) }}, {{ number_format($c2['fasilitas_pendidikan'] ?? 0, 2) }}, {{ number_format($c2['skor_akses_sma'] ?? 0, 2) }}, {{ number_format($c2['faskes_desa'] ?? 0, 2) }}, {{ number_format($c2['skor_akses_puskesmas'] ?? 0, 2) }}, {{ number_format($c2['skor_kualitas_sinyal'] ?? 0, 2) }}, {{ number_format($c2['keamanan_bencana'] ?? 0, 2) }} ]
                </div>
            </div>

            <!-- Centroid 3 -->
            @php $c3 = $logData['initial_centroids'][2]; @endphp
            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                <p class="font-bold text-[#1E3A8A] mb-1">Centroid 3 (Terendah)</p>
                <p class="text-xs text-slate-500 mb-2">Diambil dari: <b class="text-green-300">{{ $c3['_nama_desa'] }}</b></p>
                <div class="text-[11px] bg-white p-2 rounded border border-slate-100 font-mono overflow-x-auto whitespace-nowrap">
                    [ {{ number_format($c3['listrik_pln'] ?? 0, 2) }}, {{ number_format($c3['fasilitas_ekonomi'] ?? 0, 2) }}, {{ number_format($c3['fasilitas_pendidikan'] ?? 0, 2) }}, {{ number_format($c3['skor_akses_sma'] ?? 0, 2) }}, {{ number_format($c3['faskes_desa'] ?? 0, 2) }}, {{ number_format($c3['skor_akses_puskesmas'] ?? 0, 2) }}, {{ number_format($c3['skor_kualitas_sinyal'] ?? 0, 2) }}, {{ number_format($c3['keamanan_bencana'] ?? 0, 2) }} ]
                </div>
            </div>
        </div>
    </div>

    <!-- TAHAP 4: EUCLIDEAN DISTANCE & ITERASI -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-[#1E3A8A] text-lg border-b pb-2 mb-4">TAHAP 4: Perhitungan Jarak Euclidean & Iterasi</h3>
        <p class="text-slate-700 text-sm mb-4">Menghitung jarak terpendek dari setiap 8 nilai indikator desa menuju nilai Centroid. Data desa akan masuk ke klaster dengan jarak Euclidean terkecil. Setelah klaster terbentuk, titik Centroid akan bergeser ke tengah kelompok baru, dan perhitungan diulang (Iterasi) sampai posisi Centroid tidak berubah lagi (Konvergen).</p>
        
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-100 mb-6">
            <h4 class="font-bold text-orange-900 mb-2">Rumus Jarak Euclidean (Contoh Desa {{ $logData['raw_data'][0]->desa->nama_desa }} ke Centroid 1):</h4>
            <code class="block bg-white p-3 rounded border text-orange-800 text-xs overflow-x-auto mb-2">D = √ ( (Listrik_Desa - Listrik_C1)² + (Eko_Desa - Eko_C1)² + ... + (Bencana_Desa - Bencana_C1)² )</code>
            
            @php 
                $desaSatu = $logData['normalized'][0];
                $c1 = $logData['all_iterations'][0]['centroids_used'][0];
            @endphp
            <code class="block bg-white p-3 rounded border text-orange-800 text-xs overflow-x-auto">
                D = √ ( 
                ({{ number_format($desaSatu['listrik_pln'], 3) }} - {{ number_format($c1['listrik_pln'], 3) }})² + 
                ({{ number_format($desaSatu['fasilitas_ekonomi'], 3) }} - {{ number_format($c1['fasilitas_ekonomi'], 3) }})² + ... 
                ) = <b>{{ number_format($logData['all_iterations'][0]['distances'][0][0], 4) }}</b>
            </code>
        </div>

        <!-- RIWAYAT ITERASI (Accordion / List) -->
        <div class="space-y-4">
            @foreach($logData['all_iterations'] as $iterIndex => $iter)
                <div class="border border-slate-200 rounded-lg overflow-hidden">
                    <!-- Header Iterasi -->
                    <div class="bg-slate-100 p-3 font-bold text-[#1E293B] border-b border-slate-200 flex justify-between items-center">
                        <span>Iterasi Ke-{{ $iter['iteration_number'] }} {{ $iterIndex == count($logData['all_iterations'])-1 ? ' (KONVERGEN - SELESAI)' : '' }}</span>
                    </div>
                    
                    <!-- Tabel Iterasi -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-center border-collapse">
                            <thead class="bg-white text-slate-500">
                                <tr>
                                    <th class="border-b border-slate-200 p-2 text-left">Nama Desa</th>
                                    <th class="border-b border-slate-200 p-2">Jarak ke C1</th>
                                    <th class="border-b border-slate-200 p-2">Jarak ke C2</th>
                                    <th class="border-b border-slate-200 p-2">Jarak ke C3</th>
                                    <th class="border-b border-slate-200 p-2 bg-blue-50">Masuk Klaster</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($iter['distances'] as $index => $dist)
                                @php 
                                    $minVal = min($dist);
                                    $cIndex = array_search($minVal, $dist);
                                    $klasterReal = $logData['cluster_labels'][$cIndex];
                                @endphp
                                <tr class="hover:bg-slate-50 border-b border-slate-100 last:border-0">
                                    <td class="p-2 text-left font-bold text-slate-700">{{ $logData['raw_data'][$index]->desa->nama_desa }}</td>
                                    <td class="p-2 {{ $dist[0] == $minVal ? 'font-bold bg-slate-100' : 'text-slate-400' }}">{{ number_format($dist[0], 4) }}</td>
                                    <td class="p-2 {{ $dist[1] == $minVal ? 'font-bold bg-slate-100' : 'text-slate-400' }}">{{ number_format($dist[1], 4) }}</td>
                                    <td class="p-2 {{ $dist[2] == $minVal ? 'font-bold bg-slate-100' : 'text-slate-400' }}">{{ number_format($dist[2], 4) }}</td>
                                    <td class="p-2 font-bold bg-blue-50 text-[#1E3A8A]">{{ $klasterReal }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection