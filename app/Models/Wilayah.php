<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    use HasFactory;

    protected $table = 'm_wilayah';
    protected $primaryKey = 'wilayah_id';
    public $timestamps = false;

    protected $fillable = [
        'nama_kota',
        'provinsi'
    ];

    public function dosen()
    {
        return $this->hasMany(Dosen::class, 'wilayah_id', 'wilayah_id');
    }

    public function perusahaan()
    {
        return $this->hasMany(Perusahaan::class, 'wilayah_id', 'wilayah_id');
    }
}