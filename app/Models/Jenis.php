<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    use HasFactory;

    protected $table = 'm_jenis'; // Nama tabel
    protected $primaryKey = 'jenis_id'; // Primary key
    protected $fillable = ['nama_jenis']; // Kolom yang dapat diisi

    // Relasi ke model Lowongan
    public function lowongan()
    {
        return $this->hasMany(Lowongan::class, 'jenis_id', 'jenis_id');
    }
}