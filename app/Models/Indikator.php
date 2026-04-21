<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    protected $fillable = [
        'desa_id', 'tahun_data', 'listrik_pln', 'fasilitas_ekonomi', 
        'fasilitas_pendidikan', 'akses_sma', 'faskes_desa', 
        'akses_puskesmas', 'kualitas_sinyal', 'keamanan_bencana', 'klaster_hasil'
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }
}
