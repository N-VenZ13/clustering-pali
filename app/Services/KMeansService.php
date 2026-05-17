<?php

namespace App\Services;

use App\Models\Indikator;

class KMeansService
{
    // Kolom asli dari database
    protected $rawColumns = [
        'listrik_pln', 'fasilitas_ekonomi', 'fasilitas_pendidikan',
        'akses_sma', 'faskes_desa', 'akses_puskesmas',
        'kualitas_sinyal', 'keamanan_bencana'
    ];

    // Kolom setelah di-invers (Siap untuk Min-Max)
    protected $processColumns = [
        'listrik_pln', 'fasilitas_ekonomi', 'fasilitas_pendidikan',
        'skor_akses_sma', 'faskes_desa', 'skor_akses_puskesmas',
        'skor_kualitas_sinyal', 'keamanan_bencana'
    ];

    public function process($tahun)
    {
        $data = Indikator::where('tahun_data', $tahun)->get();

        if ($data->count() < 3) {
            return false; 
        }

        // ==========================================
        // 1. PRE-PROCESSING (LOGIKA INVERS)
        // ==========================================
        $maxSma = $data->max('akses_sma');
        $maxPuskesmas = $data->max('akses_puskesmas');

        foreach ($data as $row) {
            // Jarak dibalik: Jarak Terjauh - Jarak Desa Ini (Makin dekat = Skor makin tinggi)
            $row->skor_akses_sma = $maxSma - $row->akses_sma;
            $row->skor_akses_puskesmas = $maxPuskesmas - $row->akses_puskesmas;
            
            // Sinyal dibalik: 5 - Sinyal (Misal sinyal 1(bagus) -> 5-1 = 4 (Skor Tinggi))
            $row->skor_kualitas_sinyal = 5 - $row->kualitas_sinyal;
        }

        // ==========================================
        // 2. NORMALISASI MIN-MAX (Berdasarkan $processColumns)
        // ==========================================
        $normalizedData = $this->normalize($data);

        // 3. INISIALISASI CENTROID DETERMINISTIK (ANTI-ACAK)
        // ==========================================
        // Agar hasil 100% konsisten tiap kali dijalankan, kita tidak memakai array_rand().
        // Kita urutkan data dari yang terbaik ke terburuk, lalu ambil Ujung Atas, Tengah, dan Ujung Bawah.
        
        $sortedForInit = $normalizedData;
        usort($sortedForInit, function($a, $b) {
            $rataA = array_sum($a) / count($a);
            $rataB = array_sum($b) / count($b);
            return $rataB <=> $rataA; // Descending
        });

        $countData = count($sortedForInit);
        
        $centroids = [
            $sortedForInit[0],                                 // Centroid 1: Desa dengan nilai paling tinggi
            $sortedForInit[(int) floor($countData / 2)],       // Centroid 2: Desa dengan nilai tepat di tengah (Median)
            $sortedForInit[$countData - 1]                     // Centroid 3: Desa dengan nilai paling rendah
        ];

        $clusters = [];
        $isConverged = false;
        $maxIterations = 100;
        $iteration = 0;

        // 4. ITERASI K-MEANS
        while (!$isConverged && $iteration < $maxIterations) {
            $newClusters = [[], [], []];

            foreach ($normalizedData as $index => $item) {
                $distances = [];
                foreach ($centroids as $cIndex => $centroid) {
                    $distances[$cIndex] = $this->euclideanDistance($item, $centroid);
                }
                $closestCentroidIndex = array_search(min($distances), $distances);
                $newClusters[$closestCentroidIndex][] = $index;
            }

            $newCentroids = [];
            foreach ($newClusters as $cIndex => $clusterMembers) {
                if (count($clusterMembers) > 0) {
                    $newCentroids[$cIndex] = $this->calculateMean($normalizedData, $clusterMembers);
                } else {
                    $newCentroids[$cIndex] = $centroids[$cIndex];
                }
            }

            if ($centroids === $newCentroids) {
                $isConverged = true;
            }

            $centroids = $newCentroids;
            $clusters = $newClusters;
            $iteration++;
        }

        // ==========================================
        // 5. MEMBERI NAMA KLASTER (Makin Tinggi Rata2 = Sejahtera)
        // ==========================================
        $centroidScores = [];
        foreach ($centroids as $cIndex => $centroid) {
            $centroidScores[$cIndex] = array_sum($centroid) / count($this->processColumns);
        }

        arsort($centroidScores); // Urutkan dari nilai tertinggi ke terendah
        
        $clusterLabels = [];
        $rank = 1; // 1: Sejahtera, 2: Berkembang, 3: Perhatian
        foreach ($centroidScores as $cIndex => $score) {
            $clusterLabels[$cIndex] = $rank;
            $rank++;
        }

        // 6. Simpan Hasil ke Database
        foreach ($clusters as $cIndex => $members) {
            $label = $clusterLabels[$cIndex];
            foreach ($members as $dataIndex) {
                $id_indikator = $data[$dataIndex]->id;
                Indikator::where('id', $id_indikator)->update(['klaster_hasil' => $label]);
            }
        }

        return true;
    }

