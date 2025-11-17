<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periode extends Model
{
    use HasFactory;

    protected $table = 'm_periode'; // Nama tabel
    protected $primaryKey = 'periode_id'; // Kolom ID utama
    public $timestamps = true; // Jika tabel memiliki kolom created_at dan updated_at

    protected $fillable = [
        'waktu', // Kolom yang dapat diisi
        'tgl_mulai', // Tanggal mulai periode]
        'tgl_selesai', // Tanggal selesai periode
        'created_at', // Tanggal dibuat
        'updated_at', // Tanggal diperbarui
    ];

    // Relasi ke model Lowongan (jika ada)
    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'periode_id', 'periode_id');
    }
}