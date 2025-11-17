<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KapasitasLowongan extends Model
{
    protected $table = 't_kapasitas_lowongan';
    protected $primaryKey = 'id_kapasitas';
    protected $fillable = ['id_lowongan', 'kapasitas_tersedia', 'kapasitas_total'];

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'id_lowongan', 'id_lowongan');
    }
}
