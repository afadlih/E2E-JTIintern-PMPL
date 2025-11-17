<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'm_kelas';
    protected $primaryKey = 'id_kelas';

    protected $fillable = [
        'nama_kelas',
        'kode_prodi',
        'tahun_masuk'
    ];

    /**
     * Get the program studi that owns the kelas.
     */
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'kode_prodi', 'kode_prodi');
    }

    /**
     * Get the mahasiswa for the kelas.
     */
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'id_kelas', 'id_kelas');
    }

    /**
     * Get the full name of the kelas including prodi and year.
     */
    public function getFullNameAttribute()
    {
        return "{$this->nama_kelas} ({$this->tahun_masuk})";
    }
}