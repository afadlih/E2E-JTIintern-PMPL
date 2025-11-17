<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minat extends Model
{
    protected $table = 'm_minat';
    protected $primaryKey = 'minat_id';
    protected $fillable = ['nama_minat', 'deskripsi'];
    
    public function mahasiswa()
    {
        return $this->belongsToMany(Mahasiswa::class, 't_minat_mahasiswa', 'minat_id', 'mahasiswa_id');
    }
    
    public function dosen()
    {
        return $this->belongsToMany(Dosen::class, 't_minat_dosen', 'minat_id', 'dosen_id');
    }
}