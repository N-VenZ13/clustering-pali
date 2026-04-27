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
            $minMax[$col] = ['min' => $data->min($col), 'max' => $data->max($col)];
        }

        $normalized = [];
        foreach ($data as $row) {
            $normRow = [];
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
}
