<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    protected $fillable = ['kecamatan_id', 'nama_desa'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function indikators()
    {
        return $this->hasMany(Indikator::class);
    }
}
