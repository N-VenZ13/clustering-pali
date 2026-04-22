<?php

namespace App\Services;

use App\Models\Indikator;

class KMeansService
{
    // 8 Variabel kita
    protected $columns = [
        'listrik_pln',
        'fasilitas_ekonomi',
        'fasilitas_pendidikan',
        'akses_sma',
        'faskes_desa',
        'akses_puskesmas',
        'kualitas_sinyal',
        'keamanan_bencana'
    ];

    public function process($tahun)
    {
        $data = Indikator::where('tahun_data', $tahun)->get();

        // K-Means butuh minimal 3 data untuk K=3
        if ($data->count() < 3) {
            return false;
        }

        // 1. Normalisasi Min-Max (0-1)
        $normalizedData = $this->normalize($data);

        // 2. Inisialisasi 3 Centroid Awal (Random dari data)
        $centroids = [
            $normalizedData[array_rand($normalizedData)],
            $normalizedData[array_rand($normalizedData)],
            $normalizedData[array_rand($normalizedData)]
        ];

        $clusters = [];
        $isConverged = false;
        $maxIterations = 100;
        $iteration = 0;

        // 3. Iterasi K-Means
        while (!$isConverged && $iteration < $maxIterations) {
            $newClusters = [[], [], []]; // Index 0, 1, 2

            // Hitung Jarak Euclidean & Masukkan ke klaster terdekat
            foreach ($normalizedData as $index => $item) {
                $distances = [];
                foreach ($centroids as $cIndex => $centroid) {
                    $distances[$cIndex] = $this->euclideanDistance($item, $centroid);
                }

                // Cari jarak terpendek
                $closestCentroidIndex = array_search(min($distances), $distances);
                $newClusters[$closestCentroidIndex][] = $index;
            }

            // Hitung Centroid Baru (Rata-rata dari anggota klasternya)
            $newCentroids = [];
            foreach ($newClusters as $cIndex => $clusterMembers) {
                if (count($clusterMembers) > 0) {
                    $newCentroids[$cIndex] = $this->calculateMean($normalizedData, $clusterMembers);
                } else {
                    $newCentroids[$cIndex] = $centroids[$cIndex]; // Tetap jika kosong
                }
            }

            // Cek Konvergensi (Apakah centroid berubah?)
            if ($centroids === $newCentroids) {
                $isConverged = true;
            }

            $centroids = $newCentroids;
            $clusters = $newClusters;
            $iteration++;
        }

        // 4. PENENTUAN LABEL (Sejahtera / Berkembang / Perlu Perhatian)
        // Hitung rata-rata nilai tiap centroid untuk di-ranking
        $centroidScores = [];
        foreach ($centroids as $cIndex => $centroid) {
            $centroidScores[$cIndex] = array_sum($centroid) / count($this->columns);
        }

        // Sortir Descending (Nilai tertinggi = Sejahtera)
        arsort($centroidScores);

        $clusterLabels = [];
        $rank = 1; // 1: Sejahtera, 2: Berkembang, 3: Perhatian
        foreach ($centroidScores as $cIndex => $score) {
            $clusterLabels[$cIndex] = $rank;
            $rank++;
        }

        // 5. Simpan Hasil ke Database
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
        // Cari nilai Min dan Max tiap kolom
        foreach ($this->columns as $col) {
            $minMax[$col] = ['min' => $data->min($col), 'max' => $data->max($col)];
        }

        $normalized = [];
        foreach ($data as $row) {
            $normRow = [];
            foreach ($this->columns as $col) {
                $val = $row->$col;
                $min = $minMax[$col]['min'];
                $max = $minMax[$col]['max'];
                // Rumus Min-Max
                $normRow[$col] = ($max - $min) == 0 ? 0 : ($val - $min) / ($max - $min);
            }
            $normalized[] = $normRow;
        }
        return $normalized;
    }

    private function euclideanDistance($pointA, $pointB)
    {
        $sum = 0;
        foreach ($this->columns as $col) {
            $sum += pow($pointA[$col] - $pointB[$col], 2);
        }
        return sqrt($sum);
    }

    private function calculateMean($normalizedData, $members)
    {
        $mean = [];
        foreach ($this->columns as $col) {
            $sum = 0;
            foreach ($members as $index) {
                $sum += $normalizedData[$index][$col];
            }
            $mean[$col] = $sum / count($members);
        }
        return $mean;
    }
}