    // --- FUNGSI MATEMATIKA ---
    private function normalize($data)
    {
        $minMax = [];
        foreach ($this->processColumns as $col) {
            $minMax[$col] = [
                'min' => $data->min(fn($item) => $item->{$col}), 
                'max' => $data->max(fn($item) => $item->{$col})
            ];
        }

        $normalized = [];
        foreach ($data as $row) {
            $normRow = [];
            
            // --- TAMBAHAN KITA: Bawa nama desanya ---
            $normRow['_nama_desa'] = $row->desa->nama_desa; 
            
            foreach ($this->processColumns as $col) {
                $val = $row->$col;
                $min = $minMax[$col]['min'];
                $max = $minMax[$col]['max'];
                $normRow[$col] = ($max - $min) == 0 ? 0 : ($val - $min) / ($max - $min);
            }
            $normalized[] = $normRow;
        }
        return $normalized;
    }

    private function euclideanDistance($pointA, $pointB)
    {
        $sum = 0;
        foreach ($this->processColumns as $col) {
            $sum += pow($pointA[$col] - $pointB[$col], 2);
        }
        return sqrt($sum);
    }

    private function calculateMean($normalizedData, $members)
    {
        $mean = [];
        foreach ($this->processColumns as $col) {
            $sum = 0;
            foreach ($members as $index) {
                $sum += $normalizedData[$index][$col];
            }
            $mean[$col] = $sum / count($members);
        }
        return $mean;
    }

    public function getCalculationLog($tahun)
    {
        $data = Indikator::with('desa')->where('tahun_data', $tahun)->get();
        if ($data->count() < 3) return false;

        $log = [];
        $log['raw_data'] = $data;

        // 1. Min/Max untuk referensi Rumus
        $log['min_max_raw'] = [];
        foreach ($this->rawColumns as $col) {
            // Gunakan Arrow Function agar VS Code tidak protes
            $log['min_max_raw'][$col] = [
                'min' => $data->min(fn($item) => $item->{$col}), 
                'max' => $data->max(fn($item) => $item->{$col})
            ];
        }

        // 2. Pre-processing & Invers
        $maxSma = $data->max(fn($item) => $item->akses_sma);
        $maxPuskesmas = $data->max(fn($item) => $item->akses_puskesmas);
        
        foreach ($data as $row) {
            $row->skor_akses_sma = $maxSma - $row->akses_sma;
            $row->skor_akses_puskesmas = $maxPuskesmas - $row->akses_puskesmas;
            $row->skor_kualitas_sinyal = 5 - $row->kualitas_sinyal;
        }

        // Simpan Min/Max sesudah Invers
        $log['min_max_processed'] = [];
        foreach ($this->processColumns as $col) {
            $log['min_max_processed'][$col] = [
                'min' => $data->min(fn($item) => $item->{$col}), 
                'max' => $data->max(fn($item) => $item->{$col})
            ];
        }

        // 3. Normalisasi
        $normalizedData = $this->normalize($data);
        $log['normalized'] = $normalizedData;

        // 4. Inisialisasi Centroid
        $sortedForInit = $normalizedData;
        usort($sortedForInit, function($a, $b) {
            return (array_sum($b) / count($b)) <=> (array_sum($a) / count($a));
        });
        
        $countData = count($sortedForInit);
        $centroids = [
            $sortedForInit[0],
            $sortedForInit[(int) floor($countData / 2)],
            $sortedForInit[$countData - 1]
        ];
        
        // PASTIKAN BARIS INI ADA:
        $log['initial_centroids'] = $centroids;

        // 5. Jalankan Iterasi
        $clusters = [];
        $isConverged = false;
        $iteration = 0;
        $maxIterations = 100;
        
        $log['all_iterations'] = []; // Array untuk menyimpan sejarah setiap iterasi!

        while (!$isConverged && $iteration < $maxIterations) {
            $newClusters = [[], [], []];
            $distances_log = [];

            // Hitung Jarak
            foreach ($normalizedData as $index => $item) {
                $distances = [
                    $this->euclideanDistance($item, $centroids[0]),
                    $this->euclideanDistance($item, $centroids[1]),
                    $this->euclideanDistance($item, $centroids[2]),
                ];
                $closestIndex = array_search(min($distances), $distances);
                $newClusters[$closestIndex][] = $index;
                $distances_log[$index] = $distances; 
            }

            // Simpan sejarah iterasi SAAT INI ke dalam Log sebelum centroid bergeser
            $log['all_iterations'][$iteration] = [
                'iteration_number' => $iteration + 1,
                'centroids_used' => $centroids,
                'distances' => $distances_log,
                'clusters_formed' => $newClusters
            ];

            // Cari Centroid Baru
            $newCentroids = [];
            foreach ($newClusters as $cIndex => $members) {
                if (count($members) > 0) {
                    $newCentroids[$cIndex] = $this->calculateMean($normalizedData, $members);
                } else {
                    $newCentroids[$cIndex] = $centroids[$cIndex];
                }
            }

            if ($centroids === $newCentroids) {
                $isConverged = true;
            }

            $centroids = $newCentroids;
            $iteration++;
        }

        // 6. Tentukan Ranking
        $centroidScores = [];
        foreach ($centroids as $cIndex => $centroid) {
            $centroidScores[$cIndex] = array_sum($centroid) / count($this->processColumns);
        }
        arsort($centroidScores);
        $clusterLabels = [];
        $rank = 1;
        foreach ($centroidScores as $cIndex => $score) {
            $clusterLabels[$cIndex] = $rank;
            $rank++;
        }

        // Simpan data untuk View
        $log['total_iterations'] = $iteration;
        $log['final_distances'] = $distances_log; // Ini jarak iterasi konvergen
        $log['final_centroids'] = $centroids;
        $log['cluster_labels'] = $clusterLabels; // Peta konversi index ke nama klaster

        return $log;
    }
}
