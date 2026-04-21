<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    protected $fillable = ['nama_kecamatan', 'file_geojson', 'skor_agregasi', 'status_akhir', 'status_validasi'];

    public function desas()
    {
        return $this->hasMany(Desa::class);
    }

    // Fitur pro: Langsung ambil semua data indikator yang ada di kecamatan ini
    public function indikators()
    {
        return $this->hasManyThrough(Indikator::class, Desa::class);
    }
}
