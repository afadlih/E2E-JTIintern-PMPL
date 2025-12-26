<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    use HasFactory;

    protected $table = 't_lamaran';

    protected $primaryKey = 'id_lamaran';

    protected $fillable = [
        'id_mahasiswa',
        'id_lowongan',
        'id_dosen',
        'status',
        'auth',
        'catatan',
        'tanggal_lamaran'
    ];

    protected $casts = [
        'tanggal_lamaran' => 'date'
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    public function magang()
    {
        return $this->belongsTo(Magang::class, 'id_magang', 'id_magang');
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'id_lowongan', 'id_lowongan')->withDefault();
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'dosen_id');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'perusahaan_id');
    }
}
