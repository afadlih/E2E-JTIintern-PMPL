<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Skill;
use App\Models\Perusahaan;
use App\Models\Periode;
use App\Models\Jenis;

class Lowongan extends Model
{
    use HasFactory;

    protected $table = 'm_lowongan'; // Nama tabel
    protected $primaryKey = 'id_lowongan'; // Kolom ID utama
    public $timestamps = true; // Jika tabel memiliki kolom created_at dan updated_at

    protected $fillable = [
        'judul_lowongan',
        'perusahaan_id',
        'periode_id',
        'jenis_id',
        'kapasitas',
        'min_ipk',
        'deskripsi',

    ];

    // ✅ TAMBAHKAN: Cast min_ipk sebagai decimal
    protected $casts = [
        'min_ipk' => 'decimal:2',
        'kapasitas' => 'integer',
    ];

    // Relasi ke model Perusahaan
    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'perusahaan_id');
    }

    // Relasi ke model Periode
    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id', 'periode_id');
    }

    // Relasi ke model Jenis
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'jenis_id', 'jenis_id');
    }

    // Add this missing skills relationship
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 't_skill_lowongan', 'id_lowongan', 'id_skill')
            ->withoutTimestamps(); // Since t_skill_lowongan doesn't have timestamps
    }

    public function kapasitas()
    {
        return $this->hasOne(KapasitasLowongan::class, 'id_lowongan', 'id_lowongan');
    }
}
