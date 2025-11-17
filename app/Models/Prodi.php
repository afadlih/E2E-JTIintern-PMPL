<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prodi extends Model
{
    use HasFactory;

    protected $table = 'm_prodi';
    protected $primaryKey = 'kode_prodi';
    public $incrementing = false; // Jika kode_prodi bukan auto-increment
    protected $keyType = 'string';

    protected $fillable = [
        'kode_prodi',
        'nama_prodi',
    ];

    /**
     * Relasi dengan mahasiswa
     */
    public function mahasiswa()
    {
        return $this->hasMany(Mahasiswa::class, 'program_studi_id', 'kode_prodi');
    }

    /**
     * Relasi dengan dosen
     */
    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'program_studi_id', 'kode_prodi');
    }
}
