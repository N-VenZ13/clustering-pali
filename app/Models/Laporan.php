<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $fillable = ['tahun', 'status', 'dikunci_pada'];
}
